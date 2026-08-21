/*
 * WBACFSPWI — Test 05: 30W Solar Panel Voltage Monitor
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - 30W Solar Panel (Operating ~18V Vmp, Open Circuit ~21.6V Voc)
 *   - Resistor Voltage Divider: R3 = 100kΩ, R4 = 20kΩ (or 22kΩ)
 * 
 * Wiring:
 *   - Solar Panel (+) -> R3 (100kΩ)
 *   - Junction (R3 & R4) -> Arduino Analog Pin A3
 *   - R4 (20kΩ) -> Common GND
 * 
 * Formula:
 *   Divider Ratio = (100k + 20k) / 20k = 120 / 20 = 6.00
 *   V_solar = (ADC / 1023.0) * 5.0 * 6.00
 */

const int PIN_SOLAR_ANALOG = A3;
const float ARDUINO_VREF   = 5.00;     // Measure 5V rail with multimeter
const float SOLAR_DIVIDER  = 6.000;    // (100k + 20k) / 20k = 6.00

const float SOLAR_SUN_THRESHOLD = 12.0; // Voltage above which active charging occurs

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: 30W Solar Panel Monitor Test         "));
  Serial.println(F("=================================================="));
  Serial.println(F("Reading solar panel voltage on pin A3..."));
  delay(1000);
}

float readSolarVoltage() {
  long sum = 0;
  for (int i = 0; i < 32; i++) {
    sum += analogRead(PIN_SOLAR_ANALOG);
    delay(5);
  }
  float avgAdc = (float)sum / 32.0;
  float pinVoltage = (avgAdc / 1023.0) * ARDUINO_VREF;
  return pinVoltage * SOLAR_DIVIDER;
}

void loop() {
  float solarVolts = readSolarVoltage();

  Serial.print(F("[Solar Panel] Output: "));
  Serial.print(solarVolts, 2);
  Serial.print(F("V | Condition: "));

  if (solarVolts < 2.0) {
    Serial.println(F("NIGHT / DARK / DISCONNECTED"));
  } else if (solarVolts < SOLAR_SUN_THRESHOLD) {
    Serial.println(F("OVERCAST / LOW SUNLIGHT"));
  } else {
    Serial.println(F("SUNNY / ACTIVE HARVESTING"));
  }

  delay(1000);
}
