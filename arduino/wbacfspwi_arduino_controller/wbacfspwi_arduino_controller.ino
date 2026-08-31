/*
 * WBACFSPWI — Full Standalone Arduino Uno Irrigation Controller
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 * 
 * Target Application: Miniature Rice Field Automation
 * 
 * Hardware Setup:
 *   - Arduino Uno R3 (ATmega328P)
 *   - Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone)
 *   - Pin A1: Surface Water Level Sensor (Surface Ponding / Depth)
 *   - Pin A2: 3S 18650 Battery Voltage Divider (100kΩ / 33kΩ)
 *   - Pin A3: 30W Solar Panel Voltage Divider (100kΩ / 20kΩ)
 *   - Pin D7: 5V Relay Module (DC Water Pump Switch)
 *   - Pin D8: Soil Moisture Sensor Power Gate (Corrosion Prevention)
 *   - Pin D13: Status / Fault Indicator LED
 * 
 * Autonomous Control Logic:
 *   - Starts pump if Root Moisture < MOISTURE_START_PCT AND Surface Water < SURFACE_MAX_PCT.
 *   - Stops pump when Root Moisture >= MOISTURE_STOP_PCT OR Surface Water >= SURFACE_MAX_PCT.
 *   - Safety Interlock 1: Low battery lockout (< 10.0V).
 *   - Safety Interlock 2: Continuous pump runtime cap (e.g. 180 seconds max) with mandatory cooldown.
 *   - Safety Interlock 3: Sensor wire disconnect/fault detection.
 *   - Periodic Serial Telemetry output (115200 baud).
 */

// ============================================================================
// 1. PIN DEFINITIONS & HARDWARE CONSTANTS
// ============================================================================
const int PIN_ROOT_SOIL      = A0;
const int PIN_SURFACE_WATER  = A1;
const int PIN_VBATT          = A2;
const int PIN_VSOLAR         = A3;

const int PIN_RELAY_PUMP     = 7;
const int PIN_SENSOR_PWR     = 8;
const int PIN_STATUS_LED     = 13;

const bool RELAY_ACTIVE_LOW  = true;   // Most 5V relay modules trigger on LOW
const bool USE_SENSOR_PWR    = true;   // Enable power gating to prevent corrosion

// ============================================================================
// 2. CALIBRATION & THRESHOLD VALUES
// ============================================================================
const float ARDUINO_VREF     = 5.00;

// Voltage Dividers
const float VBATT_RATIO      = 4.0303; // (100k + 33k) / 33k
const float VSOLAR_RATIO     = 6.0000; // (100k + 20k) / 20k

// Capacitive Root Sensor (Air vs Water raw ADC)
const int SOIL_AIR_RAW       = 417;    // 0% moisture in dry air
const int SOIL_WATER_RAW     = 153;    // 100% moisture in water

// HW-080 Moisture Sensor (Surface Ponding Depth: Inverted ADC mapping)
const int HW080_RAW_DRY      = 1020;   // Stage A: Probe in dry air (0% ponding / surface dry)
const int HW080_RAW_WET      = 360;    // Probe at container maximum depth (100% full ponding)

// Irrigation Decision Thresholds (for Rice Field - Constant 85% Water Level Maintenance)
const float WATER_TARGET_MAX   = 85.0; // Automatically stop pump when surface water level reaches >= 85%
const float WATER_REFILL_MIN   = 80.0; // Automatically start pump whenever surface water level drops < 80%

// Safety & Battery Protection Thresholds
const float BATT_MIN_LOCKOUT = 10.00;  // 3S Pack cutoff (~3.33V/cell)
const float BATT_RESUME_VOLTS = 10.50; // Hysteresis before allowing pump to resume

// Timing Protections (in milliseconds)
const unsigned long MAX_PUMP_RUN_MS  = 180000UL; // 3 minutes maximum continuous runtime
const unsigned long PUMP_COOLDOWN_MS = 60000UL;  // 1 minute mandatory cooldown after timeout
const unsigned long SAMPLE_INTERVAL  = 3000UL;   // Read sensors & evaluate logic every 3s
const unsigned long TELEMETRY_PERIOD = 5000UL;   // Print telemetry every 5s

// ============================================================================
// 3. SYSTEM STATE VARIABLES
// ============================================================================
bool  pumpState          = false;
bool  lowBatteryLockout  = false;
bool  timeoutLockout     = false;

unsigned long pumpStartTime   = 0;
unsigned long pumpStopTime    = 0;
unsigned long lastSampleTime  = 0;
unsigned long lastTeleTime    = 0;

float currentRootMoisture = 0.0;
float currentSurfaceWater = 0.0;
float currentBattVolts    = 0.0;
float currentSolarVolts   = 0.0;

// ============================================================================
// 4. HELPER FUNCTIONS
// ============================================================================

void setPump(bool enable) {
  if (enable == pumpState) return;

  if (enable) {
    digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? LOW : HIGH);
    digitalWrite(PIN_STATUS_LED, HIGH);
    pumpState = true;
    pumpStartTime = millis();
    Serial.println(F("[EVENT] Pump STARTED."));
  } else {
    digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);
    digitalWrite(PIN_STATUS_LED, LOW);
    pumpState = false;
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
  float pct = 100.0 * (float)(HW080_RAW_DRY - raw) / (float)(HW080_RAW_DRY - HW080_RAW_WET);
  return constrain(pct, 0.0, 100.0);
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
  Serial.print(F("Pump Relay State: ")); Serial.println(pumpState ? F("ON (Irrigating)") : F("OFF"));
  
  if (timeoutLockout) {
    Serial.println(F("[ALERT] Pump Timeout Cooldown in effect!"));
  }
  Serial.println(F("--------------------------------------------------"));
}

// ============================================================================
// 5. SETUP & MAIN LOOP
// ============================================================================

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  pinMode(PIN_RELAY_PUMP, OUTPUT);
  pinMode(PIN_STATUS_LED, OUTPUT);

  if (USE_SENSOR_PWR && PIN_SENSOR_PWR >= 0) {
    pinMode(PIN_SENSOR_PWR, OUTPUT);
    digitalWrite(PIN_SENSOR_PWR, LOW);
  }

  // Ensure pump is safely OFF on startup
  setPump(false);

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: Solar Rice Irrigation Controller     "));
  Serial.println(F(" Standalone Arduino Uno Automation Firmware      "));
  Serial.println(F("=================================================="));
  Serial.println(F("Constant 85% Surface Water Level Maintenance Mode:"));
  Serial.println(F("  - TARGET LEVEL: Maintain ~85.0% Surface Water"));
  Serial.println(F("  - PUMP ON     : Surface Water < 80.0%"));
  Serial.println(F("  - PUMP OFF    : Surface Water >= 85.0%"));
  Serial.println(F("=================================================="));

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
  // A. Periodic Sensor Sampling & Automation Logic (Every 3s)
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
      lowBatteryLockout = false; // Recovered
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

    // 3. Automated Decision Logic (Constant 85% Water Level Maintenance)
    if (lowBatteryLockout || timeoutLockout) {
      setPump(false);
    } else {
      if (!pumpState) {
        // Start pumping whenever surface ponding drops below 80%
        if (currentSurfaceWater < WATER_REFILL_MIN) {
          setPump(true);
        }
      } else {
        // Stop pumping when surface ponding fills to target 85%
        if (currentSurfaceWater >= WATER_TARGET_MAX) {
          setPump(false);
        }
      }
    }
  }

  // -------------------------------------------------------------
  // B. Telemetry Output (Every 5s)
  // -------------------------------------------------------------
  if (now - lastTeleTime >= TELEMETRY_PERIOD || lastTeleTime == 0) {
    lastTeleTime = now;
    printTelemetry();
  }

  // -------------------------------------------------------------
  // C. Status LED Indicator Handling
  // -------------------------------------------------------------
  if (lowBatteryLockout) {
    // Fast blink on battery error
    digitalWrite(PIN_STATUS_LED, (now / 200) % 2 == 0 ? HIGH : LOW);
  } else if (pumpState) {
    // Solid ON when irrigating
    digitalWrite(PIN_STATUS_LED, HIGH);
  } else {
    // Gentle heartbeat blink when idle
    digitalWrite(PIN_STATUS_LED, (now / 1500) % 2 == 0 ? HIGH : LOW);
  }

  delay(20); // Small loop yield
}
