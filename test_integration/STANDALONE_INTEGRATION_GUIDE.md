# WBACFSPWI — Standalone Integration & Miniature Build Guide

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **Application:** Miniature Rice Field Automation Model  
> **Status:** All unit hardware tests (01–06) PASSED ✅  
> **Date:** August 2026  

---

## 📋 Executive Summary & Where We Left Off

All hardware tests for your individual components are completely working and verified:
1. **Soil Moisture Sensor (Capacitive v1.2):** Root zone analog reading (Pin A0)
2. **Surface Water Level Sensor (HW-080):** Standing water depth reading (Pin A1)
3. **5V Relay Module & DC Pump:** Digital switching (Pin D7, Active LOW)
4. **3S 18650 Battery Pack:** Calibrated voltage divider at $100\text{ k}\Omega / 33\text{ k}\Omega$ (Pin A2, Nominal ~11.1V, Full 12.6V)
5. **12V Solar Panel (15W/30W):** Calibrated voltage divider at $100\text{ k}\Omega / 20\text{ k}\Omega$ (Pin A3)
6. **Solar Charging & Battery Status:** Verified solar harvesting and state transitions.

---

## 🌾 Physical Miniature Model Architecture

For your miniature prototype demonstration, here is the physical layout:

```
+-----------------------------------------------------------------------------------+
|                            WBACFSPWI MINIATURE SETUP                              |
|                                                                                   |
|   +--------------------------+                      +-------------------------+   |
|   |   12V / 15W SOLAR PANEL  |                      |    WATER RESERVOIR      |   |
|   |   (Mounted on tilt stand)|                      |  (Transparent Acrylic)  |   |
|   +------------+-------------+                      |  - 12V Mini DC Pump     |   |
|                |                                    |  - Water inlet/outlet   |   |
|                v                                    +------------+------------+   |
|   +---------------------------------------+                      |                |
|   |         MAIN CONTROLLER BOX           |                      | (Silicone Tube)|
|   |                                       |                      v                |
|   |  - 3S 18650 Battery Pack (11.1V-12.6V)|         +-------------------------+   |
|   |  - 3S BMS / Solar Charge Controller   |         |   MINIATURE RICE FIELD  |   |
|   |  - Arduino Uno R3                     |         |   (Soil Bed + Water Tray|   |
|   |  - 5V Relay Module (Pump Switch)      |         |                         |   |
|   |  - LM2596 Buck (12V -> 5.0V Logic)    |         |  [A0] Capacitive Sensor |   |
|   |  - Voltage Dividers (A2 & A3)         |         |  [A1] Surface Depth     |   |
|   +---------------------------------------+         +-------------------------+   |
+-----------------------------------------------------------------------------------+
```

---

## 🔌 Complete Hardware Pinout & Wiring Table

| Component | Arduino Pin | Circuit / Interface | Connection Notes |
| :--- | :--- | :--- | :--- |
| **Capacitive Soil Sensor** | `A0` | Analog Input | VCC $\rightarrow$ Pin `D8` (Power Gate), GND $\rightarrow$ Common GND |
| **Surface Water Sensor** | `A1` | Analog Input | VCC $\rightarrow$ 5V, GND $\rightarrow$ Common GND |
| **3S Battery Monitor** | `A2` | Voltage Divider | Battery (+) $\rightarrow 100\text{ k}\Omega \rightarrow$ Pin A2 $\rightarrow 33\text{ k}\Omega \rightarrow$ GND |
| **Solar Panel Monitor** | `A3` | Voltage Divider | Solar (+) $\rightarrow 100\text{ k}\Omega \rightarrow$ Pin A3 $\rightarrow 20\text{ k}\Omega \rightarrow$ GND |
| **Pump Relay Module** | `D7` | Digital Output | Control Pin (IN) $\rightarrow$ D7, VCC $\rightarrow$ 5V, GND $\rightarrow$ GND (Active LOW) |
| **Sensor Power Gate** | `D8` | Digital Output | Gates VCC to soil sensor to prevent corrosion |
| **Status / Fault LED** | `D13` | Digital Output | Built-in LED for system blink heartbeat & faults |

---

## ⚙️ Autonomous Controller Logic (Rice Field Rules)

1. **Irrigation Trigger (Pump ON):**
   $$\text{Soil Moisture} < 35\% \quad\text{AND}\quad \text{Surface Water} < 80\% \quad\text{AND}\quad V_{\text{batt}} \ge 10.0\text{V}$$
2. **Irrigation Cutoff (Pump OFF):**
   $$\text{Soil Moisture} \ge 65\% \quad\text{OR}\quad \text{Surface Water} \ge 80\% \quad\text{OR}\quad V_{\text{batt}} < 10.0\text{V}$$
3. **Continuous Run Protection:**
   - Pump is capped at a maximum of **180 seconds continuous run**.
   - Mandatory **5-minute cooldown** before running again if cap is hit.
4. **Solar Profile:**
   - $V_{\text{solar}} \ge 12.0\text{V} \rightarrow$ Active Solar Harvesting
   - $V_{\text{solar}} < 2.0\text{V} \rightarrow$ Night / Battery Operation Mode

---

## 🚀 Step-by-Step Checklist for Tomorrow

- [ ] **Step 1: Open the Test Integration Webpage**
  - Open [`test_integration/index.html`](index.html) in your browser.
  - Review the interactive sliders, presets, schedule manager, and telemetry terminal.
- [ ] **Step 2: Open the Miniature Blueprint**
  - Open [`test_integration/miniature_blueprint.html`](miniature_blueprint.html) to see the visual assembly and dimension guide for your physical miniature.
- [ ] **Step 3: Upload Full Standalone Firmware to Arduino**
  - Open [`arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino`](../arduino/wbacfspwi_arduino_controller/wbacfspwi_arduino_controller.ino) in Arduino IDE.
  - Compile and upload to the Arduino Uno.
  - Open Serial Monitor at **115200 baud** to confirm full telemetry output.
- [ ] **Step 4: Connect Web Backend / ESP8266**
  - Connect the ESP node to transmit data to Apache/MySQL via `/api/device/report.php`.
