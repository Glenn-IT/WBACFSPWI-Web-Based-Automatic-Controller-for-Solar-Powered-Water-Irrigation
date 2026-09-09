# WBACFSPWI — Test 09: NodeMCU ESP8266MOD (ESP-12E) WiFi Bridge Test Suite

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **Module Under Test:** NodeMCU v2/v3 Development Board (ESP8266MOD / ESP-12E / CP2102)  
> **Target:** Replace physical USB cable data bridge with 100% wireless WiFi telemetry link to local XAMPP backend  
> **Status:** Ready for Flashing & Verification ✅  

---

## 1. Hardware Overview: Your Board

Based on your board image, you have a **NodeMCU ESP8266 Development Board (Amica / CP2102 / ESP-12E)**:
- **MCU:** ESP8266MOD (Tensilica 32-bit RISC CPU @ 80MHz/160MHz)
- **USB-to-Serial IC:** Silicon Labs CP2102
- **Onboard Power Regulator:** AMS1117 3.3V LDO (Converts 5V from `Vin` down to clean 3.3V)
- **Buttons:** `RST` (Reset) & `FLASH`
- **Antenna:** Integrated PCB Trace Wi-Fi Antenna

---

## 2. Complete Breadboard & Wiring Guide

### Pin Connection Table

| Arduino Uno R3 Pin | Connection Type | NodeMCU ESP8266 Pin | Electrical Characteristic & Function |
| :--- | :--- | :--- | :--- |
| **LM2596 Buck OUT+ (5V)** | Star 5V Rail | **`Vin`** | Powers the NodeMCU through its onboard AMS1117 3.3V regulator |
| **Star Common GND** | Star GND Rail | **`GND`** | Common 0V reference |
| **Pin 10 (SoftwareSerial TX)** | Voltage Divider | **`D1` (GPIO5 / RX)** | **5V to 3.3V Shift:** Arduino D10 $\rightarrow 1\text{k}\Omega \rightarrow$ D1 $\rightarrow 2\text{k}\Omega \rightarrow$ GND |
| **Pin 9 (SoftwareSerial RX)** | Direct Wire | **`D2` (GPIO4 / TX)** | **3.3V to 5V Safe:** NodeMCU D2 $\rightarrow$ Arduino Pin 9 (Arduino detects 3.3V as HIGH) |

### Voltage Divider Diagram (Pin 10 TX -> NodeMCU D1 RX)
```
Arduino Uno Pin 10 (5V TX)
       │
      ┌┴┐
      │ │  1 kΩ (1/4W) Resistor
      └┬┘
       ├───► Connects directly to NodeMCU Pin D1 (3.3V Max Safe Input)
      ┌┴┐
      │ │  2 kΩ (or 2.2 kΩ) Resistor
      └┬┘
       │
Star GND Rail (0V)
```

---

## 3. How to Upload and Run Test 09

### In the Arduino IDE:
1. Go to **Tools $\rightarrow$ Board $\rightarrow$ ESP8266 Boards $\rightarrow$ NodeMCU 1.0 (ESP-12E Module)**.
   *(If not installed: File $\rightarrow$ Preferences $\rightarrow$ Additional Board Manager URLs: `http://arduino.esp8266.com/stable/package_esp8266com_index.json`)*.
2. Select your CP2102 COM Port under **Tools $\rightarrow$ Port**.
3. Open `arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino`.
4. Update lines 26–30 with your WiFi credentials:
   ```cpp
   const char* WIFI_SSID = "YOUR_WIFI_NAME";
   const char* WIFI_PASS = "YOUR_WIFI_PASSWORD";
   const char* SERVER_HOST = "http://192.168.1.XX/.../public"; // Replace with your laptop's LAN IP
   ```
5. Click **Upload**.
6. Open **Serial Monitor** at **115200 baud**.
7. Press `t` in the Serial Monitor to send an instant test telemetry packet to XAMPP over WiFi!
