/*
 * WBACFSPWI — Test 09: NodeMCU ESP8266MOD (ESP-12E) WiFi Bridge Test Suite
 * 
 * Hardware:
 *   - NodeMCU v2/v3 (ESP8266MOD / ESP-12E / CP2102 USB)
 *   - Arduino Uno R3 (Main Sensor & Pump Controller)
 * 
 * Electrical Wiring:
 *   - NodeMCU Vin  -> Star 5V Rail (from LM2596 Buck Converter)
 *   - NodeMCU GND  -> Star Common Ground Rail
 *   - NodeMCU D1 (GPIO5, RX) <- Arduino Pin 10 (TX) via 1kΩ / 2kΩ Divider (5V -> 3.3V)
 *   - NodeMCU D2 (GPIO4, TX) -> Arduino Pin 9 (RX) Direct Wire (3.3V -> 5V is safe)
 * 
 * Function:
 *   - Connects to local WiFi network.
 *   - Bridges sensor telemetry from Arduino Uno to Apache/PHP backend (POST /api/device/report.php).
 *   - Polls active schedules from backend (GET /api/device/pull-schedule.php).
 *   - Eliminates the need for a physical USB cable connection to the computer!
 */

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <SoftwareSerial.h>

// ============================================================================
// 1. NETWORK & API CONFIGURATION
// ============================================================================
// Enter your WiFi Network Credentials here:
const char* WIFI_SSID = "YOUR_WIFI_SSID";
const char* WIFI_PASS = "YOUR_WIFI_PASSWORD";

// LAN IP address of the machine running XAMPP, pointing to public/ directory
const char* SERVER_HOST = "http://192.168.1.10/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public";

// Device API key matching DEVICE_API_KEY in config/device.php
const char* DEVICE_API_KEY = "dev-local-device-key";

// ============================================================================
// 2. PIN DEFINITIONS (NodeMCU ESP-12E)
// ============================================================================
// SoftwareSerial interface linked to Arduino Uno:
// Pin D1 (GPIO5) = NodeMCU RX (Receives from Arduino Pin 10 TX)
// Pin D2 (GPIO4) = NodeMCU TX (Transmits to Arduino Pin 9 RX)
const int PIN_SW_RX = D1; // GPIO5
const int PIN_SW_TX = D2; // GPIO4

// Onboard Blue Status LED (Active LOW on NodeMCU)
const int PIN_STATUS_LED = LED_BUILTIN; // GPIO2 / D4

SoftwareSerial arduinoSerial(PIN_SW_RX, PIN_SW_TX);

// ============================================================================
// 3. TIMING & STATE VARIABLES
// ============================================================================
unsigned long lastHeartbeatTime = 0;
unsigned long lastPullScheduleTime = 0;
const unsigned long PULL_SCHEDULE_INTERVAL = 60000UL; // Poll schedules every 60s

// ============================================================================
// 4. HELPER FUNCTIONS
// ============================================================================

void blinkLed(int times, int msDelay) {
  for (int i = 0; i < times; i++) {
    digitalWrite(PIN_STATUS_LED, LOW);  // Turn ON (Active LOW)
    delay(msDelay);
    digitalWrite(PIN_STATUS_LED, HIGH); // Turn OFF
    delay(msDelay);
  }
}

void connectToWiFi() {
  Serial.println();
  Serial.print(F("[WiFi] Connecting to: "));
  Serial.println(WIFI_SSID);

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(F("."));
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println();
    Serial.println(F("[WiFi] Connected Successfully!"));
    Serial.print(F("[WiFi] NodeMCU IP Address: "));
    Serial.println(WiFi.localIP());
    Serial.print(F("[WiFi] Signal Strength (RSSI): "));
    Serial.print(WiFi.RSSI());
    Serial.println(F(" dBm"));
    blinkLed(3, 100);
  } else {
    Serial.println();
    Serial.println(F("[WiFi] Connection Failed! Check SSID and Password."));
  }
}

bool postTelemetryToBackend(const String& jsonPayload) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println(F("[HTTP] Error: WiFi not connected!"));
    return false;
  }

  WiFiClient client;
  HTTPClient http;

  String endpoint = String(SERVER_HOST) + "/api/device/report.php";
  
  http.begin(client, endpoint);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", DEVICE_API_KEY);

  Serial.println(F("[HTTP POST] Forwarding telemetry to XAMPP backend..."));
  Serial.print(F("  -> Payload: "));
  Serial.println(jsonPayload);

  int httpCode = http.POST(jsonPayload);
  bool success = false;

  if (httpCode > 0) {
    Serial.print(F("  -> Response Code: "));
    Serial.println(httpCode);
    String response = http.getString();
    Serial.print(F("  -> Server Reply: "));
    Serial.println(response);

    if (httpCode == 200) {
      success = true;
      blinkLed(1, 80); // Quick blink on successful transmit

      // Check for remote control commands from web dashboard
      if (response.indexOf("\"command\":\"PUMP_ON\"") >= 0) {
        arduinoSerial.println("PUMP_ON");
        Serial.println(F("[REMOTE COMMAND] Dispatched PUMP_ON to Arduino Uno"));
      } else if (response.indexOf("\"command\":\"PUMP_OFF\"") >= 0) {
        arduinoSerial.println("PUMP_OFF");
        Serial.println(F("[REMOTE COMMAND] Dispatched PUMP_OFF to Arduino Uno"));
      } else if (response.indexOf("\"command\":\"PUMP_AUTO\"") >= 0) {
        arduinoSerial.println("PUMP_AUTO");
        Serial.println(F("[REMOTE COMMAND] Dispatched PUMP_AUTO to Arduino Uno"));
      }
    }
  } else {
    Serial.print(F("  -> POST failed, error: "));
    Serial.println(http.errorToString(httpCode).c_str());
  }

  http.end();
  return success;
}

void pullScheduleFromBackend() {
  if (WiFi.status() != WL_CONNECTED) return;

  WiFiClient client;
  HTTPClient http;

  String endpoint = String(SERVER_HOST) + "/api/device/pull-schedule.php";
  http.begin(client, endpoint);
  http.addHeader("X-API-Key", DEVICE_API_KEY);

  int httpCode = http.GET();
  if (httpCode == 200) {
    String response = http.getString();
    Serial.print(F("[SCHEDULE SYNC] Schedules fetched: "));
    Serial.println(response);
  } else {
    Serial.print(F("[SCHEDULE SYNC] Pull failed, code: "));
    Serial.println(httpCode);
  }

  http.end();
}

// ============================================================================
// 5. SETUP & MAIN LOOP
// ============================================================================

void setup() {
  // Onboard CP2102 USB Serial Monitor
  Serial.begin(115200);
  delay(100);

  // SoftwareSerial link to Arduino Uno
  arduinoSerial.begin(9600);

  pinMode(PIN_STATUS_LED, OUTPUT);
  digitalWrite(PIN_STATUS_LED, HIGH); // Off initially (Active LOW)

  Serial.println();
  Serial.println(F("=================================================================="));
  Serial.println(F(" WBACFSPWI: Test 09 — NodeMCU ESP8266MOD WiFi Bridge Test Suite  "));
  Serial.println(F("=================================================================="));
  Serial.println(F(" Hardware: NodeMCU v2/v3 (ESP-12E / CP2102)                       "));
  Serial.println(F(" Connection: SoftwareSerial on D1(RX) & D2(TX) at 9600 baud      "));
  Serial.println(F(" Role: Wireless HTTP Serial Bridge to XAMPP Apache / MySQL       "));
  Serial.println(F("=================================================================="));

  connectToWiFi();

  // Initial schedule check
  if (WiFi.status() == WL_CONNECTED) {
    pullScheduleFromBackend();
  }

  Serial.println(F("\n[READY] Listening for telemetry strings from Arduino Uno on D1...\n"));
}

void loop() {
  // Maintain WiFi Connection
  if (WiFi.status() != WL_CONNECTED) {
    connectToWiFi();
    delay(2000);
    return;
  }

  // 1. Check for incoming telemetry line from Arduino Uno
  if (arduinoSerial.available() > 0) {
    String incoming = arduinoSerial.readStringUntil('\n');
    incoming.trim();

    // Verify valid JSON payload
    if (incoming.startsWith("{") && incoming.endsWith("}")) {
      Serial.println(F("\n>>> [ARDUINO DATA RECEIVED] Telemetry packet intercepted:"));
      postTelemetryToBackend(incoming);
    } else if (incoming.length() > 0) {
      // Forward any plain debug text from Arduino to USB monitor
      Serial.print(F("[ARDUINO LOG] "));
      Serial.println(incoming);
    }
  }

  // 2. Periodic Schedule Pull Sync
  unsigned long now = millis();
  if (now - lastPullScheduleTime >= PULL_SCHEDULE_INTERVAL) {
    lastPullScheduleTime = now;
    pullScheduleFromBackend();
  }

  // 3. User Test Command via Serial Monitor
  // Type 't' to trigger a test simulated transmission without Arduino connected!
  if (Serial.available() > 0) {
    char cmd = Serial.read();
    if (cmd == 't' || cmd == 'T') {
      Serial.println(F("\n[TEST] Manual Test Telemetry Triggered from Serial Monitor..."));
      String testJson = "{\"soil_moisture\":42.5,\"water_level\":51.0,\"battery_voltage\":12.45,\"solar_output\":18.2,\"pump_state\":\"off\"}";
      postTelemetryToBackend(testJson);
    } else if (cmd == 'p' || cmd == 'P') {
      Serial.println(F("\n[TEST] Manual Schedule Pull Triggered..."));
      pullScheduleFromBackend();
    }
  }

  delay(10);
}
