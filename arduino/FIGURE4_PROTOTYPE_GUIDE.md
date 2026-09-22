# WBACFSPWI — Figure 4 Standalone ESP32 Prototype Guide

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **System Architecture:** Figure 4 Standalone ESP32 Architecture  
> **Target Application:** Direct Miniature Rice Field Prototype Demonstration  
> **Single Source of Truth Reference:** Figure 4 Wiring & Layout  

---

## 1. System Overview

Figure 4 represents the streamlined **Standalone ESP32 Prototype Model** designed for direct capstone defense, live presentation, and field deployment.

```
[30W Solar Panel] ──► [Solar Charge Controller] ──► [12V Battery Pack]
                              │ (12V Load / Batt Out)
                              ▼
                [Solderless Breadboard Star Rails]
                  ├── 5V/3.3V Step / VIN ──► [ESP32 DevKit]
                  │                            ├── GPIO 34 (AO) ◄── [Soil Moisture Module & Probe]
                  │                            ├── GPIO 25 (VCC) ──► [Probe Power Gate]
                  │                            └── GPIO 26 (IN)  ──► [1-Channel Relay Module]
                  │                                                        │ (Switched 12V)
                  │                                                        ▼
                  └── 12V Switched Power ─────────────────────────► [12V DC Water Pump]
                                                                           │
                                                                           ▼
                                                             [Miniature Rice Field Soil Bed]
                                                                           │ (Wireless WiFi Link)
                                                                           ▼
                                                             [Smartphone Web Dashboard]
                                                             ("Dry" / "Wet" & "ON" / "OFF")
```

---

## 2. Hardware Pinout Table

| ESP32 Pin | Component | Interface | Description |
| :---: | :--- | :---: | :--- |
| **GPIO 34** | Soil Moisture Sensor (AO) | Analog IN (ADC1) | Reads 12-bit analog voltage from soil probe (3200 dry air, 1350 saturated water). |
| **GPIO 32** | Soil Moisture Module (DO) | Digital IN | Digital threshold comparator output from onboard LM393 potentiometer. |
| **GPIO 25** | Soil Sensor VCC Gate | Digital OUT | Powers on sensor only during reading to prevent probe electrolysis corrosion. |
| **GPIO 26** | 1-Channel Relay Module (IN) | Digital OUT | Active-LOW relay trigger. Turns on 12V pump when LOW. |
| **GPIO 35** | 12V Battery Divider Tap | Analog IN (ADC1) | $100\text{ k}\Omega / 33\text{ k}\Omega$ voltage divider (Factor: `4.0303`). |
| **GPIO 33** | Solar Panel Divider Tap | Analog IN (ADC1) | $100\text{ k}\Omega / 20\text{ k}\Omega$ voltage divider (Factor: `6.0000`). |
| **GPIO 2**  | Onboard Status LED | Digital OUT | Illuminates blue during active pump operation. |
| **VIN / 5V**| Breadboard Power Rail | Power IN | 5V regulated logic input from buck converter or USB. |
| **GND**     | Breadboard Ground Rail | Common GND | Star reference connected to battery, controller, pump, and sensors. |

---

## 3. Autonomous Control Logic

1. **Refill Trigger (PUMP ON):**
   $$\text{Soil Moisture} < 40.0\% \quad\text{AND}\quad V_{\text{batt}} \ge 10.0\text{V}$$
2. **Target Cutoff (PUMP OFF):**
   $$\text{Soil Moisture} \ge 50.0\% \quad\text{OR}\quad V_{\text{batt}} < 10.0\text{V}$$
3. **Safety Timing Caps:**
   - **Anti-Splash Minimum Runtime:** $5000\text{ ms}$ (pump must run at least 5 seconds once activated).
   - **Wave Settling Delay:** $10000\text{ ms}$ before subsequent re-triggering.
   - **Continuous Run Timeout:** $180\text{ seconds}$ maximum runtime before automatic safety shutdown.

---

## 4. Software & Firmware Links

- **Interactive Prototype Simulator:** [`public/prototype.php`](../../public/prototype.php)
- **Mobile Smartphone Web View:** [`public/mobile.php`](../../public/mobile.php)
- **Interactive Wiring Guide (HTML):** [`arduino/figure4_prototype_guide.html`](figure4_prototype_guide.html)
- **ESP32 Firmware Source:** [`firmware/wbacfspwi_figure4_prototype/wbacfspwi_figure4_prototype.ino`](../../firmware/wbacfspwi_figure4_prototype/wbacfspwi_figure4_prototype.ino)
- **Backend API Endpoints:**
  - `POST /api/device/report.php`
  - `GET /api/device/pull-schedule.php`
  - `POST /api/admin/pump-control.php`
