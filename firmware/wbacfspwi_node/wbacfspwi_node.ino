/*
 * WBACFSPWI — ESP32 irrigation node
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 *
 * Talks to:
 *   GET  /api/device/pull-schedule.php   -> { "schedules": [ {id, label, start_time, duration_minutes, days_of_week[]} ] }
 *   POST /api/device/report.php          <- { soil_moisture, water_level, battery_voltage, solar_output, pump_state, schedule_id }
 * Both require header  X-API-Key: <DEVICE_API_KEY from config/device.php>
 *
 * HARDWARE (see the Hardware Build artifact for the wiring diagram)
 *   9 V 2 W panel -> 1N5819 blocking diode -> 6 V 4.5 Ah SLA
 *   Battery feeds the ESP32 VIN pin directly (onboard AMS1117 makes 3.3 V) and
 *   the pump directly. No charge controller and no buck converter in the path.
 *   Pump switched low-side by an IRLZ44N (220 R gate resistor, 10 k gate pulldown, 1N5819 flyback).
 *
 * NO HARDWARE PROTECTION: there is no charge controller and no low-voltage
 * disconnect. MIN_BATT_TO_PUMP below is the only thing protecting the battery
 * from a deep discharge, and the server-side overcharge alert
 * (ALERT_HIGH_BATTERY_VOLTS in config/device.php) is the only warning that the
 * panel has been left connected too long. Do not remove either.
 *
 * PIN NOTE: every analog pin here is on ADC1. ADC2 pins (GPIO 0,2,4,12-15,25-27)
 * cannot be read while WiFi is active — analogRead() returns garbage.
 *
 * Libraries: ArduinoJson (Benoit Blanchon), ESP32 board package.
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// ---------------------------------------------------------------- config
const char* SSID    = "YOUR_WIFI";
const char* PASS    = "YOUR_PASS";
// LAN IP of the machine running XAMPP, plus the path to public/
const char* HOST    = "http://192.168.1.10/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public";
const char* API_KEY = "dev-local-device-key";   // must match DEVICE_API_KEY in config/device.php

const long  TZ_OFFSET_SEC = 8 * 3600;           // UTC+8 (Philippines)

// ---------------------------------------------------------------- pins
const int PIN_PUMP     = 26;   // MOSFET gate, via 220 R
const int PIN_SOIL     = 34;   // HW-080 AO      (ADC1, input-only)
const int PIN_SOIL_PWR = 32;   // HW-080 VCC     (power-gated to slow probe corrosion)
const int PIN_VBAT     = 35;   // battery divider tap (ADC1)
const int PIN_VSOL     = 33;   // panel divider tap   (ADC1)

// The two dividers use different ratios because the two sources have different
// ranges. Trim both constants against a multimeter — resistors are +/-5%.
const float VBAT_DIV = 3.128;  // 100k + 47k  ->  7.2 V full charge reads 2.30 V
const float VSOL_DIV = 5.545;  // 100k + 22k  -> 10.5 V open circuit reads 1.89 V

// ---------------------------------------------------------------- calibration
// Read the raw values yourself: probe in dry air, then probe in a glass of water.
const int SOIL_DRY = 3100;     // raw ADC, dry
const int SOIL_WET = 1300;     // raw ADC, in water

// Safety interlocks
const float MIN_BATT_TO_PUMP = 5.6;    // 6 V SLA — the ONLY deep-discharge protection
const float SOIL_ALREADY_WET = 75.0;   // percent

// ---------------------------------------------------------------- state
struct Sched { int id; int startMin; int durMin; uint8_t days; };  // days bit0=mon .. bit6=sun
const int MAX_SCHED = 10;
Sched scheds[MAX_SCHED];
int   nSched = 0;

bool  pumpOn = false;
int   activeSchedId = 0;
unsigned long tReport = 0, tPull = 0;

const unsigned long REPORT_MS = 60000;    // POST every 60 s
const unsigned long PULL_MS   = 120000;   // resync schedules every 2 min

// ---------------------------------------------------------------- helpers

// tm_wday is 0=Sunday. The DB SET column orders mon..sun, so remap to bit0..bit6.
int dayBit(int tm_wday) {
  const int lut[] = {6, 0, 1, 2, 3, 4, 5};
  return 1 << lut[tm_wday];
}

float readMoisture() {
  digitalWrite(PIN_SOIL_PWR, HIGH);
  delay(500);                                   // let the probe settle
  long sum = 0;
  for (int i = 0; i < 16; i++) { sum += analogRead(PIN_SOIL); delay(10); }
  digitalWrite(PIN_SOIL_PWR, LOW);

  int raw = sum / 16;                           // resistive probe: dry = high, wet = low
  float pct = 100.0 * (SOIL_DRY - raw) / (float)(SOIL_DRY - SOIL_WET);
  return constrain(pct, 0.0, 100.0);
}

float readVolts(int pin, float ratio) {
  long mv = 0;
  for (int i = 0; i < 16; i++) mv += analogReadMilliVolts(pin);   // factory-calibrated
  return (mv / 16.0) / 1000.0 * ratio;
}

void pullSchedule() {
  HTTPClient http;
  http.begin(String(HOST) + "/api/device/pull-schedule.php");
  http.addHeader("X-API-Key", API_KEY);

  int code = http.GET();
  if (code != 200) {
    Serial.printf("pull-schedule failed: %d\n", code);
    http.end();
    return;
  }

  StaticJsonDocument<2048> doc;
  DeserializationError err = deserializeJson(doc, http.getString());
  http.end();
  if (err) { Serial.printf("json: %s\n", err.c_str()); return; }

  const char* names[] = {"mon","tue","wed","thu","fri","sat","sun"};
  nSched = 0;
  for (JsonObject s : doc["schedules"].as<JsonArray>()) {
    if (nSched >= MAX_SCHED) break;
    const char* st = s["start_time"];                 // "HH:MM"
    Sched &x = scheds[nSched];
    x.id       = s["id"] | 0;
    x.startMin = atoi(st) * 60 + atoi(st + 3);
    x.durMin   = s["duration_minutes"] | 0;
    x.days     = 0;
    for (JsonVariant d : s["days_of_week"].as<JsonArray>())
      for (int i = 0; i < 7; i++)
        if (strcmp(d.as<const char*>(), names[i]) == 0) x.days |= (1 << i);
    nSched++;
  }
  Serial.printf("schedules loaded: %d\n", nSched);
}

void sendReport(float soil, float vbat, float vsol) {
  HTTPClient http;
  http.begin(String(HOST) + "/api/device/report.php");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", API_KEY);

  StaticJsonDocument<256> d;
  d["soil_moisture"]   = round(soil * 10) / 10.0;
  d["battery_voltage"] = round(vbat * 100) / 100.0;
  d["solar_output"]    = round(vsol * 10) / 10.0;   // panel volts (see note below)
  d["pump_state"]      = pumpOn ? "on" : "off";
  if (pumpOn && activeSchedId) d["schedule_id"] = activeSchedId;
  // "water_level" intentionally omitted — no tank sensor in this build.

  String body;
  serializeJson(d, body);
  int code = http.POST(body);
  Serial.printf("report %d  %s\n", code, body.c_str());
  http.end();
}

// ---------------------------------------------------------------- setup / loop

void setup() {
  Serial.begin(115200);

  pinMode(PIN_PUMP, OUTPUT);      digitalWrite(PIN_PUMP, LOW);
  pinMode(PIN_SOIL_PWR, OUTPUT);  digitalWrite(PIN_SOIL_PWR, LOW);

  analogSetPinAttenuation(PIN_SOIL, ADC_11db);
  analogSetPinAttenuation(PIN_VBAT, ADC_11db);
  analogSetPinAttenuation(PIN_VSOL, ADC_11db);

  WiFi.mode(WIFI_STA);
  WiFi.begin(SSID, PASS);
  Serial.print("wifi");
  while (WiFi.status() != WL_CONNECTED) { delay(400); Serial.print("."); }
  Serial.printf(" ok  %s\n", WiFi.localIP().toString().c_str());

  configTime(TZ_OFFSET_SEC, 0, "pool.ntp.org", "time.nist.gov");
  struct tm t;
  while (!getLocalTime(&t)) { Serial.println("waiting for NTP..."); delay(500); }
  Serial.println(&t, "time: %Y-%m-%d %H:%M:%S");

  pullSchedule();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    digitalWrite(PIN_PUMP, LOW);   // fail safe: no network, no pumping
    pumpOn = false;
    WiFi.reconnect();
    delay(2000);
    return;
  }

  unsigned long now = millis();

  if (now - tPull > PULL_MS) { tPull = now; pullSchedule(); }

  struct tm t;
  if (!getLocalTime(&t)) { delay(1000); return; }
  int mins = t.tm_hour * 60 + t.tm_min;

  bool shouldRun = false;
  int  hitId = 0;
  for (int i = 0; i < nSched; i++) {
    if (!(scheds[i].days & dayBit(t.tm_wday))) continue;
    if (mins >= scheds[i].startMin && mins < scheds[i].startMin + scheds[i].durMin) {
      shouldRun = true;
      hitId = scheds[i].id;
      break;
    }
  }

  // TODO: pull-schedule.php does not yet expose the manual override state from the
  // `overrides` table. Until it does, the dashboard's Override button has no effect
  // on the hardware. Once the endpoint returns it, check the override here — it must
  // win over `shouldRun` before the interlocks below run.

  if (now - tReport > REPORT_MS || tReport == 0) {
    tReport = now;

    float soil = readMoisture();
    float vbat = readVolts(PIN_VBAT, VBAT_DIV);
    float vsol = readVolts(PIN_VSOL, VSOL_DIV);

    if (vbat < MIN_BATT_TO_PUMP || soil > SOIL_ALREADY_WET) shouldRun = false;

    pumpOn = shouldRun;
    activeSchedId = shouldRun ? hitId : 0;
    digitalWrite(PIN_PUMP, pumpOn ? HIGH : LOW);

    sendReport(soil, vbat, vsol);
  }

  delay(1000);
}

/*
 * NOTE on solar_output
 * --------------------
 * This sketch reports panel *voltage*, not watts — there is no current sensing in
 * this build. The dashboard column is DECIMAL(6,2) and unitless, so it displays
 * fine, but relabel it "Panel V" or add an INA219 (I2C on GPIO 21/22) inline with
 * the panel + lead if you need real watts.
 *
 * DEMO TIP: this is the field that sells the solar story to a panel of examiners.
 * Cover the panel with your hand and solar_output collapses toward 0 while
 * battery_voltage settles back to its ~6.3 V resting value; uncover it and both
 * jump within seconds. Two live numbers moving on the dashboard is a much more
 * convincing demonstration than any wattage rating.
 */
