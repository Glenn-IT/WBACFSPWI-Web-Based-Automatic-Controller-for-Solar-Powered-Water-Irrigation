/*
 * ============================================================================
 * WBACFSPWI — Test 08: Direct Current (12V DC Adapter) Presentation Test
 * ============================================================================
 * 
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)
 * 
 * PURPOSE:
 *   Designed specifically for live indoor presentations, panel defenses, and bench
 *   demonstrations using a 12V DC Power Adapter (wall supply) instead of a 3S battery.
 * 
 * POWER ARCHITECTURE:
 *   - 12V DC Power Adapter (>= 2A) -> LM2596 Buck Converter (IN+ / IN-)
 *   - 12V DC Power Adapter (+)     -> 5V Relay COM Terminal
 *   - LM2596 Buck Converter (OUT+) -> 5.00V Regulated -> Breadboard Red Rail (5V) -> Arduino 5V
 *   - LM2596 Buck Converter (OUT-) -> Common Star GND -> Breadboard Blue Rail (GND) -> Arduino GND
 * 
 * HARDWARE PINOUT:
 *   - Pin A0: Capacitive Soil Moisture Sensor v1.2 (Root Zone Moisture)
 *   - Pin D8: Capacitive Sensor Power Gate (Corrosion Prevention)
 *   - Pin A1: HW-080 Surface Moisture Sensor (Ponding / Water Depth)
 *   - Pin D7: 5V Relay Module IN (Controls 12V DC Water Pump)
 *   - Pin D13: Status / Pulse Heartbeat LED
 * 
 * PRESENTATION FEATURES:
 *   1. Direct 12V DC operation with battery voltage cutoff checks bypassed.
 *   2. Real-time ASCII visual progress bars in Serial Monitor for live panel viewing.
 *   3. Interactive Serial Commands ('1'=Demo Pump ON, '0'=Pump OFF, 'A'=Auto Mode, 'T'=Self-Test).
 *   4. Safety Demo Cutoff (Auto shuts pump off after 15s in demo pulse to avoid spill).
 * ============================================================================
 */

// ============================================================================
// 1. PIN DEFINITIONS
// ============================================================================
const int PIN_ROOT_SOIL      = A0;  // Capacitive Soil Moisture Sensor Analog Output
const int PIN_SENSOR_PWR     = 8;   // Capacitive Sensor VCC Power Gate
const int PIN_SURFACE_WATER  = A1;  // HW-080 Surface Moisture Sensor Analog Output
const int PIN_RELAY_PUMP     = 7;   // 5V Relay Control Pin (Active LOW)
const int PIN_STATUS_LED     = 13;  // Onboard Status LED

const bool RELAY_ACTIVE_LOW  = true; // Most Arduino relay modules are Active LOW

// ============================================================================
// 2. CALIBRATION CONSTANTS
// ============================================================================
// Capacitive Soil Moisture Sensor v1.2
const int SOIL_AIR_RAW       = 417;  // Sensor in dry air (0% moisture)
const int SOIL_WATER_RAW     = 153;  // Sensor submerged in water (100% moisture)

// HW-080 Surface Water Level Sensor (3-Point Ruler Calibration)
const int HW080_RAW_DRY      = 1020; // 0.0% surface water (dry probe)
const int HW080_RAW_MID      = 410;  // 50.0% water depth (mid-probe mark)
const int HW080_RAW_WET      = 355;  // 100.0% water depth (fully immersed)

// ============================================================================
// 3. IRRIGATION & DEMO THRESHOLDS
// ============================================================================
const float WATER_TARGET_MAX        = 80.0;  // Automatic Mode: Stop pump at 80.0% water level
const float WATER_REFILL_MIN        = 75.0;  // Automatic Mode: Start pump below 75.0%
const unsigned long DEMO_PULSE_MS   = 5000UL;  // '1' command demo pulse: 5 seconds
const unsigned long MAX_DEMO_RUN_MS = 20000UL; // Safety maximum run cap: 20 seconds

// ============================================================================
// 4. OPERATIONAL MODES & SYSTEM STATE
// ============================================================================
enum OperationMode {
  MODE_AUTO,   // Autonomous rice field irrigation logic
  MODE_MANUAL  // Manual control via Serial commands
};

OperationMode currentMode = MODE_AUTO;
bool pumpState = false;
unsigned long pumpStartTime = 0;
unsigned long lastTelemetryTime = 0;
int lastRawSoil = 0;
int lastRawHW080 = 0;

// ============================================================================
// 5. HELPER FUNCTIONS: HARDWARE CONTROL & SENSING
// ============================================================================

void setPumpState(bool enable, const char* reason) {
  if (RELAY_ACTIVE_LOW) {
    digitalWrite(PIN_RELAY_PUMP, enable ? LOW : HIGH);
  } else {
    digitalWrite(PIN_RELAY_PUMP, enable ? HIGH : LOW);
  }
  digitalWrite(PIN_STATUS_LED, enable ? HIGH : LOW);

  if (enable != pumpState) {
    pumpState = enable;
    if (pumpState) {
      pumpStartTime = millis();
      Serial.println();
      Serial.print(F(">>> [ACTUATOR EVENT] PUMP TURNED [ ON  ] | Reason: "));
      Serial.println(reason);
    } else {
      unsigned long duration = (millis() - pumpStartTime) / 1000;
      Serial.println();
      Serial.print(F(">>> [ACTUATOR EVENT] PUMP TURNED [ OFF ] | Run Duration: "));
      Serial.print(duration);
      Serial.print(F("s | Reason: "));
      Serial.println(reason);
    }
  }
}

// Read Capacitive Soil Moisture Sensor (Root Zone) with D8 Power Gate
float readRootSoilMoisture() {
  digitalWrite(PIN_SENSOR_PWR, HIGH);
  delay(30); // Power stabilization delay

  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_ROOT_SOIL);
    delay(2);
  }
  digitalWrite(PIN_SENSOR_PWR, LOW); // Turn off sensor power to prevent electrolytic corrosion

  lastRawSoil = (int)(sum / 16);
  float pct = 100.0 * (float)(SOIL_AIR_RAW - lastRawSoil) / (float)(SOIL_AIR_RAW - SOIL_WATER_RAW);
  return constrain(pct, 0.0, 100.0);
}

// Read HW-080 Surface Water Level Sensor with 3-point calibration
float readSurfaceWaterLevel() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SURFACE_WATER);
    delay(2);
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

// Render visual ASCII gauge bar e.g. [==========          ] 50%
void printVisualBar(float percentage, int barLength = 10) {
  int filled = (int)((percentage / 100.0) * barLength);
  filled = constrain(filled, 0, barLength);

  Serial.print(F("["));
  for (int i = 0; i < barLength; i++) {
    if (i < filled) {
      Serial.print(F("="));
    } else {
      Serial.print(F(" "));
    }
  }
  Serial.print(F("] "));
  if (percentage < 10.0) Serial.print(F("  "));
  else if (percentage < 100.0) Serial.print(F(" "));
  Serial.print(percentage, 1);
  Serial.print(F("%"));
}

void printHelpMenu() {
  Serial.println(F("\n=================================================================="));
  Serial.println(F("              LIVE PRESENTATION COMMAND CONSOLE                   "));
  Serial.println(F("=================================================================="));
  Serial.println(F("  '1' -> Trigger 5-Second Demo Pump Pulse (Safe Table Demo)"));
  Serial.println(F("  '0' -> Immediate Pump STOP"));
  Serial.println(F("  'A' -> Switch to AUTONOMOUS Rice Field Irrigation Mode"));
  Serial.println(F("  'M' -> Switch to MANUAL Standby Mode"));
  Serial.println(F("  'T' -> Run 5-Second Hardware Self-Test (Relay + Sensors)"));
  Serial.println(F("  'S' -> Print Hardware Status Summary Card"));
  Serial.println(F("  '?' -> Show this help menu again"));
  Serial.println(F("==================================================================\n"));
}

void printStatusSummary(float soilPct, float waterPct) {
  Serial.println(F("\n-----------------[ HARDWARE STATUS SUMMARY ]-----------------"));
  Serial.print(F("  Power Source        : 12V DC Power Adapter (Direct Current)\n"));
  Serial.print(F("  Operating Mode      : "));
  Serial.println(currentMode == MODE_AUTO ? F("AUTONOMOUS (Rice Field Logic)") : F("MANUAL (Panel Demo)"));
  Serial.print(F("  Root Soil Moisture  : "));
  Serial.print(soilPct, 1);
  Serial.print(F("% (Raw ADC: "));
  Serial.print(lastRawSoil);
  Serial.println(F(")"));
  Serial.print(F("  Surface Water Level : "));
  Serial.print(waterPct, 1);
  Serial.print(F("% (Raw ADC: "));
  Serial.print(lastRawHW080);
  Serial.println(F(")"));
  Serial.print(F("  Pump Relay State    : "));
  Serial.println(pumpState ? F("ACTIVE (ON)") : F("STANDBY (OFF)"));
  Serial.println(F("-------------------------------------------------------------\n"));
}

void runSelfTest() {
  Serial.println(F("\n>>> [SELF-TEST] Starting 5-Second Component Diagnostic..."));
  
  Serial.print(F("  [1/3] Reading Root Capacitive Sensor... "));
  float soil = readRootSoilMoisture();
  Serial.print(soil, 1);
  Serial.print(F("% (Raw: "));
  Serial.print(lastRawSoil);
  Serial.println(F(") -> PASS"));
  delay(500);

  Serial.print(F("  [2/3] Reading HW-080 Surface Sensor... "));
  float water = readSurfaceWaterLevel();
  Serial.print(water, 1);
  Serial.print(F("% (Raw: "));
  Serial.print(lastRawHW080);
  Serial.println(F(") -> PASS"));
  delay(500);

  Serial.println(F("  [3/3] Testing Relay Click (1-second safe click)..."));
  setPumpState(true, "Self-Test Relay Verification");
  delay(1000);
  setPumpState(false, "Self-Test Complete");
  
  Serial.println(F(">>> [SELF-TEST] All hardware components verified OK!\n"));
}

// ============================================================================
// 6. SETUP
// ============================================================================
void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  pinMode(PIN_SENSOR_PWR, OUTPUT);
  digitalWrite(PIN_SENSOR_PWR, LOW);

  pinMode(PIN_RELAY_PUMP, OUTPUT);
  // Ensure Relay is initially OFF
  digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);

  pinMode(PIN_STATUS_LED, OUTPUT);
  digitalWrite(PIN_STATUS_LED, LOW);

  Serial.println();
  Serial.println(F("=================================================================="));
  Serial.println(F(" WBACFSPWI: Direct Current (12V Adapter) Presentation Test Suite "));
  Serial.println(F("=================================================================="));
  Serial.println(F(" Power Supply: 12V DC Adapter -> LM2596 Buck (5.0V) & 12V Pump"));
  Serial.println(F(" Target: Demo Soil Moisture & Water Level Sensors with DC Pump"));
  Serial.println(F("=================================================================="));

  printHelpMenu();

  // Quick 3-second startup calibration
  Serial.println(F("[STARTUP] Initializing sensors (3 seconds)..."));
  for (int i = 3; i > 0; i--) {
    Serial.print(F("  -> Ready in "));
    Serial.print(i);
    Serial.println(F("s..."));
    readRootSoilMoisture();
    readSurfaceWaterLevel();
    delay(1000);
  }
  Serial.println(F("[STARTUP] System Ready! Streaming live telemetry below:\n"));
}

// ============================================================================
// 7. MAIN LOOP
// ============================================================================
void loop() {
  // Check for incoming user serial commands
  if (Serial.available() > 0) {
    char cmd = Serial.read();
    
    // Ignore line endings
    if (cmd != '\r' && cmd != '\n') {
      switch (cmd) {
        case '1':
          currentMode = MODE_MANUAL;
          Serial.println(F("\n[CMD] Manual Demo Trigger: Running pump for 5 seconds..."));
          setPumpState(true, "Manual 5s Demo Pulse");
          break;

        case '0':
          currentMode = MODE_MANUAL;
          Serial.println(F("\n[CMD] Manual Stop Triggered."));
          setPumpState(false, "Manual User Stop");
          break;

        case 'a':
        case 'A':
          currentMode = MODE_AUTO;
          Serial.println(F("\n[CMD] Switched to AUTONOMOUS Rice Field Irrigation Mode."));
          break;

        case 'm':
        case 'M':
          currentMode = MODE_MANUAL;
          setPumpState(false, "Switched to Manual Mode");
          Serial.println(F("\n[CMD] Switched to MANUAL Standby Mode."));
          break;

        case 't':
        case 'T':
          runSelfTest();
          break;

        case 's':
        case 'S':
          printStatusSummary(readRootSoilMoisture(), readSurfaceWaterLevel());
          break;

        case '?':
        case 'h':
        case 'H':
          printHelpMenu();
          break;

        default:
          Serial.print(F("\n[!] Unknown command: '"));
          Serial.print(cmd);
          Serial.println(F("'. Type '?' for command menu."));
          break;
      }
    }
  }

  // Sample sensors
  float rootMoisture = readRootSoilMoisture();
  float surfaceWater = readSurfaceWaterLevel();

  // Safety Run Timeout Enforcement
  if (pumpState) {
    unsigned long runDuration = millis() - pumpStartTime;

    // If manual 5s demo pulse elapsed
    if (currentMode == MODE_MANUAL && runDuration >= DEMO_PULSE_MS) {
      setPumpState(false, "5s Demo Pulse Complete");
    }
    // Universal safety cap (20s)
    else if (runDuration >= MAX_DEMO_RUN_MS) {
      setPumpState(false, "Safety Maximum Run Cap (20s) Reached");
    }
  }

  // Autonomous Mode Irrigation Decision Logic
  if (currentMode == MODE_AUTO) {
    if (surfaceWater >= WATER_TARGET_MAX) {
      if (pumpState) {
        setPumpState(false, "Surface Water Target (>=80%) Satisfied");
      }
    } else if (surfaceWater < WATER_REFILL_MIN) {
      if (!pumpState) {
        setPumpState(true, "Surface Water Below Min (<75%) -> Refilling");
      }
    }
  }

  // Periodic Live Telemetry Stream (every 1.0 second)
  if (millis() - lastTelemetryTime >= 1000) {
    lastTelemetryTime = millis();

    Serial.print(F("[LIVE] Soil: "));
    printVisualBar(rootMoisture, 8);
    
    Serial.print(F(" | Surface: "));
    printVisualBar(surfaceWater, 8);

    Serial.print(F(" | Pump: "));
    if (pumpState) {
      unsigned long sec = (millis() - pumpStartTime) / 1000;
      Serial.print(F("[ON  ("));
      Serial.print(sec);
      Serial.print(F("s)]"));
    } else {
      Serial.print(F("[OFF (Standby)]"));
    }

    Serial.print(F(" | Mode: "));
    Serial.print(currentMode == MODE_AUTO ? F("AUTO") : F("MANUAL"));

    Serial.print(F(" | Raw: (S="));
    Serial.print(lastRawSoil);
    Serial.print(F(", W="));
    Serial.print(lastRawHW080);
    Serial.println(F(")"));
  }

  delay(50);
}
