/*
 * WBACFSPWI — Test 10: GSM Module (SIM800L / SIM900) SMS Irrigation Alert Test Suite
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 * 
 * Target Application: Miniature Rice Field Automation
 * Module Under Test: SIM800L / SIM900 GSM/GPRS Cellular Module
 * Surface Sensor: JSN-SR04T Waterproof Ultrasonic Sensor (Non-Contact Depth)
 * 
 * Description:
 *   Sends automated SMS text message alerts directly to Admin mobile phones:
 *     1. When irrigation is STARTED (water level drops below 45.0% threshold, pump turns ON).
 *     2. When irrigation is STOPPED (water level reaches >= 50.0% threshold, pump turns OFF).
 *     3. When irrigation is RESTARTED (water level drops below 45.0% again, pump turns ON).
 *     4. Emergency Safety Cutoff alert if continuous runtime reaches 3 minutes.
 * 
 * Hardware Pinout:
 *   - Arduino Pin D2: SoftwareSerial RX (Connects to GSM 5VT / TXD)
 *   - Arduino Pin D3: SoftwareSerial TX (Direct wire to SIM900A 5VR pin; or via 1kΩ/2kΩ divider for raw 3.3V SIM800L RXD)
 *   - Arduino Pin A1: JSN-SR04T Waterproof Ultrasonic TRIG Output (10µs pulse)
 *   - Arduino Pin A4: JSN-SR04T Waterproof Ultrasonic ECHO Input (5V TTL echo pulse)
 *   - Arduino Pin D7: 5V Relay Module (Active LOW, switches 12V DC Water Pump)
 *   - Arduino Pin D8: Capacitive Sensor Power Gate (Corrosion prevention)
 *   - Arduino Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone)
 *   - Arduino Pin D13: Status / Fault LED (Blinks during GSM operations, ON when pump is active)
 * 
 * GSM Module Power Note (CRITICAL):
 *   SIM900A / SIM800L can draw up to 2.0A peak current during cellular transmission bursts.
 *   DO NOT power from Arduino 5V or 3.3V pins. Power via LM2596 Buck Converter (5.0V for SIM900A 5V pin,
 *   or 4.0V for raw VBAT) with a 1000µF low-ESR electrolytic capacitor across power and shared Common GND.
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
const int PIN_TRIG           = A1;     // JSN-SR04T Ultrasonic TRIG Output (Digital Pin 15)
const int PIN_ECHO           = A4;     // JSN-SR04T Ultrasonic ECHO Input  (Digital Pin 18)
const int PIN_STATUS_LED     = 13;     // Built-in Status LED

const bool RELAY_ACTIVE_LOW  = true;   // Standard 5V relay modules trigger on LOW
const bool USE_SENSOR_PWR    = true;   // Enable power gating to prevent probe corrosion

SoftwareSerial gsmSerial(PIN_GSM_RX, PIN_GSM_TX);

// ============================================================================
// 2. ADMIN RECIPIENT PHONE NUMBER & SYSTEM CONFIGURATION
// ============================================================================
char ADMIN_PHONE[20]   = "+639158127228"; // Primary Admin
char ADMIN_PHONE_2[20] = "+639242074903"; // Secondary Admin

// ============================================================================
// 3. CALIBRATION & THRESHOLD VALUES (SYNCHRONIZED WITH SYSTEM MEMORY & TEST 11)
// ============================================================================
// Capacitive Root Sensor (Air vs Water raw ADC)
const int SOIL_AIR_RAW       = 408;    // 0% moisture in dry air (bench calibrated)
const int SOIL_WATER_RAW     = 172;    // 100% moisture in water (bench calibrated)

// JSN-SR04T Waterproof Ultrasonic Sensor Geometry (Centimeters)
// Live Bench Calibrated: Soil Bed=24.4cm (0%), 50% Target=22.4cm, 45% Refill=22.6cm
float sensorClearanceCM          = 20.4;   // Air gap from transducer face to 100% full mark (24.4 - 4.0)
float containerDepthCM           = 4.0;    // Calibrated usable water depth (2.0cm at 50% * 2)
const float SPEED_OF_SOUND_CM_US = 0.0343; // cm per microsecond at ~25°C
const float MIN_BLIND_ZONE_CM    = 20.0;   // Physical dead band limit of JSN-SR04T
float currentDistanceCM          = 0.0;    // Last measured acoustic distance

// Irrigation Decision Thresholds (Surface Water Level Control with 5% Hysteresis)
const float WATER_TARGET_MAX   = 50.0; // Automatically stop pump when surface water reaches >= 50.0%
const float WATER_REFILL_MIN   = 45.0; // Automatically start pump only when surface water drops < 45.0%

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
// 5. GSM HELPER & PARSING FUNCTIONS
// ============================================================================

// Flush all leftover bytes in SoftwareSerial buffer
void flushGSMSerial() {
  while (gsmSerial.available()) {
    gsmSerial.read();
  }
}

// Wait for a specific text response with timeout
bool waitForResponse(const char* expected, unsigned long timeoutMs) {
  unsigned long start = millis();
  String resp = "";
  while (millis() - start < timeoutMs) {
    while (gsmSerial.available()) {
      char c = (char)gsmSerial.read();
      resp += c;
    }
    if (resp.indexOf(expected) != -1) {
      return true;
    }
    if (resp.indexOf(F("ERROR")) != -1 || resp.indexOf(F("+CMS ERROR")) != -1) {
      return false;
    }
  }
  return false;
}

// Wait specifically for the SMS body prompt '>'
bool waitForPrompt(unsigned long timeoutMs) {
  unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (gsmSerial.available()) {
      char c = (char)gsmSerial.read();
      if (c == '>') {
        return true;
      }
    }
  }
  return false;
}

// Send an AT command, print debug info, and check for expected response
bool sendATCommand(const char* cmd, const char* expectedResponse, unsigned long timeoutMs) {
  flushGSMSerial();

  Serial.print(F("[GSM TX] "));
  Serial.println(cmd);
  gsmSerial.println(cmd);

  String response = "";
  unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (gsmSerial.available()) {
      char c = (char)gsmSerial.read();
      response += c;
    }
    if (response.indexOf(expectedResponse) != -1) {
      Serial.print(F("[GSM RX OK] "));
      Serial.println(response);
      return true;
    }
    if (response.indexOf(F("ERROR")) != -1 || response.indexOf(F("+CMS ERROR")) != -1) {
      Serial.print(F("[GSM RX ERROR] "));
      Serial.println(response);
      return false;
    }
  }

  Serial.print(F("[GSM RX TIMEOUT] "));
  Serial.println(response);
  return false;
}

// Read and dump GSM serial responses to Serial Monitor
void dumpGSMResponse(unsigned long waitMs) {
  unsigned long start = millis();
  while (millis() - start < waitMs) {
    while (gsmSerial.available()) {
      char c = (char)gsmSerial.read();
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
      flushGSMSerial();
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
    Serial.println(F("  Check the following wiring & power points:"));
    Serial.println(F("  1. Swap TX and RX: Arduino D2 connects to GSM 5VT / TXD; Arduino D3 connects to GSM 5VR / RXD."));
    Serial.println(F("  2. Direct 5VR connection: If using SIM900A '5VR' pin, wire D3 directly."));
    Serial.println(F("  3. Common GND: Ensure Arduino GND and SIM900A/SIM800L GND share the same common ground bus."));
    Serial.println(F("  4. Power Supply: Requires 3.7V - 4.4V with 2A burst capability + 1000µF buffer capacitor."));
    return false;
  }

  // Lock to 9600 baud for rock-solid SoftwareSerial communication
  if (activeBaud != 9600) {
    Serial.println(F("[INFO] Locking GSM module to reliable 9600 baud (AT+IPR=9600)..."));
    gsmSerial.println(F("AT+IPR=9600"));
    delay(400);
    gsmSerial.begin(9600);
    delay(400);
    sendATCommand("AT&W", "OK", 1000); // Save baud rate to non-volatile profile
  }

  sendATCommand("ATE0", "OK", 1000);              // Echo OFF
  sendATCommand("AT+CMEE=2", "OK", 1000);         // Enable verbose error messages for clear diagnosis
  sendATCommand("AT+CPIN?", "READY", 3000);       // Verify SIM card readiness
  sendATCommand("AT+CMGF=1", "OK", 1000);         // Set SMS to Text Mode
  sendATCommand("AT+CSCS=\"GSM\"", "OK", 1000);   // Set GSM default character encoding
  sendATCommand("AT+CSMP=17,167,0,0", "OK", 1000);// Standard SMS-SUBMIT parameters (validity 24h, text mode)

  // Check signal quality
  Serial.println(F("[INFO] Checking signal quality (AT+CSQ)..."));
  gsmSerial.println(F("AT+CSQ"));
  dumpGSMResponse(1500);

  // Check network registration (loop up to 10s if searching)
  Serial.println(F("[INFO] Checking network registration (AT+CREG?)..."));
  bool registered = false;
  for (int attempt = 1; attempt <= 5; attempt++) {
    flushGSMSerial();
    gsmSerial.println(F("AT+CREG?"));
    unsigned long regStart = millis();
    String regResp = "";
    while (millis() - regStart < 2000) {
      while (gsmSerial.available()) {
        regResp += (char)gsmSerial.read();
      }
    }
    Serial.print(F("  CREG response: "));
    Serial.println(regResp);

    if (regResp.indexOf(F("0,1")) != -1 || regResp.indexOf(F("0,5")) != -1 ||
        regResp.indexOf(F(",1")) != -1 || regResp.indexOf(F(",5")) != -1) {
      registered = true;
      Serial.println(F("  [SUCCESS] SIM registered on cellular carrier network!"));
      break;
    } else if (regResp.indexOf(F("0,2")) != -1) {
      Serial.println(F("  [INFO] Modem searching for operator network... waiting 2s."));
      delay(2000);
    } else {
      delay(1500);
    }
  }

  if (!registered) {
    Serial.println(F("  [WARNING] SIM not yet registered on network. SMS dispatch may fail until registered."));
  }

  // Check SMS Service Center Address (SMSC)
  Serial.println(F("[INFO] Querying SMS Service Center Address (AT+CSCA?)..."));
  gsmSerial.println(F("AT+CSCA?"));
  dumpGSMResponse(1500);

  Serial.println(F("--- GSM Module Ready & Configured ---\n"));
  return true;
}

// ============================================================================
// 6. ROBUST SMS TRANSMISSION ENGINE
// ============================================================================

bool sendSMS(const char* phoneNumber, const String& message) {
  if (!phoneNumber || strlen(phoneNumber) < 7) {
    Serial.println(F("[ERROR] Invalid recipient phone number."));
    return false;
  }

  if (millis() - lastSmsTime < SMS_COOLDOWN_MS && lastSmsTime != 0) {
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

  // Step 1: Clean buffer
  flushGSMSerial();

  // Step 2: Ensure Text Mode (AT+CMGF=1)
  gsmSerial.println(F("AT+CMGF=1"));
  if (!waitForResponse("OK", 2000)) {
    Serial.println(F("[ERROR] Failed to set SMS text mode (AT+CMGF=1)."));
    return false;
  }

  // Step 3: Ensure SMS-SUBMIT parameters (AT+CSMP=17,167,0,0)
  gsmSerial.println(F("AT+CSMP=17,167,0,0"));
  waitForResponse("OK", 1000);

  // Step 4: Initiate SMS sending with recipient phone number
  flushGSMSerial();
  gsmSerial.print(F("AT+CMGS=\""));
  gsmSerial.print(phoneNumber);
  gsmSerial.println(F("\""));

  // Step 5: Wait for '>' prompt from GSM modem
  if (!waitForPrompt(5000)) {
    Serial.println(F("[ERROR] GSM modem did not return '>' prompt. Canceling..."));
    gsmSerial.write(27); // ESC to abort
    delay(300);
    flushGSMSerial();
    return false;
  }

  // Step 6: Write message body and terminate with Ctrl+Z (ASCII 26)
  gsmSerial.print(message);
  delay(150);
  gsmSerial.write(26); // ASCII 26 (Ctrl+Z)

  // Step 7: Wait for cellular transmission confirmation (+CMGS: <id> or OK)
  String response = "";
  unsigned long start = millis();
  bool success = false;
  while (millis() - start < 25000UL) { // Cellular transmit may take up to 25s
    while (gsmSerial.available()) {
      char c = (char)gsmSerial.read();
      response += c;
      Serial.write(c); // Echo live modem response to Serial Monitor
    }
    if (response.indexOf(F("+CMGS:")) != -1 || response.indexOf(F("\r\nOK")) != -1) {
      success = true;
      break;
    }
    if (response.indexOf(F("ERROR")) != -1 || response.indexOf(F("+CMS ERROR")) != -1) {
      success = false;
      break;
    }
  }

  if (success) {
    Serial.println(F("\n[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<"));
    lastSmsTime = millis();
  } else {
    Serial.print(F("\n[SMS STATUS] >>> SMS FAILED TO SEND. Modem Response: "));
    Serial.println(response);

    // Provide actionable troubleshooting insights based on CMS error codes
    if (response.indexOf(F("302")) != -1) {
      Serial.println(F("  [DIAGNOSTIC] CMS ERROR 302: Modem not registered on cellular network. Check antenna or SIM."));
    } else if (response.indexOf(F("304")) != -1 || response.indexOf(F("500")) != -1) {
      Serial.println(F("  [DIAGNOSTIC] CMS ERROR 304/500: SMSC center address missing, invalid CSMP, or SIM out of load/credits."));
    } else if (response.indexOf(F("330")) != -1) {
      Serial.println(F("  [DIAGNOSTIC] CMS ERROR 330: SMSC address error. Query AT+CSCA? in console."));
    } else if (response.indexOf(F("512")) != -1) {
      Serial.println(F("  [DIAGNOSTIC] CMS ERROR 512: Modem busy. Wait a few seconds before retrying."));
    } else if (response.length() == 0) {
      Serial.println(F("  [DIAGNOSTIC] No response received: Likely GSM power brownout during RF transmission. Ensure 2A power supply + 1000µF capacitor."));
    }
  }

  return success;
}

// Broadcast alert SMS to all configured admin recipients
void dispatchAlertSMS(const String& message) {
  sendSMS(ADMIN_PHONE, message);
  if (strlen(ADMIN_PHONE_2) > 0) {
    delay(1500); // 1.5-second pause for GSM modem transmission recovery
    lastSmsTime = 0; // Clear cooldown so secondary recipient receives alert immediately
    sendSMS(ADMIN_PHONE_2, message);
  }
}

// ============================================================================
// 7. SENSOR READING FUNCTIONS (TEST 11 JSN-SR04T ULTRASONIC & CAPACITIVE SOIL)
// ============================================================================

// Single acoustic pulse-echo time-of-flight measurement via JSN-SR04T
float singlePingCM() {
  digitalWrite(PIN_TRIG, LOW);
  delayMicroseconds(4);

  digitalWrite(PIN_TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(PIN_TRIG, LOW);

  unsigned long duration = pulseIn(PIN_ECHO, HIGH, 35000UL); // 35ms timeout (~6m max)
  if (duration == 0) return -1.0;

  return (float)duration * SPEED_OF_SOUND_CM_US / 2.0;
}

// Multi-sample median filtered distance reading to reject ripples & jitter
float readFilteredDistanceCM(int samples = 5) {
  float readings[10];
  if (samples > 10) samples = 10;
  if (samples < 1)  samples = 1;

  int validCount = 0;
  for (int i = 0; i < samples; i++) {
    float d = singlePingCM();
    if (d > 0.0) {
      readings[validCount++] = d;
    }
    delay(20);
  }

  if (validCount == 0) return -1.0;

  // Median sort
  for (int i = 0; i < validCount - 1; i++) {
    for (int j = i + 1; j < validCount; j++) {
      if (readings[i] > readings[j]) {
        float temp = readings[i];
        readings[i] = readings[j];
        readings[j] = temp;
      }
    }
  }
  return readings[validCount / 2];
}

// Calculate water depth in centimeters
float calculateWaterDepthCM(float distanceCM) {
  if (distanceCM < 0.0 || containerDepthCM <= 0.0) return 0.0;
  float emptyFloorDistance = sensorClearanceCM + containerDepthCM;
  float depth = emptyFloorDistance - distanceCM;
  return constrain(depth, 0.0, containerDepthCM);
}

// Convert measured distance into 0.0% to 100.0% ponding percentage
float calculateWaterPercent(float distanceCM) {
  if (distanceCM < 0.0 || containerDepthCM <= 0.0) return 0.0;
  float emptyFloorDistance = sensorClearanceCM + containerDepthCM;
  float depth = emptyFloorDistance - distanceCM;
  float pct = (depth / containerDepthCM) * 100.0;
  return constrain(pct, 0.0, 100.0);
}

// JSN-SR04T Non-Contact Surface Water Depth Percentage (0.0% to 100.0%)
float readSurfaceWater() {
  float dist = readFilteredDistanceCM(5);
  if (dist > 0.0) {
    currentDistanceCM = dist;
    return calculateWaterPercent(dist);
  }
  // Return last known reading if acoustic echo timed out
  return currentSurfaceWater;
}

// Capacitive Soil Moisture Sensor v1.2 (Root Zone) with D8 power gating
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
// 8. PUMP CONTROL & STATE DISPATCH
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
// 9. IRRIGATION DECISION & SMS TRIGGER LOGIC (3-LAYER SAFETY NET)
// ============================================================================

void processIrrigationLogic() {
  currentSurfaceWater = readSurfaceWater();
  currentRootMoisture = readRootMoisture();

  // Print live telemetry to Serial Monitor
  Serial.print(F("[MONITOR] Dist: "));
  Serial.print(currentDistanceCM, 1);
  Serial.print(F("cm | Water: "));
  Serial.print(currentSurfaceWater, 1);
  Serial.print(F("% (Depth: "));
  Serial.print(calculateWaterDepthCM(currentDistanceCM), 1);
  Serial.print(F("cm) | Soil: "));
  Serial.print(currentRootMoisture, 1);
  Serial.print(F("% | Pump: "));
  Serial.print(pumpState ? F("ON") : F("OFF"));
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

    // Safety Interlock: Anti-splash minimum runtime (5s)
    if (runtime < MIN_PUMP_RUN_MS) {
      Serial.print(F("  [SAFETY] Anti-splash lock active ("));
      Serial.print((MIN_PUMP_RUN_MS - runtime) / 1000);
      Serial.println(F("s remaining). Pump cannot be stopped yet."));
      return;
    }

    // Safety Interlock: Maximum continuous runtime cap (180s)
    if (runtime >= MAX_PUMP_RUN_MS) {
      Serial.println(F("  [SAFETY ALERT] Continuous runtime cap exceeded (180s)! Emergency stop."));
      setPump(false);
      isSettling = true;
      settlingStartTime = now;
      
      String alertMsg = F("[WBACFSPWI ALERT] PUMP TIMEOUT: Continuous runtime cap reached (3 mins). Pump STOPPED for safety cooldown.");
      dispatchAlertSMS(alertMsg);
      return;
    }

    // TARGET REACHED: Surface water reached or exceeded 50.0% target
    if (currentSurfaceWater >= WATER_TARGET_MAX) {
      Serial.println(F("  [TRIGGER] Target threshold REACHED (>= 50.0%)! Stopping irrigation immediately."));
      setPump(false); // Pump stops instantly (0ms delay prevents container overflow!)
      isSettling = true;
      settlingStartTime = now;

      // SMS TRIGGER 2: Irrigation STOPPED upon reaching target threshold
      if (lastTriggeredEvent != EVENT_STOPPED) {
        String msg = F("[WBACFSPWI ALERT] Irrigation STOPPED. Target water level reached ");
        msg += String(currentSurfaceWater, 1);
        msg += F("% (at/above 50.0% threshold). Pump is now OFF.");

        Serial.println(F("  [POWER STABILIZATION] Pausing 1.5s for inductive kickback & power rail recovery..."));
        delay(1500);
        Serial.println(F("  [POWER STABILIZATION] Power rail clean. Transmitting stop alert SMS..."));
        dispatchAlertSMS(msg);
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
        cycleCount = 1;

        if (lastTriggeredEvent != EVENT_STARTED) {
          String msg = F("[WBACFSPWI ALERT] Irrigation STARTED. Water level dropped to ");
          msg += String(currentSurfaceWater, 1);
          msg += F("% (below 45.0% threshold). Pump is now ON.");

          Serial.println(F("  [POWER & SAFETY] Sending 'STARTED' SMS first while pump is OFF (100% clean power, zero overflow risk)..."));
          dispatchAlertSMS(msg);
          lastTriggeredEvent = EVENT_STARTED;
        }

        Serial.println(F("  [TRIGGER] Surface water dropped below 45.0%! Engaging pump relay (Cycle #1)."));
        setPump(true); // Now engage pump relay
      }
      // TRIGGER 3: Water went below threshold AGAIN -> Irrigation RESTARTED
      else if (lastTriggeredEvent == EVENT_STOPPED) {
        cycleCount++;

        String msg = F("[WBACFSPWI ALERT] Irrigation RESTARTED (Cycle #");
        msg += String(cycleCount);
        msg += F("). Water dropped to ");
        msg += String(currentSurfaceWater, 1);
        msg += F("% (< 45.0%). Pump is refilling the field.");

        Serial.println(F("  [POWER & SAFETY] Sending 'RESTARTED' SMS first while pump is OFF (100% clean power, zero overflow risk)..."));
        dispatchAlertSMS(msg);
        lastTriggeredEvent = EVENT_RESTARTED;

        Serial.print(F("  [TRIGGER] Surface water dropped below 45.0% AGAIN! Engaging pump relay (Cycle #"));
        Serial.print(cycleCount);
        Serial.println(F(")."));
        setPump(true); // Now engage pump relay
      }
    }
  }
}

// ============================================================================
// 10. SERIAL COMMAND INTERPRETER (INTERACTIVE BENCH TEST CONSOLE)
// ============================================================================

void printHelpMenu() {
  Serial.println(F("\n======================================================="));
  Serial.println(F(" WBACFSPWI Test 10: GSM SMS Alert Interactive Console  "));
  Serial.println(F("======================================================="));
  Serial.print(F(" Primary Admin Phone   : "));
  Serial.println(ADMIN_PHONE);
  Serial.print(F(" Secondary Admin Phone : "));
  Serial.println(ADMIN_PHONE_2);
  Serial.print(F(" Surface Water Sensor  : JSN-SR04T Ultrasonic (TRIG: A1, ECHO: A4)\n"));
  Serial.print(F(" Calibrated Clearance  : ")); Serial.print(sensorClearanceCM, 1); Serial.println(F(" cm"));
  Serial.print(F(" Usable Depth          : ")); Serial.print(containerDepthCM, 1);  Serial.println(F(" cm"));
  Serial.print(F(" Empty Floor Distance  : ")); Serial.print(sensorClearanceCM + containerDepthCM, 1); Serial.println(F(" cm"));
  Serial.println(F("-------------------------------------------------------"));
  Serial.println(F(" Available Test Commands:"));
  Serial.println(F("   's' -> Send an instant test SMS to the Admin phone"));
  Serial.println(F("   'c' -> Comprehensive GSM diagnostics (Signal, Network, SMSC)"));
  Serial.println(F("   'r' -> Re-initialize GSM modem & network registration"));
  Serial.println(F("   'p' -> Toggle 12V DC Pump Relay ON/OFF"));
  Serial.println(F("   'w' -> Print live Ultrasonic distance, depth & soil moisture"));
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
      String testMsg = F("[WBACFSPWI TEST] GSM Module SIM800L link verified with JSN-SR04T ultrasonic sensor. Ready for irrigation alerts.");
      dispatchAlertSMS(testMsg);
      break;
    }

    case 'c':
    case 'C': {
      Serial.println(F("\n[GSM DIAGNOSTICS] Querying Module & Network Status..."));
      Serial.println(F("1. Signal Quality (AT+CSQ):"));
      gsmSerial.println(F("AT+CSQ"));
      dumpGSMResponse(1500);

      Serial.println(F("\n2. Network Registration (AT+CREG?):"));
      gsmSerial.println(F("AT+CREG?"));
      dumpGSMResponse(1500);

      Serial.println(F("\n3. Current Operator (AT+COPS?):"));
      gsmSerial.println(F("AT+COPS?"));
      dumpGSMResponse(2000);

      Serial.println(F("\n4. SMS Service Center Address (AT+CSCA?):"));
      gsmSerial.println(F("AT+CSCA?"));
      dumpGSMResponse(1500);

      Serial.println(F("\n5. Power/Battery Status (AT+CBC):"));
      gsmSerial.println(F("AT+CBC"));
      dumpGSMResponse(1500);
      break;
    }

    case 'r':
    case 'R': {
      Serial.println(F("\n[RESET] Re-initializing GSM module..."));
      gsmReady = initGSM();
      break;
    }

    case 'p':
    case 'P':
      Serial.print(F("[TEST] Toggling pump manually. Current: "));
      Serial.println(pumpState ? F("ON -> turning OFF") : F("OFF -> turning ON"));
      setPump(!pumpState);
      break;

    case 'w':
    case 'W': {
      float dist = readFilteredDistanceCM(5);
      float pct  = calculateWaterPercent(dist);
      float dpth = calculateWaterDepthCM(dist);
      float soil = readRootMoisture();

      Serial.println(F("\n[INSTANT SENSOR READINGS]"));
      Serial.print(F("• Ultrasonic Echo Distance: "));
      if (dist > 0.0) {
        Serial.print(dist, 2);
        Serial.println(F(" cm"));
      } else {
        Serial.println(F("TIMEOUT (No echo detected)"));
      }
      Serial.print(F("• Surface Water Depth:      "));
      Serial.print(dpth, 2);
      Serial.println(F(" cm"));
      Serial.print(F("• Surface Ponding Level:    "));
      Serial.print(pct, 1);
      Serial.println(F(" %"));
      Serial.print(F("• Root Soil Moisture:       "));
      Serial.print(soil, 1);
      Serial.println(F(" %"));
      break;
    }

    case '1': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 1: Water dropped to 42.0% (< 45.0%) -> Irrigation Started"));
      cycleCount = 1;
      setPump(true);
      String msg = F("[WBACFSPWI ALERT] Irrigation STARTED. Water level dropped to 42.0% (below 45.0% threshold). Pump is now ON.");
      dispatchAlertSMS(msg);
      lastTriggeredEvent = EVENT_STARTED;
      break;
    }

    case '2': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 2: Water reached 50.4% (>= 50.0%) -> Irrigation Stopped"));
      setPump(false);
      isSettling = true;
      settlingStartTime = millis();
      String msg = F("[WBACFSPWI ALERT] Irrigation STOPPED. Target water level reached 50.4% (at/above 50.0% threshold). Pump is now OFF.");
      dispatchAlertSMS(msg);
      lastTriggeredEvent = EVENT_STOPPED;
      break;
    }

    case '3': {
      Serial.println(F("\n[SIMULATION] Simulating Trigger 3: Water dropped to 43.5% (< 45.0%) AGAIN -> Irrigation Restarted"));
      cycleCount++;
      setPump(true);
      String msg = F("[WBACFSPWI ALERT] Irrigation RESTARTED (Cycle #2). Water dropped to 43.5% (< 45.0%). Pump is refilling the field.");
      dispatchAlertSMS(msg);
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
// 11. SETUP & MAIN LOOP
// ============================================================================

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println(F("\n======================================================="));
  Serial.println(F(" WBACFSPWI — Test 10: GSM SMS Irrigation Alert System  "));
  Serial.println(F(" Surface Sensor: JSN-SR04T Waterproof Ultrasonic       "));
  Serial.println(F("======================================================="));

  // Initialize output pins
  pinMode(PIN_RELAY_PUMP, OUTPUT);
  pinMode(PIN_STATUS_LED, OUTPUT);
  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    pinMode(PIN_SENSOR_PWR, OUTPUT);
    digitalWrite(PIN_SENSOR_PWR, LOW);
  }

  // Initialize JSN-SR04T Ultrasonic sensor pins
  pinMode(PIN_TRIG, OUTPUT);
  pinMode(PIN_ECHO, INPUT);
  digitalWrite(PIN_TRIG, LOW);

  // Ensure relay and pump are initially OFF
  setPump(false);

  // =============================================================
  // STEP 1: 5-SECOND GSM BASEBAND WARM-UP COUNTDOWN
  // =============================================================
  Serial.println(F("\n[STARTUP] Step 1/3: 5-Second GSM Baseband Boot-Up Countdown..."));
  Serial.println(F("  Allowing GSM module to complete hardware power-on & boot baseband..."));
  for (int s = 5; s > 0; s--) {
    Serial.print(F("  -> GSM boot countdown: "));
    Serial.print(s);
    Serial.println(F("s"));
    digitalWrite(PIN_STATUS_LED, (s % 2 == 0) ? HIGH : LOW);
    delay(1000);
  }
  digitalWrite(PIN_STATUS_LED, LOW);

  // =============================================================
  // STEP 2: INITIALIZE & CHECK GSM MODULE, SIGNAL & REGISTRATION
  // =============================================================
  Serial.println(F("\n[STARTUP] Step 2/3: Checking GSM Module, Signal & Network Registration..."));
  gsmReady = initGSM();
  if (gsmReady) {
    Serial.println(F("[SUCCESS] GSM module connected and initialized."));
  } else {
    Serial.println(F("[WARNING] GSM module initialization failed or pending."));
    Serial.println(F("  Check power supply: SIM800L/SIM900 requires 3.7V - 4.4V with 2A burst current."));
  }

  // =============================================================
  // STEP 3: 3-SECOND SENSOR STABILIZATION COUNTDOWN
  // =============================================================
  Serial.println(F("\n[STARTUP] Step 3/3: 3-Second Sensor Stabilization Countdown..."));
  Serial.println(F("  Locking ultrasonic baseline & stabilizing sensor inputs..."));
  for (int s = 3; s > 0; s--) {
    Serial.print(F("  -> Sensor stabilization countdown: "));
    Serial.print(s);
    Serial.println(F("s"));

    // Take initial baseline readings
    currentDistanceCM   = readFilteredDistanceCM(3);
    currentSurfaceWater = readSurfaceWater();
    currentRootMoisture = readRootMoisture();

    digitalWrite(PIN_STATUS_LED, HIGH);
    delay(500);
    digitalWrite(PIN_STATUS_LED, LOW);
    delay(500);
  }
  digitalWrite(PIN_STATUS_LED, LOW);
  Serial.println(F("[STARTUP] Setup complete! Starting automated alert test...\n"));

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
