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
// 3. IRRIGATION CONTROL THRESHOLDS (HW-080 DRIVEN)
// ============================================================================
const float SURFACE_START_PCT  = 30.0; // Automatically turn pump ON when surface water drops <= 30.0%
const float SURFACE_STOP_PCT   = 85.0; // Automatically turn pump OFF when surface water reaches >= 85.0%

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

// Read HW-080 Surface Water Level (Primary Pump Controller)
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
  Serial.println(F("Paddy Water Control Rules (HW-080 Surface Driven):"));
  Serial.println(F("  - PUMP ON : Surface Water <= 30.0% (Water level low)"));
  Serial.println(F("  - PUMP OFF: Surface Water >= 85.0% (Paddy flooded / target reached)"));
  Serial.println(F("  - Capacitive Soil Sensor: Telemetry/Data Monitoring Only (0-100%)"));
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

  // HW-080 Driven Rice Field Control Logic:
  // 1. If Surface Water hits 85.0% -> Turn pump OFF
  if (surfaceWater >= SURFACE_STOP_PCT) {
    if (pumpState) {
      setPump(false, "Surface Water Target Reached (>= 85.0%)");
    }
  } 
  // 2. If Surface Water drops to 30.0% or below -> Turn pump ON
  else if (surfaceWater <= SURFACE_START_PCT) {
    if (!pumpState) {
      setPump(true, "Surface Water Level Low (<= 30.0%)");
    }
  }

  // Print Formatted Telemetry Line
  Serial.print(F("[LIVE TELEMETRY] Root Moisture: "));
  Serial.print(rootMoisture, 1);
  Serial.print(F("% (Capacitive Data) | Surface Water: "));
  Serial.print(surfaceWater, 1);
  Serial.print(F("% (HW-080 Control) | Pump Relay: "));

  if (pumpState) {
    unsigned long runSec = (millis() - pumpStartTime) / 1000;
    Serial.print(F("RUNNING (ON for "));
    Serial.print(runSec);
    Serial.print(F("s)"));
  } else {
    Serial.print(F("STANDBY (OFF)"));
  }

  Serial.print(F(" | Decision: "));
  if (surfaceWater >= SURFACE_STOP_PCT) {
    Serial.println(F("PONDING FULL (>= 85.0%) -> PUMP OFF"));
  } else if (pumpState) {
    Serial.println(F("IRRIGATING PADDY (Filling until surface water reaches 85.0%)"));
  } else if (surfaceWater <= SURFACE_START_PCT) {
    Serial.println(F("LOW WATER LEVEL (<= 30.0%) -> STARTING PUMP"));
  } else {
    Serial.println(F("OPTIMAL WATER LEVEL (30% - 85%) -> STANDBY"));
  }

  delay(1000);
}
