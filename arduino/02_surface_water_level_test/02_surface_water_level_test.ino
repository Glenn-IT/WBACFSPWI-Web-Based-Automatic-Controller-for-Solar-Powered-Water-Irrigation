/*
 * WBACFSPWI — Test 02: HW-080 Soil Moisture & Surface Water Level Sensor
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - HW-080 Moisture Sensor Module (Sensor Probe + LM393 Driver Board)
 * 
 * Role in Miniature Rice Field:
 *   - Measures surface moisture & standing water (ponding depth) on top of the rice soil.
 *   - Prevents over-flooding during automated irrigation cycles.
 * 
 * HW-080 Module Pinout:
 *   - VCC -> Star 5V Power Rail (Regulated 5.0V from Buck Converter)
 *   - GND -> Star Common Ground Rail (0V)
 *   - AO  -> Arduino Analog Pin A1 (Analog voltage representing moisture/water depth)
 *   - DO  -> (Optional) Digital threshold trigger from LM393 potentiometer
 *   - 2-Pin Header -> Connects to the HW-080 sensor probe tracks
 */

const int PIN_HW080_ANALOG = A1;
const int PIN_HW080_DIGITAL = 2; // Optional DO pin for threshold interrupt

// Calibrated HW-080 constants (Two-Stage Piecewise Curve for Resistive Sensor Physics):
const int HW080_RAW_DRY = 1020; // Stage 0: Probe in dry air (0.0% surface water)
const int HW080_RAW_TIP = 530;  // Stage 1: Water just wetting bottom tip (~20.0% entry level)
const int HW080_RAW_WET = 350;  // Stage 2: Probe at full target submergence (100.0% full ponding depth)

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  pinMode(PIN_HW080_DIGITAL, INPUT);

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: HW-080 Surface Moisture Sensor Test   "));
  Serial.println(F("=================================================="));
  Serial.println(F("Reading HW-080 on Analog Pin A1 & Digital Pin D2..."));
  delay(1000);
}

// 16-sample multi-read filter to eliminate surface ripple & electrical noise
int readRawHW080() {
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_HW080_ANALOG);
    delay(5);
  }
  return (int)(sum / 16);
}

// Piecewise resistance calibration to prevent tip wetting from reading as 70-100%
float calculateHW080Percent(int raw) {
  if (raw >= HW080_RAW_DRY) {
    return 0.0;
  } else if (raw >= HW080_RAW_TIP) {
    // Stage 1: Dry air to bottom tip wetting (0.0% -> 20.0%)
    float pct = 20.0 * (float)(HW080_RAW_DRY - raw) / (float)(HW080_RAW_DRY - HW080_RAW_TIP);
    return constrain(pct, 0.0, 20.0);
  } else {
    // Stage 2: Tip rising to full maximum submergence (20.0% -> 100.0%)
    float pct = 20.0 + 80.0 * (float)(HW080_RAW_TIP - raw) / (float)(HW080_RAW_TIP - HW080_RAW_WET);
    return constrain(pct, 0.0, 100.0);
  }
}

void loop() {
  int rawADC = readRawHW080();
  float voltage = (rawADC / 1023.0) * 5.0;
  float surfaceMoisturePct = calculateHW080Percent(rawADC);
  int digitalThreshold = digitalRead(PIN_HW080_DIGITAL);

  Serial.print(F("[HW-080 Sensor] Raw ADC: "));
  Serial.print(rawADC);
  Serial.print(F(" | Voltage: "));
  Serial.print(voltage, 2);
  Serial.print(F("V | Ponding Level: "));
  Serial.print(surfaceMoisturePct, 1);
  Serial.print(F("% | DO Pin: "));
  Serial.print(digitalThreshold == LOW ? F("TRIGGERED (WET)") : F("IDLE (DRY)"));
  Serial.print(F(" | Status: "));

  if (surfaceMoisturePct < 15.0) {
    Serial.println(F("NO STANDING WATER (Surface Dry)"));
  } else if (surfaceMoisturePct < 90.0) {
    Serial.println(F("OPTIMAL RICE PONDING DEPTH"));
  } else {
    Serial.println(F("MAX FLOOD LEVEL REACHED (Inhibit Irrigation)"));
  }

  delay(1000);
}
