/*
 * WBACFSPWI — Test 10: GSM Module (SIM800L / SIM900) SMS Irrigation Alert Test Suite
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 * 
 * Target Application: Miniature Rice Field Automation
 * Module Under Test: SIM800L / SIM900 GSM/GPRS Cellular Module
 * 
 * Description:
 *   Sends automated SMS text message alerts directly to the Admin's mobile phone:
 *     1. When irrigation is STARTED (water level drops below 45.0% threshold, pump turns ON).
 *     2. When irrigation is STOPPED (water level reaches >= 50.0% threshold, pump turns OFF).
 *     3. When irrigation is RESTARTED (water level drops below 45.0% again, pump turns ON).
 * 
 * Hardware Pinout:
 *   - Arduino Pin D2: SoftwareSerial RX (Connects to GSM 5VT / TXD)
 *   - Arduino Pin D3: SoftwareSerial TX (Direct wire to SIM900A 5VR pin; or via 1kΩ/2kΩ divider for raw 3.3V SIM800L RXD)
 *   - Arduino Pin A1: HW-080 Surface Water Level Sensor (Calibrated 3-point piecewise curve)
 *   - Arduino Pin D7: 5V Relay Module (Active LOW, switches 12V DC Water Pump)
 *   - Arduino Pin D8: Capacitive Sensor Power Gate (Corrosion prevention)
 *   - Arduino Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone)
 *   - Arduino Pin D13: Status / Fault LED (Blinks during GSM operations, ON when pump is active)
 * 
 * GSM Module Power Note (CRITICAL):
 *   SIM900A / SIM800L can draw up to 2.0A peak current during cellular transmission bursts.
 *   DO NOT power from Arduino 5V or 3.3V pins. Power via LM2596 Buck Converter (5.0V for SIM900A 5V pin,
 *   or 4.0V for raw VBAT) with a 1000uF low-ESR electrolytic capacitor across power and shared Common GND.
 */

#include <SoftwareSerial.h>

// ============================================================================
// 1. PIN DEFINITIONS & HARDWARE CONSTANTS
// ============================================================================
const int PIN_GSM_RX         = 2;      // Arduino RX <- GSM 5VT / TXD
const int PIN_GSM_TX         = 3;      // Arduino TX -> GSM 5VR (direct for SIM900A; 1k/2k divider for SIM800L)
const int PIN_RELAY_PUMP     = 7;      // 5V Relay Control (DC Water Pump)
const int PIN_SENSOR_PWR     = 8;      // Capacitive Sensor Power Gate
const int PIN_ROOT_SOIL      = A0;     // Capacitive Soil Moisture Sensor v1.2
const int PIN_SURFACE_WATER  = A1;     // HW-080 Surface Water Ponding Sensor
const int PIN_STATUS_LED     = 13;     // Built-in Status LED

const bool RELAY_ACTIVE_LOW  = true;   // Standard 5V relay modules trigger on LOW
const bool USE_SENSOR_PWR    = true;   // Enable power gating to prevent probe corrosion

SoftwareSerial gsmSerial(PIN_GSM_RX, PIN_GSM_TX);

// ============================================================================
// 2. ADMIN RECIPIENT PHONE NUMBER & SYSTEM CONFIGURATION
// ============================================================================
// IMPORTANT: Set your admin mobile phone number here (include country code or local format)
// Examples: "+639123456789" (Philippines), "+1234567890" (US), or "09123456789"
char ADMIN_PHONE[20] = "+639169751409";

// ============================================================================
// 3. CALIBRATION & THRESHOLD VALUES (SYNCHRONIZED WITH SYSTEM MEMORY)
// ============================================================================
// Capacitive Root Sensor (Air vs Water raw ADC)
const int SOIL_AIR_RAW       = 417;    // 0% moisture in dry air
const int SOIL_WATER_RAW     = 153;    // 100% moisture in water

// HW-080 Moisture Sensor (Physical Ruler 3-Point Calibration for Surface Ponding Depth)
const int HW080_RAW_DRY      = 1020;   // Stage 0: Probe in dry air (0.0% surface water)
const int HW080_RAW_MID      = 410;    // Stage 1: Water at middle of sensor 7-8cm mark (50.0% depth)
const int HW080_RAW_WET      = 355;    // Stage 2: Probe at container maximum depth (100% full ponding)

// Irrigation Decision Thresholds (Surface Water Level Control with 5% Hysteresis)
const float WATER_TARGET_MAX   = 50.0; // Automatically stop pump when surface water level reaches >= 50.0%
const float WATER_REFILL_MIN   = 45.0; // Automatically start pump only when surface water level drops < 45.0%

// Timing Protections (in milliseconds)
const unsigned long MIN_PUMP_RUN_MS   = 5000UL;   // 5s minimum runtime (prevents momentary splash cutoffs)
const unsigned long MAX_PUMP_RUN_MS   = 180000UL; // 3 minutes maximum continuous runtime
const unsigned long PUMP_COOLDOWN_MS  = 60000UL;  // 1 minute cooldown after continuous run cap
const unsigned long SETTLING_DELAY_MS = 10000UL;  // 10s water settling window after pump stop
const unsigned long SAMPLE_INTERVAL   = 1500UL;   // Read sensors & evaluate logic every 1.5s
const unsigned long SMS_COOLDOWN_MS   = 15000UL;  // 15s minimum spacing between SMS sends to prevent carrier spam

// ============================================================================
// 4. SYSTEM STATE VARIABLES
// ============================================================================
bool  pumpState          = false;
bool  isSettling         = false;
bool  gsmReady           = false;
int   cycleCount         = 0;          // Tracks number of completed irrigation cycles

unsigned long pumpStartTime     = 0;
unsigned long pumpStopTime      = 0;
unsigned long settlingStartTime = 0;
unsigned long lastSampleTime    = 0;
unsigned long lastSmsTime       = 0;

float currentSurfaceWater = 0.0;
float currentRootMoisture = 0.0;

// Track last triggered event state to avoid duplicate SMS
enum IrrigationEventType {
  EVENT_NONE,
  EVENT_STARTED,
  EVENT_STOPPED,
  EVENT_RESTARTED
};
IrrigationEventType lastTriggeredEvent = EVENT_NONE;

// ============================================================================
// 5. GSM HELPER FUNCTIONS
// ============================================================================

// Send an AT command and check for expected response with timeout
bool sendATCommand(const char* cmd, const char* expectedResponse, unsigned long timeoutMs) {
  while (gsmSerial.available()) gsmSerial.read(); // Clear RX buffer

  Serial.print(F("[GSM TX] "));
  Serial.println(cmd);
  gsmSerial.println(cmd);

  String response = "";
  unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (gsmSerial.available()) {
      char c = gsmSerial.read();
      response += c;
    }
    if (response.indexOf(expectedResponse) != -1) {
      Serial.print(F("[GSM RX OK] "));
      Serial.println(response);
      return true;
    }
  }

  Serial.print(F("[GSM RX TIMEOUT/FAIL] "));
  Serial.println(response);
  return false;
}

// Read and dump GSM serial responses to Serial Monitor (for manual debugging)
void dumpGSMResponse(unsigned long waitMs) {
  unsigned long start = millis();
  while (millis() - start < waitMs) {
    while (gsmSerial.available()) {
      char c = gsmSerial.read();
      Serial.write(c);
    }
  }
}

// Initialize GSM module and verify network registration with multi-baud detection
bool initGSM() {
  Serial.println(F("\n--- Initializing GSM Module (SIM800L / SIM900A) ---"));

  const long candidateBauds[] = {9600, 19200, 115200, 38400, 57600};
  const int numBauds = sizeof(candidateBauds) / sizeof(candidateBauds[0]);
  bool synced = false;
  long activeBaud = 9600;

  Serial.println(F("[INFO] Auto-detecting GSM baud rate..."));
  for (int b = 0; b < numBauds; b++) {
    long testBaud = candidateBauds[b];
    Serial.print(F("[INFO] Testing baud: "));
    Serial.println(testBaud);
    gsmSerial.begin(testBaud);
    delay(200);

    for (int i = 0; i < 3; i++) {
      while (gsmSerial.available()) gsmSerial.read(); // Clear RX buffer
      gsmSerial.println(F("AT"));

      unsigned long start = millis();
      String resp = "";
      while (millis() - start < 800) {
        while (gsmSerial.available()) {
          resp += (char)gsmSerial.read();
        }
        if (resp.indexOf(F("OK")) != -1) {
          synced = true;
          activeBaud = testBaud;
          break;
        }
      }
      if (synced) break;
      delay(200);
    }
    if (synced) {
      Serial.print(F("[GSM DETECTED] Connected successfully at "));
      Serial.print(activeBaud);
      Serial.println(F(" baud!"));
      break;
    }
  }

  if (!synced) {
    Serial.println(F("\n[ERROR] GSM Module not responding to AT commands."));
    Serial.println(F("  Check the following 3 wiring points:"));
    Serial.println(F("  1. Swap TX and RX: Arduino D2 connects to GSM 5VT; Arduino D3 connects to GSM 5VR."));
    Serial.println(F("  2. Direct 5VR connection: If using SIM900A '5VR' pin, wire D3 directly (remove 1k/2k divider)."));
    Serial.println(F("  3. Common GND: Ensure Arduino GND and SIM900A GND share the same ground bus."));
    return false;
  }

  // If detected at a baud rate other than 9600, lock it to 9600 for SoftwareSerial stability
  if (activeBaud != 9600) {
    Serial.println(F("[INFO] Locking GSM module to reliable 9600 baud (AT+IPR=9600)..."));
    gsmSerial.println(F("AT+IPR=9600"));
    delay(400);
    gsmSerial.begin(9600);
    delay(400);
    sendATCommand("AT&W", "OK", 1000); // Save to non-volatile profile
  }

  sendATCommand("ATE0", "OK", 1000);        // Echo OFF
  sendATCommand("AT+CPIN?", "READY", 3000); // Check SIM status
  sendATCommand("AT+CMGF=1", "OK", 1000);   // Set SMS to Text Mode
  sendATCommand("AT+CSCS=\"GSM\"", "OK", 1000); // Set GSM character set

  // Check signal quality
  Serial.println(F("[INFO] Checking signal quality (AT+CSQ)..."));
  gsmSerial.println(F("AT+CSQ"));
  dumpGSMResponse(1500);

  // Check network registration (1 = Registered Home, 5 = Registered Roaming)
  Serial.println(F("[INFO] Checking network registration (AT+CREG?)..."));
  gsmSerial.println(F("AT+CREG?"));
  dumpGSMResponse(2000);

  Serial.println(F("--- GSM Module Ready & Configured ---\n"));
  return true;
}

// Send an SMS to the Admin phone number
bool sendSMS(const char* phoneNumber, const String& message) {
  if (millis() - lastSmsTime < SMS_COOLDOWN_MS) {
    Serial.println(F("[GSM RATE LIMIT] Skipping SMS to protect against carrier throttling."));
    return false;
  }

  Serial.println(F("\n=================================================="));
  Serial.print(F("[SMS DISPATCH] Recipient: "));
  Serial.println(phoneNumber);
  Serial.print(F("[SMS CONTENT]  "));
  Serial.println(message);
  Serial.println(F("=================================================="));

  // Visual LED alert
  for (int i = 0; i < 3; i++) {
    digitalWrite(PIN_STATUS_LED, HIGH);
    delay(80);
    digitalWrite(PIN_STATUS_LED, LOW);
    delay(80);
  }

  // Set SMS Text Mode
  gsmSerial.println(F("AT+CMGF=1"));
  delay(300);

  // Prepare SMS recipient
  gsmSerial.print(F("AT+CMGS=\""));
  gsmSerial.print(phoneNumber);
  gsmSerial.println(F("\""));
  delay(500);

  // Send message body
  gsmSerial.print(message);
  delay(300);

  // Send Ctrl+Z (ASCII 26) to commit and send SMS
  gsmSerial.write(26);

  // Wait for confirmation response from GSM module
  String response = "";
  unsigned long start = millis();
  bool success = false;
  while (millis() - start < 15000UL) { // SMS send may take up to 15s
    while (gsmSerial.available()) {
      char c = gsmSerial.read();
      response += c;
    }
    if (response.indexOf(F("+CMGS:")) != -1 || response.indexOf(F("OK")) != -1) {
      success = true;
      break;
    }
    if (response.indexOf(F("ERROR")) != -1) {
      success = false;
      break;
    }
  }

  if (success) {
    Serial.println(F("[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<"));
    lastSmsTime = millis();
  } else {
    Serial.print(F("[SMS STATUS] >>> SMS FAILED TO SEND. GSM Response: "));
    Serial.println(response);
  }

  return success;
}

// ============================================================================
// 6. SENSOR READING FUNCTIONS (SYNCHRONIZED CALIBRATION)
// ============================================================================

float readSurfaceWater() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SURFACE_WATER);
    delay(2);
  }
  int raw = sum / 16;

  if (raw >= HW080_RAW_DRY) {
    return 0.0;
  } else if (raw >= HW080_RAW_MID) {
    // Stage 1: Dry air (1020) down to Middle height (410) -> 0.0% to 50.0%
    float pct = 50.0 * (float)(HW080_RAW_DRY - raw) / (float)(HW080_RAW_DRY - HW080_RAW_MID);
    return constrain(pct, 0.0, 50.0);
  } else {
    // Stage 2: Middle height (410) down to Full top (355) -> 50.0% to 100.0%
    float pct = 50.0 + 50.0 * (float)(HW080_RAW_MID - raw) / (float)(HW080_RAW_MID - HW080_RAW_WET);
    return constrain(pct, 50.0, 100.0);
  }
}

float readRootMoisture() {
  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    digitalWrite(PIN_SENSOR_PWR, HIGH);
    delay(80); // Power stabilization
  }

  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_ROOT_SOIL);
    delay(2);
  }

  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    digitalWrite(PIN_SENSOR_PWR, LOW);
  }

  int raw = sum / 16;
  float pct = 100.0 * (float)(SOIL_AIR_RAW - raw) / (float)(SOIL_AIR_RAW - SOIL_WATER_RAW);
  return constrain(pct, 0.0, 100.0);
}

// ============================================================================
// 7. PUMP CONTROL & STATE DISPATCH
// ============================================================================

void setPump(bool enable) {
  digitalWrite(PIN_RELAY_PUMP, enable ? (RELAY_ACTIVE_LOW ? LOW : HIGH) : (RELAY_ACTIVE_LOW ? HIGH : LOW));
  digitalWrite(PIN_STATUS_LED, enable ? HIGH : LOW);

  if (enable == pumpState) return;

  pumpState = enable;
  if (pumpState) {
    pumpStartTime = millis();
    Serial.println(F("[ACTUATOR] Relay ENGAGED -> 12V DC Water Pump ON"));
  } else {
    pumpStopTime = millis();
    Serial.println(F("[ACTUATOR] Relay RELEASED -> 12V DC Water Pump OFF"));
  }
}

// ============================================================================
// 8. IRRIGATION DECISION & SMS TRIGGER LOGIC
// ============================================================================

void processIrrigationLogic() {
  currentSurfaceWater = readSurfaceWater();
  currentRootMoisture = readRootMoisture();

  // Print live telemetry to Serial Monitor
  Serial.print(F("[MONITOR] Surface Water: "));
  Serial.print(currentSurfaceWater, 1);
  Serial.print(F("% | Root Moisture: "));
  Serial.print(currentRootMoisture, 1);
  Serial.print(F("% | Pump: "));
  Serial.print(pumpState ? F("ON (RUNNING)") : F("OFF"));
  Serial.print(F(" | Cycle: #"));
  Serial.println(cycleCount);

  unsigned long now = millis();

  // --------------------------------------------------------------------------
  // LAYER 1 & 3: SETTLING WINDOW MANAGEMENT
  // --------------------------------------------------------------------------
  if (isSettling) {
    if (now - settlingStartTime < SETTLING_DELAY_MS) {
      Serial.println(F("  [STATUS] Wave settling in progress... Waiting for water surface to stabilize."));
      return;
    } else {
      isSettling = false;
      Serial.println(F("  [STATUS] Wave settling complete. Resuming normal threshold monitoring."));
    }
  }

  // --------------------------------------------------------------------------
  // DECISION A: PUMP IS CURRENTLY RUNNING -> CHECK FOR STOP THRESHOLD (>= 50%)
  // --------------------------------------------------------------------------
  if (pumpState) {
    unsigned long runtime = now - pumpStartTime;

    // Safety Interlock: Anti-splash minimum runtime
    if (runtime < MIN_PUMP_RUN_MS) {
      Serial.print(F("  [SAFETY] Anti-splash lock active ("));
      Serial.print((MIN_PUMP_RUN_MS - runtime) / 1000);
      Serial.println(F("s remaining). Pump cannot be stopped yet."));
      return;
    }

    // Safety Interlock: Maximum runtime cap
    if (runtime >= MAX_PUMP_RUN_MS) {
      Serial.println(F("  [SAFETY ALERT] Continuous runtime cap exceeded (180s)! Emergency stop."));
      setPump(false);
      isSettling = true;
      settlingStartTime = now;
      
      String alertMsg = F("[WBACFSPWI ALERT] PUMP TIMEOUT: Continuous runtime cap reached (3 mins). Pump STOPPED for safety cooldown.");
      sendSMS(ADMIN_PHONE, alertMsg);
      return;
    }

    // TARGET REACHED: Surface water reached or exceeded 50.0% target
    if (currentSurfaceWater >= WATER_TARGET_MAX) {
      Serial.println(F("  [TRIGGER] Target threshold REACHED (>= 50.0%)! Stopping irrigation."));
      setPump(false);
      isSettling = true;
      settlingStartTime = now;

      // SMS TRIGGER 2: Irrigation STOPPED upon reaching target threshold
      if (lastTriggeredEvent != EVENT_STOPPED) {
        String msg = F("[WBACFSPWI ALERT] Irrigation STOPPED. Target water level reached ");
        msg += String(currentSurfaceWater, 1);
        msg += F("% (at/above 50.0% threshold). Pump is now OFF.");
        sendSMS(ADMIN_PHONE, msg);
        lastTriggeredEvent = EVENT_STOPPED;
      }
    }
    return;
  }

  // --------------------------------------------------------------------------
  // DECISION B: PUMP IS CURRENTLY OFF -> CHECK FOR START / REFILL THRESHOLD (< 45%)
  // --------------------------------------------------------------------------
  if (!pumpState) {
    // Water level dropped below 45.0% refill minimum
    if (currentSurfaceWater < WATER_REFILL_MIN) {
      
      // TRIGGER 1: First-time Irrigation Started
      if (cycleCount == 0) {
        Serial.println(F("  [TRIGGER] Surface water dropped below 45.0%! STARTING irrigation (Cycle #1)."));
        setPump(true);
        cycleCount = 1;

        if (lastTriggeredEvent != EVENT_STARTED) {
          String msg = F("[WBACFSPWI ALERT] Irrigation STARTED. Water level dropped to ");
          msg += String(currentSurfaceWater, 1);
          msg += F("% (below 45.0% threshold). Pump is now ON.");
          sendSMS(ADMIN_PHONE, msg);
          lastTriggeredEvent = EVENT_STARTED;
        }
      }
      // TRIGGER 3: Water went below threshold AGAIN -> Irrigation RESTARTED
      else if (lastTriggeredEvent == EVENT_STOPPED) {
        cycleCount++;
        Serial.print(F("  [TRIGGER] Surface water dropped below 45.0% AGAIN! RESTARTING irrigation (Cycle #"));
        Serial.print(cycleCount);
        Serial.println(F(")."));
        setPump(true);

        String msg = F("[WBACFSPWI ALERT] Irrigation RESTARTED (Cycle #");
        msg += String(cycleCount);
        msg += F("). Water dropped to ");
        msg += String(currentSurfaceWater, 1);
        msg += F("% (< 45.0%). Pump is refilling the field.");
        sendSMS(ADMIN_PHONE, msg);
        lastTriggeredEvent = EVENT_RESTARTED;
      }
    }
  }
}

// ============================================================================
// 9. SERIAL COMMAND INTERPRETER (INTERACTIVE BENCH TEST CONSOLE)
// ============================================================================

void printHelpMenu() {
  Serial.println(F("\n======================================================="));
  Serial.println(F(" WBACFSPWI Test 10: GSM SMS Alert Interactive Console  "));
  Serial.println(F("======================================================="));
  Serial.print(F(" Admin Phone Number: "));
  Serial.println(ADMIN_PHONE);
  Serial.println(F(" Available Test Commands:"));
  Serial.println(F("   's' -> Send an instant test SMS to the Admin phone"));
  Serial.println(F("   'c' -> Check GSM signal strength & network registration"));
  Serial.println(F("   'p' -> Toggle 12V DC Pump Relay ON/OFF"));
  Serial.println(F("   'w' -> Print live water level & soil moisture readings"));
  Serial.println(F("   '1' -> Simulate TRIGGER 1: Irrigation Started SMS (< 45%)"));
  Serial.println(F("   '2' -> Simulate TRIGGER 2: Irrigation Stopped SMS (>= 50%)"));
  Serial.println(F("   '3' -> Simulate TRIGGER 3: Irrigation Restarted SMS (< 45% again)"));
  Serial.println(F("   'n' -> Change Admin recipient phone number"));
  Serial.println(F("   'h' -> Display this help menu"));
  Serial.println(F("=======================================================\n"));
}

void handleSerialCommands() {
  if (!Serial.available()) return;

  char cmd = Serial.read();

  switch (cmd) {
    case 'h':
    case 'H':
      printHelpMenu();
      break;

    case 's':
    case 'S': {
      Serial.println(F("\n[TEST] Sending immediate verification SMS to Admin..."));
      String testMsg = F("[WBACFSPWI TEST] GSM Module SIM800L communication link verified. Ready to report automated irrigation events.");
      sendSMS(ADMIN_PHONE, testMsg);
      break;
    }

    case 'c':
    case 'C': {
      Serial.println(F("\n[GSM DIAGNOSTICS] Querying Module Status..."));
      gsmSerial.println(F("AT+CSQ"));
      dumpGSMResponse(1500);
      gsmSerial.println(F("AT+CREG?"));
      dumpGSMResponse(1500);
      gsmSerial.println(F("AT+CBC")); // Battery/voltage status of GSM module
      dumpGSMResponse(1500);
      break;
    }

    case 'p':
    case 'P':
      Serial.print(F("[TEST] Toggling pump manually. Current: "));
      Serial.println(pumpState ? F("ON -> turning OFF") : F("OFF -> turning ON"));
      setPump(!pumpState);
      break;

    case 'w':
    case 'W':
      Serial.println(F("\n[INSTANT READING]"));
      Serial.print(F("Surface Water Level: "));
      Serial.print(readSurfaceWater(), 1);
      Serial.println(F("%"));
      Serial.print(F("Root Soil Moisture:  "));
      Serial.print(readRootMoisture(), 1);
      Serial.println(F("%"));
      break;

    case '1': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 1: Water dropped to 42.0% (< 45.0%) -> Irrigation Started"));
      cycleCount = 1;
      setPump(true);
      String msg = F("[WBACFSPWI ALERT] Irrigation STARTED. Water level dropped to 42.0% (below 45.0% threshold). Pump is now ON.");
      sendSMS(ADMIN_PHONE, msg);
      lastTriggeredEvent = EVENT_STARTED;
      break;
    }

    case '2': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 2: Water reached 50.4% (>= 50.0%) -> Irrigation Stopped"));
      setPump(false);
      isSettling = true;
      settlingStartTime = millis();
      String msg = F("[WBACFSPWI ALERT] Irrigation STOPPED. Target water level reached 50.4% (at/above 50.0% threshold). Pump is now OFF.");
      sendSMS(ADMIN_PHONE, msg);
      lastTriggeredEvent = EVENT_STOPPED;
      break;
    }

    case '3': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 3: Water dropped to 43.5% (< 45.0%) AGAIN -> Irrigation Restarted"));
      cycleCount++;
      setPump(true);
      String msg = F("[WBACFSPWI ALERT] Irrigation RESTARTED (Cycle #2). Water dropped to 43.5% (< 45.0%). Pump is refilling the field.");
      sendSMS(ADMIN_PHONE, msg);
      lastTriggeredEvent = EVENT_RESTARTED;
      break;
    }

    case 'n':
    case 'N': {
      Serial.println(F("\n[CONFIG] Enter new Admin phone number (e.g. +639123456789) followed by newline:"));
      unsigned long waitStart = millis();
      String newNum = "";
      while (millis() - waitStart < 15000UL) {
        while (Serial.available()) {
          char c = Serial.read();
          if (c == '\r' || c == '\n') {
            if (newNum.length() >= 7) {
              newNum.toCharArray(ADMIN_PHONE, 20);
              Serial.print(F("[CONFIG SUCCESS] Admin phone updated to: "));
              Serial.println(ADMIN_PHONE);
              return;
            }
          } else {
            newNum += c;
          }
        }
      }
      Serial.println(F("[CONFIG TIMEOUT] No phone number entered."));
      break;
    }

    default:
      break;
  }
}

// ============================================================================
// 10. SETUP & MAIN LOOP
// ============================================================================

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println(F("\n======================================================="));
  Serial.println(F(" WBACFSPWI — Test 10: GSM SMS Irrigation Alert System  "));
  Serial.println(F("======================================================="));

  // Initialize output pins
  pinMode(PIN_RELAY_PUMP, OUTPUT);
  pinMode(PIN_STATUS_LED, OUTPUT);
  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    pinMode(PIN_SENSOR_PWR, OUTPUT);
    digitalWrite(PIN_SENSOR_PWR, LOW);
  }

  // Ensure relay and pump are initially OFF
  setPump(false);

  // Initialize GSM module
  gsmReady = initGSM();
  if (gsmReady) {
    Serial.println(F("[SUCCESS] GSM SIM800L module connected and initialized."));
  } else {
    Serial.println(F("[WARNING] GSM module initialization failed or pending."));
    Serial.println(F("  Check power supply: SIM800L requires 3.7V - 4.4V with 2A burst current."));
  }

  printHelpMenu();
}

void loop() {
  // Check for incoming user commands in Serial Monitor
  handleSerialCommands();

  // Periodic sensor reading and automated irrigation logic
  unsigned long now = millis();
  if (now - lastSampleTime >= SAMPLE_INTERVAL) {
    lastSampleTime = now;
    processIrrigationLogic();
  }
}
