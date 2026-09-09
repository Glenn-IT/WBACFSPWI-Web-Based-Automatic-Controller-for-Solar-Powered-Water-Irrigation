/*
 * WBACFSPWI — NodeMCU ESP8266MOD Production Telemetry Bridge Node
 * Web-Based Automatic Controller for Solar-Powered Water Irrigation
 *
 * Hardware:
 *   - NodeMCU v2 / v3 (ESP-12E / CP2102)
 *   - Arduino Uno R3 Main Controller
 *
 * Talks to:
 *   GET  /api/device/pull-schedule.php   -> { "schedules": [ ... ] }
 *   POST /api/device/report.php          <- { soil_moisture, water_level, battery_voltage, solar_output, pump_state }
 * Requires Header: X-API-Key: <DEVICE_API_KEY from config/device.php>
 */

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SoftwareSerial.h>
#include <ArduinoJson.h>

// ---------------------------------------------------------------- Network Config
const char* WIFI_SSID   = "YOUR_WIFI_SSID";
const char* WIFI_PASS   = "YOUR_WIFI_PASSWORD";
const char* SERVER_HOST = "http://192.168.1.10/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public";
const char* API_KEY     = "dev-local-device-key"; // Matches DEVICE_API_KEY in config/device.php

// ---------------------------------------------------------------- Pins
const int PIN_SW_RX = D1; // GPIO5 <- Arduino Pin 10 (TX) via 1k/2k divider
const int PIN_SW_TX = D2; // GPIO4 -> Arduino Pin 9 (RX)
const int PIN_LED   = LED_BUILTIN; // GPIO2 / D4 (Active LOW)

SoftwareSerial arduinoSerial(PIN_SW_RX, PIN_SW_TX);

// ---------------------------------------------------------------- State
unsigned long lastPullTime = 0;
const unsigned long PULL_INTERVAL = 60000UL; // Sync schedules every 60s

void setup() {
  Serial.begin(115200);
  arduinoSerial.begin(9600);

  pinMode(PIN_LED, OUTPUT);
  digitalWrite(PIN_LED, HIGH);

  Serial.println(F("\n[WBACFSPWI] NodeMCU ESP8266 Bridge Initializing..."));

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  while (WiFi.status() != WL_CONNECTED) {
    delay(400);
    Serial.print(F("."));
  }

  Serial.println();
  Serial.print(F("[WiFi] Connected! IP: "));
  Serial.println(WiFi.localIP());

  // Blink LED to indicate successful connection
  for (int i = 0; i < 3; i++) {
    digitalWrite(PIN_LED, LOW); delay(80);
    digitalWrite(PIN_LED, HIGH); delay(80);
  }
}

void forwardReport(const String& payload) {
  if (WiFi.status() != WL_CONNECTED) return;

  WiFiClient client;
  HTTPClient http;

  http.begin(client, String(SERVER_HOST) + "/api/device/report.php");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", API_KEY);

  int code = http.POST(payload);
  if (code == 200) {
    digitalWrite(PIN_LED, LOW); delay(50); digitalWrite(PIN_LED, HIGH);
  } else {
    Serial.printf("[HTTP] Report error code: %d\n", code);
  }
  http.end();
}

void pullSchedule() {
  if (WiFi.status() != WL_CONNECTED) return;

  WiFiClient client;
  HTTPClient http;

  http.begin(client, String(SERVER_HOST) + "/api/device/pull-schedule.php");
  http.addHeader("X-API-Key", API_KEY);

  int code = http.GET();
  if (code == 200) {
    String res = http.getString();
    // Forward schedule info to Arduino if needed
  }
  http.end();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    return;
  }

  // Intercept telemetry line from Arduino Uno
  if (arduinoSerial.available() > 0) {
    String line = arduinoSerial.readStringUntil('\n');
    line.trim();
    if (line.startsWith("{") && line.endsWith("}")) {
      forwardReport(line);
    }
  }

  unsigned long now = millis();
  if (now - lastPullTime >= PULL_INTERVAL) {
    lastPullTime = now;
    pullSchedule();
  }

  delay(10);
}
