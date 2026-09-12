/*
 * WBACFSPWI — Full Standalone Arduino Uno Irrigation Controller
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 * 
 * Target Application: Miniature Rice Field Automation
 * 
 * Hardware Setup:
 *   - Arduino Uno R3 (ATmega328P)
 *   - Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone)
 *   - Pin A1: HW-080 Surface Water Level Sensor (Surface Ponding / Depth)
 *   - Pin A2: 3S 18650 / 12V Motorcycle Battery Voltage Divider (100kΩ / 33kΩ)
 *   - Pin A3: 30W Solar Panel Voltage Divider (100kΩ / 20kΩ)
 *   - Pin D2: GSM SoftwareSerial RX (from SIM900A 5VT / TXD)
 *   - Pin D3: GSM SoftwareSerial TX (direct to SIM900A 5VR onboard level shifter)
 *   - Pin D7: 5V Relay Module (DC Water Pump Switch, Active LOW)
 *   - Pin D8: Soil Moisture Sensor Power Gate (Corrosion Prevention)
 *   - Pin D9: NodeMCU SoftwareSerial RX (from NodeMCU Pin D2 TX)
 *   - Pin D10: NodeMCU SoftwareSerial TX (to NodeMCU Pin D1 RX via 1kΩ / 2kΩ divider)
 *   - Pin D13: Status / Fault Indicator LED
 * 
 * Autonomous Dual-Channel Telemetry & Decision Logic:
 *   - Channel 1 (WiFi Bridge): Streams real-time JSON to NodeMCU ESP8266 -> XAMPP Apache / MySQL dashboard.
 *   - Channel 2 (Cellular GSM): Sends automated SMS alerts directly to Admin's mobile phone:
 *       * Irrigation STARTED (Water level < 45.0%, pump turns ON).
 *       * Irrigation STOPPED (Water level >= 50.0%, pump turns OFF).
 *       * Irrigation RESTARTED (Water level drops < 45.0% again).
 *   - 3-Layer Safety Net:
 *       1. Hysteresis band (45.0% refill / 50.0% target).
 *       2. Anti-splash minimum runtime (5s min pump run).
 *       3. Wave settling window (10s stabilization delay).
 *   - Battery Protection: Low battery lockout (< 10.0V).
 *   - Continuous Runtime Cap: 180s max runtime with mandatory cooldown.
 */

#include <SoftwareSerial.h>

// ============================================================================
// 1. PIN DEFINITIONS & HARDWARE CONSTANTS
// ============================================================================
const int PIN_ROOT_SOIL      = A0;     // Capacitive Root Soil Moisture Sensor
const int PIN_SURFACE_WATER  = A1;     // HW-080 Surface Water Ponding Sensor
const int PIN_VBATT          = A2;     // Battery Voltage Divider (100k/33k)
const int PIN_VSOLAR         = A3;     // Solar Panel Voltage Divider (100k/20k)

const int PIN_GSM_RX         = 2;      // Arduino RX <- GSM 5VT / TXD
const int PIN_GSM_TX         = 3;      // Arduino TX -> GSM 5VR (direct for SIM900A)
const int PIN_RELAY_PUMP     = 7;      // 5V Relay Control (DC Water Pump)
const int PIN_SENSOR_PWR     = 8;      // Capacitive Sensor Power Gate
const int PIN_ESP_RX         = 9;      // Arduino RX <- NodeMCU TX (D2 / GPIO4)
const int PIN_ESP_TX         = 10;     // Arduino TX -> NodeMCU RX (D1 / GPIO5) via 1k/2k divider
const int PIN_STATUS_LED     = 13;     // Built-in Status LED

const bool RELAY_ACTIVE_LOW  = true;   // Standard 5V relay modules trigger on LOW
const bool USE_SENSOR_PWR    = true;   // Enable power gating to prevent corrosion

// Dedicated SoftwareSerial Ports
SoftwareSerial espSerial(PIN_ESP_RX, PIN_ESP_TX); // WiFi Bridge Link (NodeMCU)
SoftwareSerial gsmSerial(PIN_GSM_RX, PIN_GSM_TX); // Cellular SMS Link (SIM900A)

// Admin Mobile Phone Number for Automated SMS Alerts
char ADMIN_PHONE[20] = "+639169751409";

// ============================================================================
// 2. CALIBRATION & THRESHOLD VALUES (SYNCHRONIZED WITH SYSTEM MEMORY)
// ============================================================================
const float ARDUINO_VREF     = 5.00;

// Voltage Dividers
const float VBATT_RATIO      = 4.0303; // (100k + 33k) / 33k
const float VSOLAR_RATIO     = 6.0000; // (100k + 20k) / 20k

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

// Safety & Battery Protection Thresholds
const float BATT_MIN_LOCKOUT  = 10.00; // Low battery lockout cutoff (10.0V deep discharge protection)
const float BATT_RESUME_VOLTS = 10.50; // Voltage needed to clear lockout and resume operation

// Timing Protections (in milliseconds)
const unsigned long MIN_PUMP_RUN_MS  = 5000UL;   // 5s minimum runtime (prevents momentary splash cutoffs)
const unsigned long MAX_PUMP_RUN_MS  = 180000UL; // 3 minutes maximum continuous runtime
const unsigned long PUMP_COOLDOWN_MS = 60000UL;  // 1 minute mandatory cooldown after timeout
const unsigned long SETTLING_DELAY_MS= 10000UL;  // 10s water settling / stabilization window
const unsigned long SAMPLE_INTERVAL   = 1000UL;   // Read sensors & evaluate logic every 1s
const unsigned long TELEMETRY_PERIOD  = 1000UL;   // Print telemetry & stream JSON every 1s
const unsigned long SMS_COOLDOWN_MS   = 15000UL;  // 15s minimum spacing between SMS sends

// ============================================================================
// 3. SYSTEM STATE VARIABLES
// ============================================================================
bool  pumpState          = false;
bool  isSettling         = false;
bool  lowBatteryLockout  = false;
bool  timeoutLockout     = false;
bool  manualOverride     = false;
bool  manualOverrideState= false;
bool  gsmReady           = false;
int   cycleCount         = 0;

unsigned long pumpStartTime     = 0;
unsigned long pumpStopTime      = 0;
unsigned long settlingStartTime = 0;
unsigned long lastSampleTime    = 0;
unsigned long lastTeleTime      = 0;
unsigned long lastSmsTime       = 0;

float currentRootMoisture = 0.0;
float currentSurfaceWater = 0.0;
float currentBattVolts    = 0.0;
float currentSolarVolts   = 0.0;

enum IrrigationEventType {
  EVENT_NONE,
  EVENT_STARTED,
  EVENT_STOPPED,
  EVENT_RESTARTED
};
IrrigationEventType lastTriggeredEvent = EVENT_NONE;

// ============================================================================
// 4. GSM CELLULAR HELPER FUNCTIONS
// ============================================================================

bool sendATCommand(const String& cmd, const char* expected, unsigned long timeoutMs) {
  while (gsmSerial.available()) gsmSerial.read();
  gsmSerial.println(cmd);

  String resp = "";
  unsigned long start = millis();
  while (millis() - start < timeoutMs) {
    while (gsmSerial.available()) {
      resp += (char)gsmSerial.read();
    }
    if (resp.indexOf(expected) != -1) {
      return true;
    }
    if (resp.indexOf(F("ERROR")) != -1) {
      return false;
    }
  }
  return false;
}

bool initGSM() {
  Serial.println(F("\n--- Initializing GSM Module (SIM900A) ---"));
  gsmSerial.listen();

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
      while (gsmSerial.available()) gsmSerial.read();
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
    Serial.println(F("[WARNING] GSM Module not responding to AT commands. Continuing with WiFi telemetry only."));
    espSerial.listen();
    return false;
  }

  // Lock to 9600 baud for stable SoftwareSerial operation
  if (activeBaud != 9600) {
    Serial.println(F("[INFO] Locking GSM module to 9600 baud (AT+IPR=9600)..."));
    gsmSerial.println(F("AT+IPR=9600"));
    delay(400);
    gsmSerial.begin(9600);
    delay(400);
    sendATCommand("AT&W", "OK", 1000);
  }

  sendATCommand("ATE0", "OK", 1000);        // Echo OFF
  sendATCommand("AT+CPIN?", "READY", 3000); // Check SIM status
  sendATCommand("AT+CMGF=1", "OK", 1000);   // Set SMS to Text Mode
  sendATCommand("AT+CSCS=\"GSM\"", "OK", 1000); // Set GSM character set

  Serial.println(F("--- GSM Module Ready & Configured ---\n"));
  espSerial.listen(); // Return listening focus to NodeMCU WiFi bridge
  return true;
}

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

  gsmSerial.listen(); // Switch listening focus to GSM

  // Status LED alert blink
  for (int i = 0; i < 3; i++) {
    digitalWrite(PIN_STATUS_LED, HIGH);
    delay(80);
    digitalWrite(PIN_STATUS_LED, LOW);
    delay(80);
  }

  gsmSerial.println(F("AT+CMGF=1"));
  delay(300);

  gsmSerial.print(F("AT+CMGS=\""));
  gsmSerial.print(phoneNumber);
  gsmSerial.println(F("\""));
  delay(500);

  gsmSerial.print(message);
  delay(300);

  gsmSerial.write(26); // ASCII 26 (Ctrl+Z)

  String response = "";
  unsigned long start = millis();
  bool success = false;
  while (millis() - start < 15000UL) {
    while (gsmSerial.available()) {
      response += (char)gsmSerial.read();
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
    Serial.print(F("[SMS STATUS] >>> SMS FAILED. Response: "));
    Serial.println(response);
  }

  espSerial.listen(); // Return listening focus to NodeMCU WiFi bridge
  return success;
}

// ============================================================================
// 5. PUMP CONTROL & HARDWARE SENSORS
// ============================================================================

void setPump(bool enable) {
  digitalWrite(PIN_RELAY_PUMP, enable ? (RELAY_ACTIVE_LOW ? LOW : HIGH) : (RELAY_ACTIVE_LOW ? HIGH : LOW));
  digitalWrite(PIN_STATUS_LED, enable ? HIGH : LOW);

  if (enable == pumpState) return;

  pumpState = enable;
  if (pumpState) {
    pumpStartTime = millis();
    Serial.println(F("[EVENT] Pump STARTED."));

    if (lastTriggeredEvent != EVENT_STARTED) {
      cycleCount++;
      lastTriggeredEvent = (cycleCount > 1) ? EVENT_RESTARTED : EVENT_STARTED;

      String msg = F("WBACFSPWI Alert:\nIrrigation ");
      msg += (cycleCount > 1) ? F("RESTARTED (Cycle #") : F("STARTED.");
      if (cycleCount > 1) { msg += cycleCount; msg += F(")."); }
      msg += F("\nWater level: ");
      msg += String(currentSurfaceWater, 1);
      msg += F("% (< 45%).\nSoil moisture: ");
      msg += String(currentRootMoisture, 1);
      msg += F("%");

      if (gsmReady) {
        sendSMS(ADMIN_PHONE, msg);
      }
    }
  } else {
    pumpStopTime = millis();
    Serial.println(F("[EVENT] Pump STOPPED."));
  }
}

float readRootMoisture() {
  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    digitalWrite(PIN_SENSOR_PWR, HIGH);
    delay(80); // Stabilization
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
    return constrain(pct, 0.0, 100.0);
  }
}

float readBatteryVoltage() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_VBATT);
    delay(2);
  }
  float avgAdc = (float)sum / 16.0;
  return (avgAdc / 1023.0) * ARDUINO_VREF * VBATT_RATIO;
}

float readSolarVoltage() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_VSOLAR);
    delay(2);
  }
  float avgAdc = (float)sum / 16.0;
  return (avgAdc / 1023.0) * ARDUINO_VREF * VSOLAR_RATIO;
}

void printTelemetry() {
  Serial.println(F("--------------------------------------------------"));
  Serial.print(F("Time: ")); Serial.print(millis() / 1000); Serial.println(F("s"));
  
  Serial.print(F("Root Moisture   : ")); Serial.print(currentRootMoisture, 1); Serial.println(F(" %"));
  Serial.print(F("Surface Water   : ")); Serial.print(currentSurfaceWater, 1); Serial.println(F(" %"));
  Serial.print(F("Battery Voltage : ")); Serial.print(currentBattVolts, 2); Serial.print(F(" V "));
  if (lowBatteryLockout) Serial.print(F("[LOW BATT LOCK]"));
  Serial.println();
  
  Serial.print(F("Solar Output    : ")); Serial.print(currentSolarVolts, 2); Serial.println(F(" V"));
  Serial.print(F("Pump Relay State: ")); 
  if (pumpState) {
    Serial.println(F("ON (Irrigating)"));
  } else if (isSettling) {
    Serial.println(F("OFF (10s Settling Verification)"));
  } else {
    Serial.println(F("OFF (Standby)"));
  }
  
  if (timeoutLockout) {
    Serial.println(F("[ALERT] Pump Timeout Cooldown in effect!"));
  }
  Serial.println(F("--------------------------------------------------"));

  // Send formatted JSON line over SoftwareSerial to NodeMCU ESP8266 WiFi Bridge
  espSerial.print(F("{\"soil_moisture\":"));
  espSerial.print(currentRootMoisture, 1);
  espSerial.print(F(",\"water_level\":"));
  espSerial.print(currentSurfaceWater, 1);
  espSerial.print(F(",\"battery_voltage\":"));
  espSerial.print(currentBattVolts, 2);
  espSerial.print(F(",\"solar_output\":"));
  espSerial.print(currentSolarVolts, 2);
  espSerial.print(F(",\"pump_state\":\""));
  espSerial.print(pumpState ? F("on") : F("off"));
  espSerial.println(F("\"}"));
}

// ============================================================================
// 6. SETUP & MAIN LOOP
// ============================================================================

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  // 1. Initialize relay pin to safe OFF (HIGH for Active LOW) BEFORE pinMode
  digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);
  pinMode(PIN_RELAY_PUMP, OUTPUT);
  digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);

  pinMode(PIN_STATUS_LED, OUTPUT);
  digitalWrite(PIN_STATUS_LED, LOW);

  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    pinMode(PIN_SENSOR_PWR, OUTPUT);
    digitalWrite(PIN_SENSOR_PWR, LOW);
  }

  // 2. Initialize SoftwareSerial for NodeMCU WiFi Bridge & GSM Module
  espSerial.begin(9600);
  espSerial.listen();

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: Solar Rice Irrigation Controller     "));
  Serial.println(F(" Standalone Arduino Uno Automation Firmware      "));
  Serial.println(F(" Dual Telemetry: NodeMCU WiFi Bridge & SIM900A GSM"));
  Serial.println(F("3-Layer Automatic Surface Water Level Control:"));
  Serial.println(F("  - TARGET MAX (PUMP OFF) : >= 50.0% Surface Water"));
  Serial.println(F("  - REFILL MIN (PUMP ON)  : < 45.0% Surface Water (5% Hysteresis Gap)"));
  Serial.println(F("  - MINIMUM RUNTIME       : 5 Seconds Anti-Splash Protection"));
  Serial.println(F("  - SETTLING WINDOW       : 10 Seconds Wave Stabilization"));
  Serial.println(F("=================================================="));

  // Initialize GSM Cellular Transceiver
  gsmReady = initGSM();
  if (gsmReady) {
    Serial.println(F("[SYSTEM] GSM SMS Alert Module: ACTIVE"));
  } else {
    Serial.println(F("[SYSTEM] GSM SMS Alert Module: OFFLINE (Operating with WiFi Bridge)"));
  }

  // 10-Second Sensor Calibration & Stabilization Window
  Serial.println(F("[STARTUP] 10-Second Sensor Calibration & Stabilization Window..."));
  for (int sec = 10; sec > 0; sec--) {
    Serial.print(F("  -> Stabilizing sensors... "));
    Serial.print(sec);
    Serial.println(F("s remaining"));
    readRootMoisture();
    readSurfaceWater();
    delay(1000);
  }
  Serial.println(F("[STARTUP] Calibration window complete! Starting autonomous maintenance...\n"));
}

void loop() {
  unsigned long now = millis();

  // -------------------------------------------------------------
  // A. Periodic Sensor Sampling & Automation Logic (Every 1s)
  // -------------------------------------------------------------
  if (now - lastSampleTime >= SAMPLE_INTERVAL || lastSampleTime == 0) {
    lastSampleTime = now;

    currentRootMoisture = readRootMoisture();
    currentSurfaceWater = readSurfaceWater();
    currentBattVolts    = readBatteryVoltage();
    currentSolarVolts   = readSolarVoltage();

    // 1. Battery Protection Check
    if (currentBattVolts < BATT_MIN_LOCKOUT) {
      lowBatteryLockout = true;
    } else if (lowBatteryLockout && currentBattVolts >= BATT_RESUME_VOLTS) {
      lowBatteryLockout = false;
    }

    // 2. Pump Timeout & Cooldown Check
    if (pumpState && (now - pumpStartTime >= MAX_PUMP_RUN_MS)) {
      Serial.println(F("[SAFETY] Max pump runtime reached! Stopping pump."));
      setPump(false);
      timeoutLockout = true;
    }
    if (timeoutLockout && (now - pumpStopTime >= PUMP_COOLDOWN_MS)) {
      timeoutLockout = false;
      Serial.println(F("[SAFETY] Cooldown finished. Resuming normal operations."));
    }

    // 3. Automated Decision Logic with 10s Settling Safety Net & Manual Override
    if (lowBatteryLockout || timeoutLockout) {
      setPump(false);
      isSettling = false;
    } else if (manualOverride) {
      setPump(manualOverrideState);
      isSettling = false;
    } else if (isSettling) {
      if (now - settlingStartTime >= SETTLING_DELAY_MS) {
        isSettling = false;
        if (currentSurfaceWater >= WATER_TARGET_MAX) {
          Serial.println(F(">>> [STABLE] 10s Settling verified >= 50.0% -> Pump stays OFF"));
        } else {
          Serial.println(F(">>> [REFILL] 10s Settling settled < 50.0% -> Resuming pump"));
          setPump(true);
        }
      }
    } else if (pumpState) {
      // Layer 2: Minimum Run Time Check (Enforce at least 5s before allowing target shutoff)
      if (now - pumpStartTime >= MIN_PUMP_RUN_MS) {
        // Layer 1: Target Reached Check
        if (currentSurfaceWater >= WATER_TARGET_MAX) {
          setPump(false);
          isSettling = true;
          settlingStartTime = now;
          Serial.println(F(">>> [TARGET REACHED] Starting 10s settling verification..."));

          if (lastTriggeredEvent != EVENT_STOPPED) {
            lastTriggeredEvent = EVENT_STOPPED;
            String msg = F("WBACFSPWI Alert:\nIrrigation STOPPED.\nTarget depth reached: ");
            msg += String(currentSurfaceWater, 1);
            msg += F("% (>= 50%).\nSoil moisture: ");
            msg += String(currentRootMoisture, 1);
            msg += F("%\nBattery: ");
            msg += String(currentBattVolts, 2);
            msg += F("V");

            if (gsmReady) {
              sendSMS(ADMIN_PHONE, msg);
            }
          }
        }
      }
    } else {
      // Pump is idle and not settling: start pump if below threshold (< 45.0%)
      if (currentSurfaceWater < WATER_REFILL_MIN) {
        setPump(true);
      }
    }
  }

  // -------------------------------------------------------------
  // B. Telemetry Output & WiFi Stream (Every 1s)
  // -------------------------------------------------------------
  if (now - lastTeleTime >= TELEMETRY_PERIOD || lastTeleTime == 0) {
    lastTeleTime = now;
    printTelemetry();
  }

  // -------------------------------------------------------------
  // C. Status LED Indicator Handling
  // -------------------------------------------------------------
  if (lowBatteryLockout) {
    digitalWrite(PIN_STATUS_LED, (now / 200) % 2 == 0 ? HIGH : LOW);
  } else if (pumpState) {
    digitalWrite(PIN_STATUS_LED, HIGH);
  } else {
    digitalWrite(PIN_STATUS_LED, (now / 1500) % 2 == 0 ? HIGH : LOW);
  }

  // -------------------------------------------------------------
  // D. Process Remote WiFi Commands from NodeMCU ESP8266
  // -------------------------------------------------------------
  if (espSerial.available() > 0) {
    String cmd = espSerial.readStringUntil('\n');
    cmd.trim();
    if (cmd == "PUMP_ON") {
      manualOverride = true;
      manualOverrideState = true;
      setPump(true);
      Serial.println(F(">>> [REMOTE COMMAND via WiFi] Manual Override: PUMP ON"));
    } else if (cmd == "PUMP_OFF") {
      manualOverride = true;
      manualOverrideState = false;
      setPump(false);
      Serial.println(F(">>> [REMOTE COMMAND via WiFi] Manual Override: PUMP OFF"));
    } else if (cmd == "PUMP_AUTO") {
      if (manualOverride) {
        manualOverride = false;
        Serial.println(F(">>> [REMOTE COMMAND via WiFi] Manual Override Cleared -> AUTO Mode Resumed"));
      }
    }
  }

  delay(20);
}
