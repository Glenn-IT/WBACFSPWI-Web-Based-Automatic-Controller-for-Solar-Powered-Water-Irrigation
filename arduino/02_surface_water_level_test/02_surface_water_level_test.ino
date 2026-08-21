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

// HW-080 Calibration Constants:
// In typical HW-080 resistive probes:
// - Dry (Air / Dry Surface): High resistance -> AO voltage is HIGH (Raw ADC ~ 850 - 1023)
// - Submerged (Ponding / Flood): Low resistance -> AO voltage is LOW (Raw ADC ~ 250 - 450)
int HW080_RAW_DRY = 950;  // Raw ADC reading when probe is completely dry in air
int HW080_RAW_WET = 300;  // Raw ADC reading when probe is submerged in standing surface water

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

// Calculate 0% (dry) to 100% (flooded ponding) based on HW-080 inverted resistance
float calculateHW080Percent(int raw) {
  // Inverted mapping: lower raw ADC = higher moisture / deeper water
  float pct = 100.0 * (float)(HW080_RAW_DRY - raw) / (float)(HW080_RAW_DRY - HW080_RAW_WET);
  return constrain(pct, 0.0, 100.0);
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
  } else if (surfaceMoisturePct < 75.0) {
    Serial.println(F("OPTIMAL RICE PONDING DEPTH"));
  } else {
    Serial.println(F("MAX FLOOD LEVEL REACHED (Inhibit Irrigation)"));
  }

  delay(1000);
}
