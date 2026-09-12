# Test 07: Dual Sensor (Capacitive + HW-080) & Relay Pump Integration Test

## 📋 Overview
This integrated test links **both physical sensors** and the **relay water pump** to test autonomous closed-loop irrigation under calibrated miniature rice field rules.

---

## 🌾 Autonomous Rice Field Irrigation Control (3-Layer Protection)

1. **Startup Calibration & Stabilization Window (10s):**
   - On boot, the Arduino counts down for **10 seconds** to allow sensor signals and power rails to stabilize before triggering actuators.
2. **Layer 1: Hysteresis Surface Water Maintenance (50% Target / 45% Refill):**
   - **PUMP ON (Refill):** $\text{Surface Water Level (HW-080)} < 45.0\%$ (Refills whenever standing water drops below minimum threshold).
   - **PUMP OFF (Target Reached):** $\text{Surface Water Level (HW-080)} \ge 50.0\%$ (Maintains optimal ponding depth).
   - **Deadband Buffer (45.0% – 50.0%):** Maintains current pump state to prevent rapid relay cycling.
3. **Layer 2: Anti-Splash Minimum Runtime (`MIN_PUMP_RUN_MS = 5000ms`):**
   - Once activated, the pump runs for at least **5 seconds** before target shutoff is evaluated, preventing false stops caused by water surface ripples or spray splashing onto probe.
4. **Layer 3: Wave Settling Window (`SETTLING_DELAY_MS = 10000ms`):**
   - When water reaches $\ge 50.0\%$, the pump stops and the system pauses decisions for **10 seconds** while physical surface waves dissipate before confirming stable level.
5. **Capacitive Soil Sensor (Root Zone):**
   - Continuously streams calibrated telemetry ($0\text{--}100\%$) on pin A0 (powered via D8 gate) for root health logging.
6. **Continuous Run Protection:**
   - Pump is capped at a maximum of **180 seconds continuous run** (`MAX_PUMP_RUNTIME_MS = 180000ms`).

---

## 🔌 Hardware Connections

| Module / Component | Arduino Pin | Description / Notes |
| :--- | :--- | :--- |
| **Capacitive Soil Sensor (Root Zone)** | `A0` (Analog) | AOUT $\rightarrow$ Pin A0 |
| **Capacitive Sensor Power Gate** | `D8` (Digital) | VCC $\rightarrow$ Pin D8, GND $\rightarrow$ Common GND |
| **HW-080 Surface Water Level** | `A1` (Analog) | AO $\rightarrow$ Pin A1, VCC $\rightarrow$ Star 5V Rail, GND $\rightarrow$ Common GND |
| **5V Relay Module (DC Pump)** | `D7` (Digital) | IN $\rightarrow$ Pin D7 (Active LOW), VCC $\rightarrow$ Star 5V, GND $\rightarrow$ Common GND |
| **Status LED** | `D13` (Built-in) | Illuminates when pump relay is active |

---

## 📊 Calibrated ADC Mapping

- **Capacitive Root Soil Sensor (A0):**
  - Dry in Air: `417` ADC $\rightarrow$ **`0.0%` Moisture**
  - Fully Submerged: `153` ADC $\rightarrow$ **`100.0%` Moisture**
- **HW-080 Surface Water Level (A1) - 3-Point Piecewise Curve:**
  - Dry Probe in Air: `1020` ADC $\rightarrow$ **`0.0%` Surface Water**
  - Middle Mark (7-8cm): `663` ADC $\rightarrow$ **`50.0%` Target Level**
  - Full Submersion: `568` ADC $\rightarrow$ **`100.0%` Maximum Flood Depth**
