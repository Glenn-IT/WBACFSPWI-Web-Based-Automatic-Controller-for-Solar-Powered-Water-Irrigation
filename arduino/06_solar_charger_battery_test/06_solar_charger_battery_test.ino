/*
 * WBACFSPWI — Test 06: Solar Charger & Battery Monitor Test
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - 30W Solar Panel (Operating ~18V Vmp, Open Circuit ~21.6V Voc)
 *   - 3S MPPT/PWM Solar Charge Controller / BMS Module
 *   - 3S 18650 Li-Ion Battery Pack (11.1V Nominal / 12.6V Full)
 *   - LM2596 Buck Converter (Step down 12V -> 5.00V Main Logic Power)
 *   - Resistor Dividers:
 *       - Battery Divider (Pin A2): R1 = 100kΩ, R2 = 33kΩ  (Factor = 4.0303)
 *       - Solar Divider   (Pin A3): R3 = 100kΩ, R4 = 20kΩ  (Factor = 6.0000)
 * 
 * Functions:
 *   - Monitors Solar Input Voltage (0 - 22V) on Pin A3
 *   - Monitors 3S Battery Charge/State Voltage (0 - 12.6V) on Pin A2
 *   - Analyzes Solar Harvesting & Battery Charge Condition
 *   - Telemetry via Serial at 115200 baud
 */

const int PIN_BATTERY_ANALOG = A2;
const int PIN_SOLAR_ANALOG   = A3;

const float ARDUINO_VREF     = 5.00;     // Calibrate 5V rail with multimeter
const float BATT_DIVIDER     = 4.0303;   // (100k + 33k) / 33k
const float SOLAR_DIVIDER    = 6.0000;   // (100k + 20k) / 20k

const float BATT_LOCKOUT_MIN = 10.00;   // Critical low voltage threshold
const float BATT_FULL_VOLTS  = 12.50;   // Full charge threshold
const float SOLAR_SUN_MIN    = 12.00;   // Minimum solar harvesting voltage

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: Test 06 — Solar Charger & Battery     "));
  Serial.println(F("=================================================="));
  Serial.println(F("Monitoring Solar Input (A3) & Battery Pack (A2)..."));
  delay(1000);
}

float readVoltage(int pin, float dividerFactor) {
  long sum = 0;
  for (int i = 0; i < 32; i++) {
    sum += analogRead(pin);
    delay(5);
  }
  float avgAdc = (float)sum / 32.0;
  float pinVolts = (avgAdc / 1023.0) * ARDUINO_VREF;
  return pinVolts * dividerFactor;
}

void loop() {
  float vBatt  = readVoltage(PIN_BATTERY_ANALOG, BATT_DIVIDER);
  float vSolar = readVoltage(PIN_SOLAR_ANALOG, SOLAR_DIVIDER);

  Serial.print(F("[TELEMETRY] Solar Panel: "));
  Serial.print(vSolar, 2);
  Serial.print(F("V | Battery: "));
  Serial.print(vBatt, 2);
  Serial.print(F("V | System Status: "));

  if (vBatt < BATT_LOCKOUT_MIN) {
    Serial.println(F("🔴 CRITICAL LOW BATTERY LOCKOUT! (<10.0V)"));
  } else if (vSolar >= SOLAR_SUN_MIN && vBatt < BATT_FULL_VOLTS) {
    Serial.println(F("⚡ ACTIVE SOLAR CHARGING (Harvesting >12V)"));
  } else if (vBatt >= BATT_FULL_VOLTS) {
    Serial.println(F("🟢 BATTERY FULLY CHARGED (~12.6V)"));
  } else if (vSolar < 2.0) {
    Serial.println(F("🌙 NIGHT / DISCHARGING ON BATTERY"));
  } else {
    Serial.println(F("🌥️ LOW SUNLIGHT / STANDBY HARVEST"));
  }

  delay(1500);
}
