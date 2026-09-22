/*
 * =========================================================================================
 * WBACFSPWI — Standalone ESP32 Prototype Firmware (Figure 4 Architecture)
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 * =========================================================================================
 * Hardware Configuration matching Figure 4:
 *   [1] Solar Panel (12V-18V) -> Solar Charge Controller PV Input (+/-)
 *   [2] 12V Battery -> Solar Charge Controller Battery Terminals (+/-)
 *   [3] Solar Charge Controller -> Solderless Breadboard Power Distribution
 *   [4] ESP32 Microcontroller (ESP-WROOM-32)
 *   [5] Soil Moisture Module (Comparator breakout + 2-Prong probe in soil)
 *   [6] 1-Channel 5V/3.3V Relay Module (Controls 12V Water Pump)
 *   [7] 12V DC Water Pump
 *   [8] Direct Smartphone Wi-Fi Link (Direct SoftAP + Station Web Backend)
 * =========================================================================================
 */

#include <WiFi.h>
#include <WebServer.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

// -----------------------------------------------------------------------------
// 1. PIN DEFINITIONS (Matching Breadboard Layout in Figure 4)
// -----------------------------------------------------------------------------
const int PIN_RELAY        = 26;  // Digital OUT: 1-channel Relay Module (IN)
const int PIN_SOIL_ANALOG  = 34;  // Analog IN: Soil Moisture AO (ADC1 - Safe with WiFi)
const int PIN_SOIL_DIGITAL = 32;  // Digital IN: Soil Moisture DO (Threshold comparator)
const int PIN_SOIL_POWER   = 25;  // Digital OUT: Power Gate for probe anti-corrosion
const int PIN_VBAT_DIV     = 35;  // Analog IN: 12V Battery divider tap (ADC1)
const int PIN_VSOL_DIV     = 33;  // Analog IN: Solar Panel divider tap (ADC1)
const int PIN_STATUS_LED   = 2;   // Onboard Blue Status LED

// Relay Trigger Logic (Most Arduino Relay modules are ACTIVE LOW: LOW = Engaged)
const int RELAY_ACTIVE_STATE = LOW;
const int RELAY_IDLE_STATE   = HIGH;

// -----------------------------------------------------------------------------
// 2. NETWORK & SERVER CONFIGURATION
// -----------------------------------------------------------------------------
// WiFi Station (Home/Lab WiFi to reach XAMPP Server)
const char* WIFI_STA_SSID = "YOUR_WIFI_SSID";
const char* WIFI_STA_PASS = "YOUR_WIFI_PASSWORD";

// XAMPP Host URL
const char* SERVER_HOST   = "http://192.168.1.10/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public";
const char* API_KEY       = "dev-local-device-key"; // Must match config/device.php

// SoftAP Hotspot (For direct smartphone connection in the field without a router)
const char* AP_SSID       = "WBACFSPWI-Prototype";
const char* AP_PASS       = "12345678"; // Min 8 characters

const long  TZ_OFFSET_SEC = 8 * 3600; // UTC+8 (Philippines Standard Time)

// -----------------------------------------------------------------------------
// 3. CALIBRATION & SAFETY THRESHOLDS
// -----------------------------------------------------------------------------
// Soil Probe ADC Range (ADC1 on ESP32 is 12-bit: 0 - 4095)
// Dry air gives ~3200-3500, full water submersion gives ~1200-1400
const int SOIL_RAW_DRY    = 3200;
const int SOIL_RAW_WET    = 1350;

// Voltage Dividers (Trim against multimeter)
// Battery: 100k / 33k divider = 4.03 factor
const float RATIO_VBAT    = 4.0303;
// Solar: 100k / 20k divider = 6.00 factor
const float RATIO_VSOL    = 6.0000;

// Autonomous Irrigation Thresholds (Hysteresis Band)
const float MOISTURE_REFILL_MIN = 40.0; // Refill triggered below 40.0% (PUMP ON)
const float MOISTURE_TARGET_MAX = 50.0; // Target reached at or above 50.0% (PUMP OFF)

// Battery Low-Voltage Protection
const float BATT_MIN_LOCKOUT    = 10.0; // Inhibits pump below 10.0V to protect battery
const float BATT_RESUME_VOLTS   = 10.5; // Clears lockout once battery recovers

// Timing Safety Interlocks
const unsigned long MIN_PUMP_RUN_MS   = 5000;   // 5s anti-splash minimum run
const unsigned long SETTLING_DELAY_MS = 10000;  // 10s wave settling delay
const unsigned long MAX_PUMP_RUN_MS   = 180000; // 3 minutes continuous run limit
const unsigned long REPORT_INTERVAL   = 10000;  // Send telemetry report every 10s

// -----------------------------------------------------------------------------
// 4. RUNTIME SYSTEM STATE
// -----------------------------------------------------------------------------
WebServer localServer(80);

bool  isPumpActive       = false;
bool  isBatteryLocked    = false;
bool  manualOverrideOn   = false;
bool  manualOverrideOff  = false;
unsigned long pumpStartTime   = 0;
unsigned long lastReportTime  = 0;
unsigned long settlingStartTime = 0;

float currentSoilMoisture = 0.0;
float currentBattVolts    = 12.4;
float currentSolarVolts   = 0.0;

// -----------------------------------------------------------------------------
// 5. HARDWARE MEASUREMENT FUNCTIONS
// -----------------------------------------------------------------------------
float readSoilMoisture() {
  // Power gate HIGH to turn on sensor
  digitalWrite(PIN_SOIL_POWER, HIGH);
  delay(150); // Let probe settle

  long sum = 0;
  for (int i = 0; i < 16; i++) {
    sum += analogRead(PIN_SOIL_ANALOG);
    delay(5);
  }
  // Turn off sensor power gate to prevent electrochemical degradation
  digitalWrite(PIN_SOIL_POWER, LOW);

  int raw = sum / 16;
  // Calculate percentage (Inverted: High raw = Dry, Low raw = Wet)
  float pct = 100.0 * (SOIL_RAW_DRY - raw) / (float)(SOIL_RAW_DRY - SOIL_RAW_WET);
  return constrain(pct, 0.0, 100.0);
}

float readCalibratedVoltage(int pin, float ratio) {
  long mvSum = 0;
  for (int i = 0; i < 16; i++) {
    mvSum += analogReadMilliVolts(pin);
    delay(5);
  }
  float pinVolts = (mvSum / 16.0) / 1000.0;
  return pinVolts * ratio;
}

void setPump(bool enable) {
  if (enable == isPumpActive) return;

  if (enable) {
    digitalWrite(PIN_RELAY, RELAY_ACTIVE_STATE);
    digitalWrite(PIN_STATUS_LED, HIGH);
    isPumpActive = true;
    pumpStartTime = millis();
    Serial.println("[PUMP] RELAY ENGAGED -> PUMP ON");
  } else {
    digitalWrite(PIN_RELAY, RELAY_IDLE_STATE);
    digitalWrite(PIN_STATUS_LED, LOW);
    isPumpActive = false;
    settlingStartTime = millis();
    Serial.println("[PUMP] RELAY RELEASED -> PUMP OFF");
  }
}

// -----------------------------------------------------------------------------
// 6. ONBOARD DIRECT SMARTPHONE WEB SERVER (Figure 4 Direct Link)
// -----------------------------------------------------------------------------
void handleRoot() {
  String html = "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
  html += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
  html += "<title>WBACFSPWI Prototype</title>";
  html += "<style>body{background:#0a0f1d;color:#f8fafc;font-family:sans-serif;text-align:center;padding:20px;}";
  html += ".card{background:#131c31;border:2px solid #334155;border-radius:16px;padding:20px;margin:15px auto;max-width:340px;}";
  html += ".dry{color:#ef4444;font-size:2.5rem;font-weight:bold;}";
  html += ".optimal{color:#10b981;font-size:2.5rem;font-weight:bold;}";
  html += ".badge-on{background:#10b981;color:#fff;padding:12px 28px;border-radius:12px;font-size:1.6rem;font-weight:bold;display:inline-block;}";
  html += ".badge-off{background:#334155;color:#94a3b8;padding:12px 28px;border-radius:12px;font-size:1.6rem;font-weight:bold;display:inline-block;}";
  html += ".btn{padding:10px 16px;margin:5px;border-radius:8px;border:none;font-weight:bold;cursor:pointer;}";
  html += ".btn-on{background:#10b981;color:#fff;}.btn-off{background:#ef4444;color:#fff;}.btn-auto{background:#0284c7;color:#fff;}";
  html += "</style></head><body>";
  html += "<h2>WBACFSPWI PROTOTYPE</h2>";
  html += "<p style='color:#94a3b8;font-size:0.85rem;'>Figure 4 Direct ESP32 Controller</p>";
  
  html += "<div class='card'>";
  html += "<div style='color:#94a3b8;font-size:0.8rem;text-transform:uppercase;'>Soil Moisture</div>";
  if (currentSoilMoisture < MOISTURE_REFILL_MIN) {
    html += "<div class='dry'>Dry</div>";
  } else {
    html += "<div class='optimal'>Optimal</div>";
  }
  html += "<div>" + String(currentSoilMoisture, 1) + "%</div>";
  html += "<hr style='border-color:#334155;margin:15px 0;'>";
  html += "<div style='color:#94a3b8;font-size:0.8rem;text-transform:uppercase;'>Water Pump</div>";
  if (isPumpActive) {
    html += "<div class='badge-on'>ON</div>";
  } else {
    html += "<div class='badge-off'>OFF</div>";
  }
  html += "</div>";

  html += "<div class='card' style='font-size:0.85rem;text-align:left;'>";
  html += "<div><b>12V Battery:</b> " + String(currentBattVolts, 2) + " V</div>";
  html += "<div><b>Solar Panel:</b> " + String(currentSolarVolts, 1) + " V</div>";
  html += "<div><b>Lockout:</b> " + String(isBatteryLocked ? "LOCKED (<10V)" : "CLEAR") + "</div>";
  html += "</div>";

  html += "<div class='card'>";
  html += "<div style='color:#94a3b8;font-size:0.8rem;margin-bottom:8px;'>MANUAL OVERRIDE</div>";
  html += "<a href='/override?cmd=on'><button class='btn btn-on'>PUMP ON</button></a>";
  html += "<a href='/override?cmd=off'><button class='btn btn-off'>PUMP OFF</button></a>";
  html += "<a href='/override?cmd=auto'><button class='btn btn-auto'>AUTO</button></a>";
  html += "</div>";

  html += "<p style='color:#64748b;font-size:0.75rem;'><a href='/' style='color:#38bdf8;'>Refresh</a> &bull; Auto-refreshes every 3s</p>";
  html += "<script>setTimeout(()=>location.reload(), 3000);</script>";
  html += "</body></html>";

  localServer.send(200, "text/html", html);
}

void handleOverride() {
  if (localServer.hasArg("cmd")) {
    String cmd = localServer.arg("cmd");
    if (cmd == "on") {
      manualOverrideOn = true;
      manualOverrideOff = false;
      setPump(true);
    } else if (cmd == "off") {
      manualOverrideOn = false;
      manualOverrideOff = true;
      setPump(false);
    } else {
      manualOverrideOn = false;
      manualOverrideOff = false;
    }
  }
  localServer.sendHeader("Location", "/");
  localServer.send(303);
}

// -----------------------------------------------------------------------------
// 7. BACKEND TELEMETRY INGESTION (XAMPP REST API)
// -----------------------------------------------------------------------------
void sendBackendTelemetry() {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(String(SERVER_HOST) + "/api/device/report.php");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", API_KEY);

  StaticJsonDocument<256> doc;
  doc["soil_moisture"]   = round(currentSoilMoisture * 10) / 10.0;
  doc["battery_voltage"] = round(currentBattVolts * 100) / 100.0;
  doc["solar_output"]    = round(currentSolarVolts * 10) / 10.0;
  doc["pump_state"]      = isPumpActive ? "on" : "off";

  String body;
  serializeJson(doc, body);
  int httpCode = http.POST(body);

  if (httpCode == 200) {
    String response = http.getString();
    StaticJsonDocument<256> respDoc;
    if (!deserializeJson(respDoc, response)) {
      if (respDoc.containsKey("command")) {
        const char* cmd = respDoc["command"];
        if (strcmp(cmd, "PUMP_ON") == 0) {
          manualOverrideOn = true;
          manualOverrideOff = false;
        } else if (strcmp(cmd, "PUMP_OFF") == 0) {
          manualOverrideOn = false;
          manualOverrideOff = true;
        } else if (strcmp(cmd, "PUMP_AUTO") == 0) {
          manualOverrideOn = false;
          manualOverrideOff = false;
        }
      }
    }
  }
  http.end();
}

// -----------------------------------------------------------------------------
// 8. ARDUINO SETUP & LOOP
// -----------------------------------------------------------------------------
void setup() {
  Serial.begin(115200);
  Serial.println("\n--- WBACFSPWI Figure 4 Prototype Node Starting ---");

  // Pin Configurations
  pinMode(PIN_RELAY, OUTPUT);
  digitalWrite(PIN_RELAY, RELAY_IDLE_STATE);

  pinMode(PIN_STATUS_LED, OUTPUT);
  digitalWrite(PIN_STATUS_LED, LOW);

  pinMode(PIN_SOIL_POWER, OUTPUT);
  digitalWrite(PIN_SOIL_POWER, LOW);

  pinMode(PIN_SOIL_DIGITAL, INPUT);

  // ADC Configurations (ADC1)
  analogSetPinAttenuation(PIN_SOIL_ANALOG, ADC_11db);
  analogSetPinAttenuation(PIN_VBAT_DIV, ADC_11db);
  analogSetPinAttenuation(PIN_VSOL_DIV, ADC_11db);

  // Start SoftAP Hotspot
  WiFi.mode(WIFI_AP_STA);
  WiFi.softAP(AP_SSID, AP_PASS);
  Serial.printf("[WiFi] SoftAP Started: %s (IP: %s)\n", AP_SSID, WiFi.softAPIP().toString().c_str());

  // Attempt Station connection
  WiFi.begin(WIFI_STA_SSID, WIFI_STA_PASS);
  Serial.printf("[WiFi] Connecting to Station '%s'...\n", WIFI_STA_SSID);

  // Start Onboard HTTP Server
  localServer.on("/", handleRoot);
  localServer.on("/override", handleOverride);
  localServer.begin();
  Serial.println("[HTTP] Local Prototype Web Server active.");
}

void loop() {
  localServer.handleClient();
  unsigned long now = millis();

  // Read sensors every 1 second
  static unsigned long lastSenseTime = 0;
  if (now - lastSenseTime >= 1000) {
    lastSenseTime = now;
    currentSoilMoisture = readSoilMoisture();
    currentBattVolts    = readCalibratedVoltage(PIN_VBAT_DIV, RATIO_VBAT);
    currentSolarVolts   = readCalibratedVoltage(PIN_VSOL_DIV, RATIO_VSOL);

    // Battery Lockout Logic
    if (currentBattVolts < BATT_MIN_LOCKOUT) {
      isBatteryLocked = true;
    } else if (currentBattVolts >= BATT_RESUME_VOLTS) {
      isBatteryLocked = false;
    }

    // Irrigation Decision Engine
    if (isBatteryLocked) {
      setPump(false); // Inhibit pump when battery is depleted
    } else if (manualOverrideOn) {
      setPump(true);
    } else if (manualOverrideOff) {
      setPump(false);
    } else {
      // Autonomous Mode
      if (currentSoilMoisture < MOISTURE_REFILL_MIN) {
        // Soil is Dry -> Start Irrigation
        setPump(true);
      } else if (currentSoilMoisture >= MOISTURE_TARGET_MAX) {
        // Enforce 5s anti-splash runtime before permitting shutoff
        if (now - pumpStartTime >= MIN_PUMP_RUN_MS) {
          setPump(false);
        }
      }

      // Maximum Continuous Runtime Interlock
      if (isPumpActive && (now - pumpStartTime >= MAX_PUMP_RUN_MS)) {
        Serial.println("[SAFETY] MAX CONTINUOUS PUMP RUN REACHED -> AUTO CUTOFF");
        setPump(false);
      }
    }
  }

  // Push telemetry report to backend
  if (now - lastReportTime >= REPORT_INTERVAL) {
    lastReportTime = now;
    sendBackendTelemetry();
  }
}
