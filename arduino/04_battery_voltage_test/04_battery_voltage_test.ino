/*
 * WBACFSPWI — Test 04: 3S 18650 Li-ion Battery Voltage Monitor
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - 3S 18650 Battery Pack (3 cells in series: 9.0V cutoff to 12.6V max)
 *   - Resistor Voltage Divider: R1 = 100kΩ, R2 = 33kΩ
 * 
 * Wiring:
 *   - Battery (+) -> R1 (100kΩ)
 *   - Junction (R1 & R2) -> Arduino Analog Pin A2
 *   - R2 (33kΩ) -> Common GND
 * 
 * Formula:
 *   Divider Ratio = (100k + 33k) / 33k = 133 / 33 = 4.0303
 *   V_batt = (ADC / 1023.0) * 5.0 * 4.0303
 */

const int PIN_BATTERY_ANALOG = A2;
const float ARDUINO_VREF     = 5.00;     // Measure your 5V rail with a DMM for exact calibration
const float BATTERY_DIVIDER  = 4.0303;   // (R1 + R2) / R2 -> (100k + 33k) / 33k

// Battery Health Thresholds for 3S Li-ion (4.2V max, 3.7V nominal, 3.3V low cutoff per cell)
const float BATT_FULL_VOLTS  = 12.60;
const float BATT_NOM_VOLTS   = 11.10;
const float BATT_LOW_VOLTS   = 10.00;    // Deep discharge protection threshold

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: 3S 18650 Battery Monitor Test        "));
  Serial.println(F("=================================================="));
  Serial.println(F("Reading battery pack voltage on pin A2..."));
  delay(1000);
}

float readBatteryVoltage() {
  long sum = 0;
  for (int i = 0; i < 32; i++) {
    sum += analogRead(PIN_BATTERY_ANALOG);
    delay(5);
  }
  float avgAdc = (float)sum / 32.0;
  float pinVoltage = (avgAdc / 1023.0) * ARDUINO_VREF;
  return pinVoltage * BATTERY_DIVIDER;
}

float calculateBatteryPercent(float volts) {
  // Approximate linear percentage from 10.0V (0%) to 12.6V (100%)
  if (volts <= BATT_LOW_VOLTS) return 0.0;
  if (volts >= BATT_FULL_VOLTS) return 100.0;
  return ((volts - BATT_LOW_VOLTS) / (BATT_FULL_VOLTS - BATT_LOW_VOLTS)) * 100.0;
}

void loop() {
  float battVolts = readBatteryVoltage();
  float battPercent = calculateBatteryPercent(battVolts);

  Serial.print(F("[Battery Monitor] Voltage: "));
  Serial.print(battVolts, 2);
  Serial.print(F("V | Charge: "));
  Serial.print(battPercent, 1);
  Serial.print(F("% | Status: "));

  if (battVolts < BATT_LOW_VOLTS) {
    Serial.println(F("LOW BATTERY! Pump Lockout Active"));
  } else if (battVolts < BATT_NOM_VOLTS) {
    Serial.println(F("MODERATE (Operational)"));
  } else {
    Serial.println(F("HEALTHY / FULL"));
  }

  delay(1000);
}
