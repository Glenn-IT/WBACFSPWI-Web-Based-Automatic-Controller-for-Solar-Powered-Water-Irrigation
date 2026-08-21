/*
 * WBACFSPWI — Test 03: Relay Module & DC Water Pump Test
 * 
 * Hardware:
 *   - Arduino Uno R3
 *   - 5V Optocoupled Relay Module
 *   - DC Submersible Water Pump
 * 
 * Relay Wiring:
 *   - Relay VCC -> Arduino 5V
 *   - Relay GND -> Arduino GND
 *   - Relay IN  -> Arduino Digital Pin D7
 * 
 * Pump Power Wiring:
 *   - Battery Positive (+) -> Relay COM (Common) terminal
 *   - Relay NO (Normally Open) -> DC Pump Positive (+) lead
 *   - Battery Negative (-) -> DC Pump Negative (-) lead & Arduino GND
 *   - Flyback diode (1N4007 / 1N5819) across pump (+ and -)
 * 
 * Test Function:
 *   Cycles the relay/pump ON for 4 seconds, then OFF for 6 seconds.
 */

const int PIN_RELAY_PUMP = 7;
const int PIN_STATUS_LED = 13;

// Set to true if relay activates on LOW signal (most 5V Arduino relay boards)
const bool RELAY_ACTIVE_LOW = true;

void setPumpState(bool turnOn) {
  if (turnOn) {
    digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? LOW : HIGH);
    digitalWrite(PIN_STATUS_LED, HIGH);
    Serial.println(F(">>> [PUMP STATE] ===> ON  (Water Pumping)"));
  } else {
    digitalWrite(PIN_RELAY_PUMP, RELAY_ACTIVE_LOW ? HIGH : LOW);
    digitalWrite(PIN_STATUS_LED, LOW);
    Serial.println(F("--- [PUMP STATE] ---> OFF (Pump Stopped)"));
  }
}

void setup() {
  Serial.begin(115200);
  while (!Serial) { delay(10); }

  pinMode(PIN_RELAY_PUMP, OUTPUT);
  pinMode(PIN_STATUS_LED, OUTPUT);

  // Default to safe OFF state at boot
  setPumpState(false);

  Serial.println(F("=================================================="));
  Serial.println(F(" WBACFSPWI: Relay & DC Water Pump Test           "));
  Serial.println(F("=================================================="));
  Serial.println(F("Testing relay cycle: 4s ON / 6s OFF..."));
  delay(2000);
}

void loop() {
  Serial.println(F("Activating Relay / Pump for 4 seconds..."));
  setPumpState(true);
  delay(4000);

  Serial.println(F("Deactivating Relay / Pump for 6 seconds..."));
  setPumpState(false);
  delay(6000);
}
