# WBACFSPWI — Complete Hardware Startup & Integration Guide
## Arduino Uno R3 + NodeMCU ESP8266 WiFi Telemetry Bridge

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **Target:** Autonomous Solar/Battery Irrigation with 100% Cable-Free Wireless Telemetry to Local XAMPP Web Platform  
> **Status:** Production Ready ✅  

---

## 1. System Architecture Overview

```
 [12V Motorcycle Battery / 3S Li-ion] (10.5V - 14.4V)
            │
            ├───► [LM2596 Buck Step-Down] ────► Regulated 5.00V Star Rail
            │                                         ├──► Arduino Uno 5V Pin
            │                                         ├──► NodeMCU Vin Pin
            │                                         ├──► 5V Relay Module VCC
            │                                         └──► HW-080 Driver VCC
            │
            ├───► [100k / 33k Resistor Divider] ─► Arduino Pin A2 (Battery Monitor)
            │
            └───► [5V Relay COM Terminal] ─────► NO ──► 12V DC Water Pump
                                                            │ (Flyback 1N4007 Diode)
                                                            ▼
                                                       Star GND Rail

 ┌─────────────────────────┐               ┌─────────────────────────┐
 │      Arduino Uno R3     │               │     NodeMCU ESP-12E     │
 │  (Sensors & Relay Auto) │               │   (Wireless WiFi Bridge)│
 │                         │               │                         │
 │ Pin 10 (TX, 5V Logic)   ├─►[1k / 2k Div]├─► Pin D1 (RX, 3.3V)     │
 │ Pin 9  (RX, 5V Safe)    │◄──────────────┤   Pin D2 (TX, 3.3V)     │
 │ Star 5V Rail            │               │   Pin Vin (5V In)       │
 │ Star GND Rail           │               │   Pin GND               │
 └─────────────────────────┘               └───────────┬─────────────┘
                                                       │ WiFi (HTTP POST)
                                                       ▼
                                          [XAMPP Server / MySQL DB]
                                          http://192.168.1.XX/.../public
```

---

## 2. Pre-Flight Hardware Wiring Checklist

Before turning on power, verify these physical connections on your solderless breadboard:

| Wire / Component | From | To | Checkpoint / Requirement |
| :--- | :--- | :--- | :--- |
| **Star Ground (0V)** | Star GND Blue Rail | Arduino `GND`, NodeMCU `GND`, Relay `GND`, Sensor `GND`, Battery `(-)` | **Crucial:** All grounds MUST tie into the common blue rail. |
| **Logic Supply (5V)**| Star 5V Red Rail | Arduino `5V`, NodeMCU `Vin`, Relay `VCC`, HW-080 `VCC` | Must measure `5.00V ± 0.05V` from Buck OUT+ before connecting boards. |
| **Serial Link (TX $\rightarrow$ RX)** | Arduino Pin `10` | NodeMCU Pin `D1` | **Voltage Divider Required:** Arduino 10 $\rightarrow 1\text{k}\Omega \rightarrow$ D1 $\rightarrow 2\text{k}\Omega \rightarrow$ GND. |
| **Serial Link (RX $\leftarrow$ TX)** | NodeMCU Pin `D2` | Arduino Pin `9` | Direct jumper wire (Arduino accepts 3.3V HIGH directly). |
| **Root Moisture Sensor** | Capacitive AOUT | Arduino Pin `A0` | VCC to Arduino Pin 8 (power gate) or Star 5V; GND to Star GND. |
| **Surface Water Sensor** | HW-080 AO | Arduino Pin `A1` | VCC to Star 5V Rail; GND to Star GND Rail. |
| **Battery Divider** | Row 10 Tap (R1/R2) | Arduino Pin `A2` | R1 = $100\text{k}\Omega$ (to Batt +), R2 = $33\text{k}\Omega$ (to GND). |
| **Solar Divider** | Row 25 Tap (R3/R4) | Arduino Pin `A3` | R3 = $100\text{k}\Omega$ (to Solar +), R4 = $20\text{k}\Omega$ (to GND). |
| **Relay Signal** | Arduino Pin `D7` | Relay Module `IN` | Active LOW trigger. |
| **Flyback Diode** | Across DC Pump | Across Pump Leads | **Silver Stripe (Cathode)** to switched +12V; **Anode** to Ground. |
| **GSM Link (RX $\leftarrow$ TX)** | GSM 5VT / TXD | Arduino Pin `D2` | Direct jumper wire (Safe 2.8V–5.0V TTL detected by Arduino Uno). |
| **GSM Link (TX $\rightarrow$ RX)** | Arduino Pin `D3` | GSM 5VR / RXD | **SIM900A:** Direct wire to `5VR` (onboard level shifter). **SIM800L:** Requires $1\text{k}\Omega / 2\text{k}\Omega$ divider to drop 5V to 3.3V. |
| **GSM Power Supply** | Dedicated 5.0V / 4.0V | SIM900A `5V` / SIM800L `VCC` | **Critical:** 2.0A peak burst capability. SIM900A mini board connects to 5.0V; raw SIM800L connects to 4.0V. Place 1000µF cap across power/GND; tie all GNDs together! |

---

## 3. Firmware Flashing Reference

Make sure all microcontrollers and peripheral modules are flashed with their dedicated code:

### Board 1: NodeMCU ESP8266 (The WiFi Bridge)
- **Sketch File:** [`firmware/wbacfspwi_esp8266_node/wbacfspwi_esp8266_node.ino`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/firmware/wbacfspwi_esp8266_node/wbacfspwi_esp8266_node.ino)  
  *(Or [`arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/arduino/09_esp8266_wifi_bridge_test/09_esp8266_wifi_bridge_test.ino))*
- **Arduino IDE Board:** `NodeMCU 1.0 (ESP-12E Module)`
- **Configuration in Sketch:**
  ```cpp
  const char* WIFI_SSID   = "YOUR_WIFI_NAME";
  const char* WIFI_PASS   = "YOUR_WIFI_PASSWORD";
  const char* SERVER_HOST = "http://192.168.1.XX/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public";
  const char* API_KEY     = "dev-local-device-key";
  ```
- **Upload:** Plug NodeMCU into PC via micro-USB $\rightarrow$ Click **Upload**.

### Board 2: Arduino Uno R3 (The Main Automation Controller)
- **Sketch File:** [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino)
- **Arduino IDE Board:** `Arduino Uno`
- **Upload:** Plug Arduino Uno into PC via USB $\rightarrow$ Click **Upload**.

### Board 3: SIM800L / SIM900 GSM Module (Cellular SMS Alerts)
- **Sketch File:** [`arduino/10_gsm_sms_irrigation_alert_test/10_gsm_sms_irrigation_alert_test.ino`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/arduino/10_gsm_sms_irrigation_alert_test/10_gsm_sms_irrigation_alert_test.ino)
- **Arduino IDE Board:** `Arduino Uno`
- **Configuration in Sketch:**
  ```cpp
  char ADMIN_PHONE[20] = "+639123456789"; // Set your mobile phone number here
  ```
- **Features:** Dispatches automated SMS alerts directly to Admin phone when irrigation is STARTED (<45%), STOPPED (>=50%), or RESTARTED. Requires active mini-SIM card with SMS credit.
- **Dedicated Wiring Guide:** [`arduino/10_gsm_sms_irrigation_alert_test/wiring_guide.html`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/arduino/10_gsm_sms_irrigation_alert_test/wiring_guide.html)

---

## 4. Step-by-Step Power-Up Sequence

Follow this order to ensure error-free operation:

### Step 1: Start XAMPP on Your Computer
1. Open the **XAMPP Control Panel**.
2. Click **Start** for **Apache** and **MySQL**.
3. Note your computer's local IPv4 address (run `ipconfig` in Command Prompt, e.g. `192.168.1.18`).

### Step 2: Calibrate the LM2596 Buck Converter (First Time Only)
1. With Arduino and NodeMCU **unplugged from the 5V rail**, connect your 12V battery to the Buck `IN+` and `IN-`.
2. Measure the voltage across Buck `OUT+` and `OUT-` with a digital multimeter.
3. Turn the brass potentiometer screw until the meter reads **`5.00V ± 0.05V`**.
4. Once verified at 5.00V, connect the Buck `OUT+` to the Red breadboard rail and `OUT-` to the Blue breadboard rail.

### Step 3: Power On the Microcontrollers
1. Connect Arduino Uno `5V` to the Red 5V rail and `GND` to the Blue rail.
2. Connect NodeMCU `Vin` to the Red 5V rail and `GND` to the Blue rail.
3. Both boards will power on simultaneously.

### Step 4: Observe Board Boot Indications
- **NodeMCU ESP8266:**
  - Blue LED will flicker while connecting to Wi-Fi.
  - Blue LED flashes **3 times quickly** when successfully connected to Wi-Fi and the backend server.
- **Arduino Uno:**
  - Built-in Pin 13 LED will blink for **10 seconds** during the sensor stabilization countdown window.
  - After 10 seconds, the LED switches to a gentle heartbeat pulse (or solid ON if irrigating).

---

## 5. Live Telemetry Verification

Once powered up, verify the end-to-end integration without touching any wires:

### Option A: Open the Live Telemetry Test Page
In your web browser, open:  
👉 **`http://localhost/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public/admin/telemetry_test.php`**

- The status badge will glow: **`🟢 HARDWARE STREAMING LIVE`**.
- The raw JSON packet box will update every **1 second**:
  ```json
  {
    "soil_moisture": 8.5,
    "water_level": 2.1,
    "battery_voltage": 12.60,
    "battery_percent": 53.8,
    "solar_output": 17.80,
    "pump_state": "on"
  }
  ```
- The database table will populate with incoming rows in real-time.

### Option B: Smartphone Live Monitor (No Login Required)
Open on any phone connected to the same Wi-Fi:  
👉 **`http://192.168.1.XX/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public/telemetry_monitor.php`**

### Option C: Main Web Dashboard
👉 **`http://localhost/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public/admin/dashboard.php`**
- The "Sample data" notice will disappear.
- Gauges and charts will plot live real-time values from the physical sensors.

---

## 6. Functional Field Tests

Perform these 3 tests using a cup of water:

1. **Auto-Start Refill Test:**
   - Leave the HW-080 probe dry in open air (reads $< 45.0\%$).
   - Confirm the relay clicks **`ON (Irrigating)`**.
2. **Auto-Stop Target Test:**
   - Dip the HW-080 probe halfway into the cup of water (reads $\ge 50.0\%$).
   - The Arduino triggers the **10-second wave settling window** (`>>> [TARGET REACHED]`).
   - After 10 seconds of verified stable water level, the relay clicks **`OFF (Standby)`**.
3. **Anti-Splash Protection Test:**
   - While the pump is running, quickly splash or touch the water sensor for 1 second and release.
   - The pump **stays running** because the 5-second anti-splash minimum runtime (`MIN_PUMP_RUN_MS = 5000ms`) prevents momentary ripples from prematurely stopping irrigation.

---

## 7. Troubleshooting & FAQ

### Q: Serial Monitor displays `[LOW BATT LOCK]` on a full 12V battery?
- **Cause:** Battery divider $R_2$ resistor mismatch.
- **Fix:** Verify $R_1$ is **$100\text{k}\Omega$** (Brown-Black-Yellow) and $R_2$ is **$33\text{k}\Omega$** (Orange-Orange-Orange). If you accidentally used a $20\text{k}\Omega$ resistor for $R_2$, the Arduino will calculate $\approx 8.01\text{V}$ instead of $12.4\text{V}$.
- Also verify the battery negative wire is firmly seated in the Star GND rail.

### Q: NodeMCU blue LED keeps flashing slowly and won't connect?
- **Cause:** Incorrect Wi-Fi SSID, password, or the router is running on 5.0GHz only.
- **Fix:** ESP8266 only supports **2.4GHz Wi-Fi**. Make sure your hotspot or router 2.4GHz band is active.

### Q: Data appears in Arduino Serial Monitor, but Web Dashboard shows no records?
- **Check 1:** Open NodeMCU Serial Monitor at 115200 baud to check if HTTP POST returns `200`.
- **Check 2:** Verify the voltage divider between Arduino Pin 10 (TX) and NodeMCU Pin D1 (RX) is correctly oriented.
- **Check 3:** Ensure your laptop's LAN IP didn't change (update `SERVER_HOST` if necessary).
- **Check 4:** Temporarily check Windows Defender Firewall to ensure inbound port 80 (Apache) is allowed on private networks.
