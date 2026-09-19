/*
 * WBACFSPWI — Test 11: JSN-SR04T Waterproof Ultrasonic Sensor Test & Calibration Suite
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - JSN-SR04T v2.0 / v3.0 Waterproof Ultrasonic Transducer & Transceiver Module
 * 
 * Purpose:
 *   - High-reliability, non-contact surface water depth (ponding level) measurement.
 *   - Replaces the legacy resistive HW-080 sensor to eliminate electrolytic corrosion,
 *     mineral scaling, and false 100% flood triggers caused by water splashing.
 *   - Provides interactive Serial Monitor calibration to configure container geometry:
 *       1. Sensor Clearance (Transducer face down to 100% full water mark)
 *       2. Total Usable Container Depth (Floor to 100% mark)
 * 
 * Pinout:
 *   - JSN-SR04T 5V   -> LM2596 Star 5.0V Power Rail
 *   - JSN-SR04T GND  -> Star Common Ground Rail
 *   - JSN-SR04T TRIG -> Arduino Pin A1 (Configured as Digital OUTPUT)
 *   - JSN-SR04T ECHO -> Arduino Pin A4 (Configured as Digital INPUT)
 * 
 * Note on Blind Zone:
 *   The JSN-SR04T physical blind zone is 20 cm - 25 cm. The transducer must be mounted
 *   at least 25 cm above the maximum allowable flood level (100% mark).
 */

// Pin Assignments
const int PIN_TRIG = A1; // Trigger pulse output (10µs pulse)
const int PIN_ECHO = A4; // Echo pulse input
const int PIN_LED  = 13; // Built-in activity indicator

// Physical Container Geometry Calibration Constants (in Centimeters)
// Calibrated from Live Bench Measurements:
// - Dry Soil Bed / Empty Base Distance = 28.0 cm (Depth: 0.0 cm, Level: 0.0%)
// - 50.0% Target Water Level Distance  = 22.4 cm (Depth: 5.6 cm, Level: 50.0%) -> Pump OFF
// - 45.0% Refill Water Level Distance  = 23.0 cm (Depth: 5.04 cm, Level: 45.0%) -> Pump ON
// - Total Usable Container Depth (100% scale) = 11.2 cm (5.6 cm * 2)
// - Sensor Clearance (Transducer to 100% mark) = 16.8 cm (28.0 cm - 11.2 cm)
float sensorClearanceCM = 16.8; // Air gap from transducer face to 100% full mark (28.0 - 11.2)
float containerDepthCM  = 11.2; // Calibrated usable water depth (5.6cm at 50% * 2)

// Physical Constants
const float SPEED_OF_SOUND_CM_US = 0.0343; // cm per microsecond at ~25°C
const float MIN_BLIND_ZONE_CM     = 20.0;   // Physical hardware limitation of JSN-SR04T

// Autonomous Irrigation Decision Thresholds (Harmonized with SYSTEM_MEMORY.md)
const float WATER_TARGET_MAX = 50.0; // % Pump shutoff target
const float WATER_REFILL_MIN = 45.0; // % Pump activation trigger

// Timing
unsigned long lastMeasureTime = 0;
const unsigned long MEASURE_INTERVAL_MS = 1000;

// Function Prototypes
float singlePingCM();
float readFilteredDistanceCM(int samples = 5);
float calculateWaterDepthCM(float distanceCM);
float calculateWaterPercent(float distanceCM);
void printHelpMenu();
void handleSerialCommands();
void runBurstDiagnostic();

void setup() {
  Serial.begin(115200);
  while (!Serial && millis() < 3000) { delay(10); }

  pinMode(PIN_TRIG, OUTPUT);
  pinMode(PIN_ECHO, INPUT);
  pinMode(PIN_LED,  OUTPUT);

  digitalWrite(PIN_TRIG, LOW);
  digitalWrite(PIN_LED,  LOW);

  Serial.println(F("\n============================================================"));
  Serial.println(F(" WBACFSPWI: JSN-SR04T Waterproof Ultrasonic Level Suite     "));
  Serial.println(F("============================================================"));
  Serial.println(F("Transducer non-contact surface water depth calibration tool."));
  Serial.print(F("• TRIG Pin: Arduino A1 (Digital Pin 15)\n"));
  Serial.print(F("• ECHO Pin: Arduino A4 (Digital Pin 18)\n"));
  Serial.print(F("• Baud Rate: 115200 baud\n"));
  Serial.println(F("------------------------------------------------------------"));
  Serial.print(F("Current Clearance (100% Mark): ")); Serial.print(sensorClearanceCM, 1); Serial.println(F(" cm"));
  Serial.print(F("Current Usable Depth:          ")); Serial.print(containerDepthCM, 1);  Serial.println(F(" cm"));
  Serial.print(F("Floor Distance (0% Empty):     ")); Serial.print(sensorClearanceCM + containerDepthCM, 1); Serial.println(F(" cm"));
  Serial.println(F("------------------------------------------------------------"));
  Serial.println(F("Type 'h' or '?' anytime in the Serial Monitor for command menu."));
  Serial.println(F("============================================================\n"));

  delay(1500);
}

void loop() {
  handleSerialCommands();

  unsigned long currentMillis = millis();
  if (currentMillis - lastMeasureTime >= MEASURE_INTERVAL_MS) {
    lastMeasureTime = currentMillis;

    digitalWrite(PIN_LED, HIGH);
    float rawDistance = readFilteredDistanceCM(5);
    digitalWrite(PIN_LED, LOW);

    if (rawDistance < 0.0) {
      Serial.println(F("[ERROR] Sensor Echo Timeout! Check 5V power, GND, and wiring integrity."));
      return;
    }

    float waterDepth = calculateWaterDepthCM(rawDistance);
    float waterPct   = calculateWaterPercent(rawDistance);

    // Header & Raw distance
    Serial.print(F("[ULTRASONIC] Dist: "));
    if (rawDistance < 10.0) Serial.print(' ');
    Serial.print(rawDistance, 1);
    Serial.print(F(" cm | Depth: "));
    if (waterDepth < 10.0 && waterDepth >= 0.0) Serial.print(' ');
    Serial.print(waterDepth, 1);
    Serial.print(F(" cm | Level: "));
    if (waterPct < 10.0) Serial.print(F("  "));
    else if (waterPct < 100.0) Serial.print(' ');
    Serial.print(waterPct, 1);
    Serial.print(F("% "));

    // Visual ASCII gauge bar
    Serial.print('[');
    int filledBars = (int)((waterPct / 100.0) * 20.0);
    filledBars = constrain(filledBars, 0, 20);
    for (int i = 0; i < 20; i++) {
      if (i < filledBars) Serial.print('=');
      else Serial.print(' ');
    }
    Serial.print(F("] "));

    // Safety and Irrigation Logic Evaluation
    if (rawDistance < MIN_BLIND_ZONE_CM) {
      Serial.println(F("⚠️ [BLIND ZONE ALERT: Object < 20cm! Mount sensor higher]"));
    } else if (waterPct < WATER_REFILL_MIN) {
      Serial.println(F("💧 [REFILL TRIGGER: Pump would turn ON (<45.0%)]"));
    } else if (waterPct >= WATER_TARGET_MAX) {
      Serial.println(F("🛑 [TARGET REACHED: Pump would turn OFF (>=50.0%)]"));
    } else {
      Serial.println(F("⚖️  [BUFFER ZONE: 45.0%-50.0% (Holding previous state)]"));
    }
  }
}

// Single acoustic pulse-echo time-of-flight measurement
float singlePingCM() {
  // Ensure trigger pin is low for clean high pulse
  digitalWrite(PIN_TRIG, LOW);
  delayMicroseconds(4);

  // Send 10 microsecond trigger pulse
  digitalWrite(PIN_TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(PIN_TRIG, LOW);

  // Measure round-trip echo pulse duration with 35ms timeout (~6m max)
  unsigned long duration = pulseIn(PIN_ECHO, HIGH, 35000UL);

  if (duration == 0) {
    return -1.0; // Echo timed out or obstructed
  }

  // Distance = (Time * Speed of Sound) / 2
  return (float)duration * SPEED_OF_SOUND_CM_US / 2.0;
}

// Multi-sample filtered distance reading to reject surface ripples and acoustic jitter
float readFilteredDistanceCM(int samples) {
  float readings[10];
  if (samples > 10) samples = 10;
  if (samples < 1)  samples = 1;

  int validCount = 0;
  for (int i = 0; i < samples; i++) {
    float d = singlePingCM();
    if (d > 0.0) {
      readings[validCount++] = d;
    }
    delay(25); // Brief acoustic dissipation pause between pings
  }

  if (validCount == 0) return -1.0;

  // Simple sorting for median extraction
  for (int i = 0; i < validCount - 1; i++) {
    for (int j = i + 1; j < validCount; j++) {
      if (readings[i] > readings[j]) {
        float temp = readings[i];
        readings[i] = readings[j];
        readings[j] = temp;
      }
    }
  }

  // Return median reading
  return readings[validCount / 2];
}

// Convert measured distance down to physical water depth in container
float calculateWaterDepthCM(float distanceCM) {
  if (distanceCM < 0.0) return 0.0;
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

// High-speed 10-sample acoustic stability diagnostic
void runBurstDiagnostic() {
  Serial.println(F("\n--- RUNNING 10-PING ACOUSTIC STABILITY DIAGNOSTIC ---"));
  float minVal = 9999.0;
  float maxVal = 0.0;
  float sum = 0.0;
  int success = 0;

  for (int i = 1; i <= 10; i++) {
    float d = singlePingCM();
    Serial.print(F("  Ping #")); Serial.print(i); Serial.print(F(": "));
    if (d > 0.0) {
      Serial.print(d, 2); Serial.println(F(" cm"));
      sum += d;
      if (d < minVal) minVal = d;
      if (d > maxVal) maxVal = d;
      success++;
    } else {
      Serial.println(F("TIMEOUT (No echo)"));
    }
    delay(40);
  }

  Serial.println(F("----------------------------------------------------"));
  if (success > 0) {
    float avg = sum / (float)success;
    float jitter = maxVal - minVal;
    Serial.print(F("Valid Pings: ")); Serial.print(success); Serial.println(F(" / 10"));
    Serial.print(F("Average:     ")); Serial.print(avg, 2); Serial.println(F(" cm"));
    Serial.print(F("Min:         ")); Serial.print(minVal, 2); Serial.println(F(" cm"));
    Serial.print(F("Max:         ")); Serial.print(maxVal, 2); Serial.println(F(" cm"));
    Serial.print(F("Jitter/Span: ")); Serial.print(jitter, 2); Serial.println(F(" cm"));
    if (jitter <= 1.0) {
      Serial.println(F("Result:      EXCELLENT STABILITY (Jitter <= 1.0cm)"));
    } else if (jitter <= 2.5) {
      Serial.println(F("Result:      ACCEPTABLE STABILITY (Minor surface ripples)"));
    } else {
      Serial.println(F("Result:      HIGH JITTER (Check for side-wall acoustic reflections)"));
    }
  } else {
    Serial.println(F("Result: FAILED — No valid pings returned."));
  }
  Serial.println(F("----------------------------------------------------\n"));
}

// Interactive Serial Command Handler
void handleSerialCommands() {
  if (!Serial.available()) return;

  char cmd = Serial.read();
  // Flush any trailing newline/carriage return characters
  while (Serial.available()) {
    char c = Serial.peek();
    if (c == '\r' || c == '\n') Serial.read();
    else break;
  }

  float currentDist = readFilteredDistanceCM(7);

  switch (cmd) {
    case 'c':
    case 'C':
      if (currentDist > 0.0) {
        sensorClearanceCM = currentDist;
        Serial.println(F("\n>>> [CALIBRATION] SENSOR CLEARANCE (100% Full Mark) SET!"));
        Serial.print(F("    New Sensor Clearance = ")); Serial.print(sensorClearanceCM, 2); Serial.println(F(" cm"));
        Serial.print(F("    Empty Floor Distance = ")); Serial.print(sensorClearanceCM + containerDepthCM, 2); Serial.println(F(" cm\n"));
      } else {
        Serial.println(F("\n>>> [ERROR] Cannot calibrate: No valid echo detected."));
      }
      break;

    case 'e':
    case 'E':
      if (currentDist > 0.0) {
        if (currentDist > sensorClearanceCM) {
          containerDepthCM = currentDist - sensorClearanceCM;
          Serial.println(F("\n>>> [CALIBRATION] CONTAINER FLOOR (0% Empty Mark) SET!"));
          Serial.print(F("    Current Distance to Floor = ")); Serial.print(currentDist, 2); Serial.println(F(" cm"));
          Serial.print(F("    Calculated Usable Depth   = ")); Serial.print(containerDepthCM, 2); Serial.println(F(" cm\n"));
        } else {
          Serial.println(F("\n>>> [ERROR] Floor distance must be greater than Clearance distance!"));
        }
      } else {
        Serial.println(F("\n>>> [ERROR] Cannot calibrate: No valid echo detected."));
      }
      break;

    case 't':
    case 'T':
      runBurstDiagnostic();
      break;

    case '+':
      sensorClearanceCM += 0.5;
      Serial.print(F("\n>>> Clearance adjusted: ")); Serial.print(sensorClearanceCM, 1); Serial.println(F(" cm\n"));
      break;

    case '-':
      if (sensorClearanceCM > 0.5) sensorClearanceCM -= 0.5;
      Serial.print(F("\n>>> Clearance adjusted: ")); Serial.print(sensorClearanceCM, 1); Serial.println(F(" cm\n"));
      break;

    case 'h':
    case 'H':
    case '?':
      printHelpMenu();
      break;

    default:
      // Ignore unhandled characters
      break;
  }
}

void printHelpMenu() {
  Serial.println(F("\n============================================================"));
  Serial.println(F("          JSN-SR04T CALIBRATION COMMAND MENU                "));
  Serial.println(F("============================================================"));
  Serial.println(F("  'c' / 'C' : Calibrate 100% Mark (Set Clearance to current dist)"));
  Serial.println(F("  'e' / 'E' : Calibrate 0% Floor  (Set Usable Depth to floor)"));
  Serial.println(F("  't' / 'T' : Run 10-ping acoustic stability burst test"));
  Serial.println(F("  '+'       : Increase clearance offset by +0.5 cm"));
  Serial.println(F("  '-'       : Decrease clearance offset by -0.5 cm"));
  Serial.println(F("  'h' / '?' : Display this help and calibration summary"));
  Serial.println(F("------------------------------------------------------------"));
  Serial.print(F("Active Settings:\n"));
  Serial.print(F("  • Clearance (100% line): ")); Serial.print(sensorClearanceCM, 2); Serial.println(F(" cm"));
  Serial.print(F("  • Usable Depth:          ")); Serial.print(containerDepthCM, 2);  Serial.println(F(" cm"));
  Serial.print(F("  • Empty Floor Distance:  ")); Serial.print(sensorClearanceCM + containerDepthCM, 2); Serial.println(F(" cm"));
  Serial.println(F("============================================================\n"));
}
