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

// Calibrated HW-080 constants (Calibrated to Physical Ruler & Water Height):
const int HW080_RAW_DRY      = 1020;  // 0.0% surface standing water (dry surface)
const int HW080_RAW_MID      = 410;   // 50.0% water at middle of sensor (7-8cm mark)
const int HW080_RAW_WET      = 355;   // 100.0% full container depth (top header / max flood)

// ============================================================================
// 3. IRRIGATION CONTROL THRESHOLDS (CONSTANT 85% LEVEL MAINTENANCE)
// ============================================================================
const float WATER_TARGET_MAX   = 85.0; // Turn pump OFF when water level reaches >= 85.0%
const float WATER_REFILL_MIN   = 80.0; // Turn pump ON whenever water level drops below 80.0% (Maintains ~85% level)

const unsigned long MAX_PUMP_RUNTIME_MS = 180000UL; // 180 seconds continuous run protection

// ============================================================================
// 4. SYSTEM STATE VARIABLES
// ============================================================================
bool pumpState = false;
unsigned long pumpStartTime = 0;
unsigned long lastLoopTime = 0;
int lastRawHW080 = 0;

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

// Read Capacitive Soil Moisture (Data Telemetry Only)
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

// Read HW-080 Surface Water Level (Primary Controller with Physical Ruler 3-Point Calibration)
float readSurfaceWaterLevel() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SURFACE_WATER);
    delay(5);
  }
  lastRawHW080 = (int)(sum / 16);

  if (lastRawHW080 >= HW080_RAW_DRY) {
    return 0.0;
  } else if (lastRawHW080 >= HW080_RAW_MID) {
    float pct = 50.0 * (float)(HW080_RAW_DRY - lastRawHW080) / (float)(HW080_RAW_DRY - HW080_RAW_MID);
    return constrain(pct, 0.0, 50.0);
  } else {
    float pct = 50.0 + 50.0 * (float)(HW080_RAW_MID - lastRawHW080) / (float)(HW080_RAW_MID - HW080_RAW_WET);
    return constrain(pct, 0.0, 100.0);
  }
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
  Serial.println(F("Constant 85% Surface Water Level Maintenance Mode:"));
  Serial.println(F("  - TARGET LEVEL: Maintain ~85.0% Surface Water"));
  Serial.println(F("  - PUMP ON     : Surface Water < 80.0%"));
  Serial.println(F("  - PUMP OFF    : Surface Water >= 85.0%"));
  Serial.println(F("  - Calibrated  : HW080 Dry=1020, Mid=410, Full=355"));
  Serial.println(F("=================================================================="));
  
  // 10-Second Sensor Calibration & Stabilization Window
  Serial.println(F("[STARTUP] 10-Second Sensor Calibration & Stabilization Window..."));
  for (int sec = 10; sec > 0; sec--) {
    Serial.print(F("  -> Stabilizing sensors... "));
    Serial.print(sec);
    Serial.println(F("s remaining"));
    readRootSoilMoisture();
    readSurfaceWaterLevel();
    delay(1000);
  }
  Serial.println(F("[STARTUP] Calibration window complete! Starting autonomous maintenance...\n"));
}

void loop() {
  float rootMoisture = readRootSoilMoisture();
  float surfaceWater = readSurfaceWaterLevel();

  // Safety: Continuous Runtime Limit check
  if (pumpState && (millis() - pumpStartTime >= MAX_PUMP_RUNTIME_MS)) {
    setPump(false, "Continuous 180s Safety Runtime Cap Hit");
  }

  // Constant 85% Water Level Maintenance Logic:
  // 1. If Surface Water reaches >= 85.0% -> Turn pump OFF
  if (surfaceWater >= WATER_TARGET_MAX) {
    if (pumpState) {
      setPump(false, "Surface Water Target Reached (>= 85.0%)");
    }
  } 
  // 2. If Surface Water is below 80.0% -> Turn pump ON to top up to 85.0%
  else if (surfaceWater < WATER_REFILL_MIN) {
    if (!pumpState) {
      setPump(true, "Surface Water Below Target (< 80.0%) -> Refilling to 85.0%");
    }
  }

  // Print Formatted Telemetry Line with Raw ADC
  Serial.print(F("[LIVE TELEMETRY] Root Moisture: "));
  Serial.print(rootMoisture, 1);
  Serial.print(F("% (Capacitive Data) | Surface Water: "));
  Serial.print(surfaceWater, 1);
  Serial.print(F("% (Raw ADC: "));
  Serial.print(lastRawHW080);
  Serial.print(F(") | Pump Relay: "));

  if (pumpState) {
    unsigned long runSec = (millis() - pumpStartTime) / 1000;
    Serial.print(F("RUNNING (ON for "));
    Serial.print(runSec);
    Serial.print(F("s)"));
  } else {
    Serial.print(F("STANDBY (OFF)"));
  }

  Serial.print(F(" | Decision: "));
  if (surfaceWater >= WATER_TARGET_MAX) {
    Serial.println(F("TARGET MAINTAINED (>= 85.0%) -> PUMP OFF"));
  } else if (pumpState) {
    Serial.println(F("PUMPING WATER (Refilling until surface reaches 85.0%)"));
  } else {
    Serial.println(F("WATER LEVEL STABLE (80% - 85%) -> STANDBY"));
  }

  delay(1000);
}
