/*
 * WBACFSPWI — Test 07: Dual Sensor (Capacitive + HW-080) & Relay Pump Integration Test
 * 
 * Hardware Setup:
 *   - Arduino Uno R3
 *   - Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone Soil Moisture)
 *   - Pin D8: Capacitive Sensor Power Gate (VCC control to prevent corrosion)
 *   - Pin A1: HW-080 Sensor (Surface Standing Water / Ponding Depth)
 *   - Pin D7: 5V Relay Module (Switches 12V DC Water Pump)
 *   - Pin D13: Status / Heartbeat LED
 * 
 * Decision Logic (Rice Field Rules):
 *   1. PUMP TURNS ON if:
 *        - Root Soil Moisture < 50.0% (Soil needs irrigation)
 *        - AND Surface Water Level < 70.0% (Safe standing depth, not flooded)
 *   2. PUMP TURNS OFF if:
 *        - Surface Water Level >= 70.0% (Ponding safety cap reached -> FLOOD CUTOFF)
 *        - OR Root Soil Moisture >= 70.0% (Target root moisture satisfied)
 *        - OR Continuous runtime reaches MAX_PUMP_RUNTIME_SEC (180s safety cap)
 * 
 * Calibrated ADC Values (from Physical Tests):
 *   - Capacitive Soil Sensor:  SOIL_AIR_RAW = 417 (0%),   SOIL_WATER_RAW = 153 (100%)
 *   - HW-080 Surface Sensor:   HW080_RAW_DRY = 1019 (0%), HW080_RAW_WET = 508 (100%)
 */

// ============================================================================
// 1. PIN DEFINITIONS
// ============================================================================
const int PIN_ROOT_SOIL      = A0;  // Capacitive Analog Out
const int PIN_SENSOR_PWR     = 8;   // Capacitive VCC Power Gate
const int PIN_SURFACE_WATER  = A1;  // HW-080 Analog Out
const int PIN_RELAY_PUMP     = 7;   // 5V Relay IN (Active LOW)
const int PIN_STATUS_LED     = 13;  // Built-in LED

const bool RELAY_ACTIVE_LOW  = true;

// ============================================================================
// 2. CALIBRATION CONSTANTS
// ============================================================================
const int SOIL_AIR_RAW       = 417;   // 0% root moisture in dry air
const int SOIL_WATER_RAW     = 153;   // 100% root moisture submerged in water

const int HW080_RAW_DRY      = 1019;  // 0% surface standing water (dry surface)
const int HW080_RAW_WET      = 508;   // 100% full ponding depth (top of probe tracks)

// ============================================================================
// 3. IRRIGATION CONTROL THRESHOLDS
// ============================================================================
const float MOISTURE_START_PCT = 50.0; // Start pumping when root moisture drops below 50.0%
const float MOISTURE_STOP_PCT  = 70.0; // Stop pumping when root moisture reaches 70.0%
const float SURFACE_MAX_PCT    = 70.0; // Immediately stop pumping if surface water level reaches 70.0%

const unsigned long MAX_PUMP_RUNTIME_MS = 180000UL; // 180 seconds continuous run protection

// ============================================================================
// 4. SYSTEM STATE VARIABLES
// ============================================================================
bool pumpState = false;
unsigned long pumpStartTime = 0;
unsigned long lastLoopTime = 0;

void setPump(bool enable, const char* reason) {
  if (enable != pumpState) {
    pumpState = enable;
    if (pumpState) {
      pumpStartTime = millis();
      digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? LOW : HIGH);
      digitalWrite(PIN_STATUS_LED, HIGH);
      Serial.print(F(">>> [ACTION] PUMP STARTED | Reason: "));
      Serial.println(reason);
    } else {
      digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);
      digitalWrite(PIN_STATUS_LED, LOW);
      Serial.print(F(">>> [ACTION] PUMP STOPPED | Reason: "));
      Serial.println(reason);
    }
  }
}

// Read Capacitive Soil Moisture with Power Gating & 16-sample averaging
float readRootSoilMoisture() {
  digitalWrite(PIN_SENSOR_PWR, HIGH);
  delay(50); // Sensor stabilization

  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_ROOT_SOIL);
    delay(5);
  }
  digitalWrite(PIN_SENSOR_PWR, LOW);

  int raw = (int)(sum / 16);
  float pct = 100.0 * (float)(SOIL_AIR_RAW - raw) / (float)(SOIL_AIR_RAW - SOIL_WATER_RAW);
  return constrain(pct, 0.0, 100.0);
}

// Read HW-080 Surface Water Level with 16-sample averaging
float readSurfaceWaterLevel() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SURFACE_WATER);
    delay(5);
  }
  int raw = (int)(sum / 16);
  float pct = 100.0 * (float)(HW080_RAW_DRY - raw) / (float)(HW080_RAW_DRY - HW080_RAW_WET);
  return constrain(pct, 0.0, 100.0);
}

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  pinMode(PIN_SENSOR_PWR, OUTPUT);
  digitalWrite(PIN_SENSOR_PWR, LOW);

  pinMode(PIN_RELAY_PUMP, OUTPUT);
  digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW); // Ensure Pump is OFF initially

  pinMode(PIN_STATUS_LED, OUTPUT);
  digitalWrite(PIN_STATUS_LED, LOW);

  Serial.println(F("=================================================================="));
  Serial.println(F(" WBACFSPWI: Dual Sensor & Relay Pump Integrated Controller Test  "));
  Serial.println(F("=================================================================="));
  Serial.println(F("Rules:"));
  Serial.println(F("  - Root Moisture < 50.0% AND Surface Water < 70.0% -> PUMP ON"));
  Serial.println(F("  - Surface Water >= 70.0% OR Root Moisture >= 70.0% -> PUMP OFF"));
  Serial.println(F("=================================================================="));
  delay(1500);
}

void loop() {
  float rootMoisture = readRootSoilMoisture();
  float surfaceWater = readSurfaceWaterLevel();

  // Safety: Continuous Runtime Limit check
  if (pumpState && (millis() - pumpStartTime >= MAX_PUMP_RUNTIME_MS)) {
    setPump(false, "Continuous 180s Safety Runtime Cap Hit");
  }

  // Dual Sensor Decision Logic
  if (surfaceWater >= SURFACE_MAX_PCT) {
    // Cutoff Priority 1: Surface water is at or above 70% (Flood protection)
    if (pumpState) {
      setPump(false, "Surface Ponding Limit Reached (>= 70.0%)");
    }
  } else if (rootMoisture < MOISTURE_START_PCT && surfaceWater < SURFACE_MAX_PCT) {
    // Trigger: Root moisture below 50% and surface is not flooded
    if (!pumpState) {
      setPump(true, "Dry Root Zone (< 50.0%) & Safe Surface Depth");
    }
  } else if (rootMoisture >= MOISTURE_STOP_PCT) {
    // Cutoff Priority 2: Target root moisture reached (>= 70%)
    if (pumpState) {
      setPump(false, "Target Root Moisture Satisfied (>= 70.0%)");
    }
  }

  // Print Formatted Telemetry Line
  Serial.print(F("[LIVE TELEMETRY] Root Moisture: "));
  Serial.print(rootMoisture, 1);
  Serial.print(F("% (Capacitive) | Surface Water: "));
  Serial.print(surfaceWater, 1);
  Serial.print(F("% (HW-080) | Pump Relay: "));

  if (pumpState) {
    unsigned long runSec = (millis() - pumpStartTime) / 1000;
    Serial.print(F("RUNNING (ON for "));
    Serial.print(runSec);
    Serial.print(F("s)"));
  } else {
    Serial.print(F("STANDBY (OFF)"));
  }

  Serial.print(F(" | Decision: "));
  if (surfaceWater >= SURFACE_MAX_PCT) {
    Serial.println(F("SURFACE FLOOD CUTOFF (>= 70%)"));
  } else if (rootMoisture < MOISTURE_START_PCT) {
    Serial.println(F("IRRIGATING (Moisture < 50%)"));
  } else if (rootMoisture >= MOISTURE_STOP_PCT) {
    Serial.println(F("ADEQUATE MOISTURE (>= 70%)"));
  } else {
    Serial.println(F("NORMAL BUFFER ZONE (50% - 70%)"));
  }

  delay(1000);
}
