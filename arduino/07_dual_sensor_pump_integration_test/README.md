# Test 07: Dual Sensor (Capacitive + HW-080) & Relay Pump Integration Test

## 📋 Overview
This integrated test links **both physical sensors** and the **relay water pump** to test autonomous closed-loop irrigation under real rice field rules.

---

## 🌾 Constant 85% Surface Water Level Maintenance Mode
1. **Startup Calibration & Stabilization Window (10s):**
   - On boot, the Arduino counts down for **10 seconds** to allow sensor signals and power rails to stabilize before triggering actuators.
2. **Constant Level Maintenance:**
   - **PUMP ON:** $\text{Surface Water Level (HW-080)} < 80.0\%$ (Refills whenever water is not at target depth).
   - **PUMP OFF:** $\text{Surface Water Level (HW-080)} \ge 85.0\%$ (Maintains standing water at 85%).
3. **Capacitive Soil Sensor (Root Zone):**
   - Disregarded for pump control.
   - Continuously streams calibrated telemetry ($0\text{--}100\%$) for root health logging.
4. **Continuous Run Protection:**
   - Pump is capped at a maximum of **180 seconds continuous run**.

---

## 🔌 Hardware Connections

| Module / Component | Arduino Pin | Description / Notes |
| :--- | :--- | :--- |
| **Capacitive Soil Sensor (Root Zone)** | `A0` (Analog) | AOUT $\rightarrow$ Pin A0 |
| **Capacitive Sensor Power Gate** | `D8` (Digital) | VCC $\rightarrow$ Pin D8, GND $\rightarrow$ Common GND |
| **HW-080 Surface Water Level** | `A1` (Analog) | AO $\rightarrow$ Pin A1, VCC $\rightarrow$ 5V, GND $\rightarrow$ Common GND |
| **5V Relay Module (DC Pump)** | `D7` (Digital) | IN $\rightarrow$ Pin D7, VCC $\rightarrow$ 5V, GND $\rightarrow$ Common GND |
| **Status LED** | `D13` (Built-in) | Illuminates when pump relay is active |

---

## 📊 Calibrated ADC Mapping
- **Capacitive Root Soil Sensor:**
  - Dry in Air: `417` ADC $\rightarrow$ **`0.0%` Moisture**
  - Fully Submerged: `153` ADC $\rightarrow$ **`100.0%` Moisture**
- **HW-080 Surface Water Level:**
  - Dry Probe in Air: `1019` ADC $\rightarrow$ **`0.0%` Standing Water**
  - Submerged to Top: `508` ADC $\rightarrow$ **`100.0%` Full Depth**
