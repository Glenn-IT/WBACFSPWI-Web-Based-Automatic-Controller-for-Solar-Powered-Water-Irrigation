/*
 * WBACFSPWI — Test 01: Capacitive Soil Moisture Sensor v1.2 (Root Zone)
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - Capacitive Soil Moisture Sensor v1.2 (analog output)
 * 
 * Wiring:
 *   - Sensor VCC  -> Arduino 5V (or D8 if power-gated)
 *   - Sensor GND  -> Arduino GND
 *   - Sensor AOUT -> Arduino Analog Pin A0
 * 
 * Calibration Procedure:
 *   1. Hold sensor dry in open air -> Note the RAW ADC value (typically ~520 - 750).
 *   2. Submerge sensor up to the white line in water -> Note the RAW ADC value (typically ~250 - 400).
 *   3. Update SOIL_AIR_RAW and SOIL_WATER_RAW constants below.
 */

const int PIN_SOIL_ANALOG = A0;
const int PIN_SOIL_POWER  = 8;    // Set to -1 if sensor is wired directly to 5V
const bool USE_POWER_GATE = true;

// Calibrated thresholds from physical sensor test
int SOIL_AIR_RAW   = 417;  // Raw value in completely dry air (0% moisture)
int SOIL_WATER_RAW = 153;  // Raw value fully submerged in water (100% moisture)

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  if (USE_POWER_GATE && PIN_SOIL_POWER >= 0) {
    pinMode(PIN_SOIL_POWER, OUTPUT);
    digitalWrite(PIN_SOIL_POWER, LOW);
  }

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: Root Zone Capacitive Soil Sensor Test "));
  Serial.println(F("=================================================="));
  Serial.println(F("Starting readings... Hold sensor in Air then Water."));
  delay(1000);
}

int readRawSoil() {
  if (USE_POWER_GATE && PIN_SOIL_POWER >= 0) {
    digitalWrite(PIN_SOIL_POWER, HIGH);
    delay(100); // Allow sensor oscillator to stabilize
  }

  // Oversample and average 16 readings for clean noise filtering
  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SOIL_ANALOG);
    delay(5);
  }

  if (USE_POWER_GATE && PIN_SOIL_POWER >= 0) {
    digitalWrite(PIN_SOIL_POWER, LOW);
  }

  return (int)(sum / 16);
}

float calculateMoisturePercent(int raw) {
  // Capacitive sensor: Higher raw voltage = drier; Lower raw voltage = wetter
  float percent = 100.0 * (float)(SOIL_AIR_RAW - raw) / (float)(SOIL_AIR_RAW - SOIL_WATER_RAW);
  return constrain(percent, 0.0, 100.0);
}

void loop() {
  int raw = readRawSoil();
  float voltage = (raw / 1023.0) * 5.0;
  float moisturePct = calculateMoisturePercent(raw);

  Serial.print(F("[Root Soil Sensor] Raw ADC: "));
  Serial.print(raw);
  Serial.print(F(" | Voltage: "));
  Serial.print(voltage, 2);
  Serial.print(F("V | Moisture: "));
  Serial.print(moisturePct, 1);
  Serial.print(F("% | Status: "));

  if (moisturePct < 30.0) {
    Serial.println(F("DRY (Needs Irrigation)"));
  } else if (moisturePct <= 70.0) {
    Serial.println(F("OPTIMAL MOISTURE"));
  } else {
    Serial.println(F("SATURATED / WET"));
  }

  delay(1000);
}
