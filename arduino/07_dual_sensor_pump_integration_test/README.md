# Test 07: Dual Sensor (Capacitive + HW-080) & Relay Pump Integration Test

## 📋 Overview
This integrated test links **both physical sensors** and the **relay water pump** to test autonomous closed-loop irrigation under real rice field rules.

---

## 🌾 Irrigation Decision Logic
1. **PUMP ON Trigger:**
   $$\text{Root Soil Moisture} < 50.0\% \quad\text{AND}\quad \text{Surface Water Level} < 70.0\%$$
2. **PUMP OFF (Surface Ponding Depth Target):**
   $$\text{Surface Water Level} \ge 70.0\%$$
   *(The pump continues filling the paddy until surface water reaches 70.0%. It disregards capacitive root saturation so the required standing ponding layer is achieved!)*
3. **Continuous Run Protection:**
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
