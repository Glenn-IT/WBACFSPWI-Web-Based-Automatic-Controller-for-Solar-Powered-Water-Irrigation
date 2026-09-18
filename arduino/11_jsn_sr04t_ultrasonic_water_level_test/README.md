# WBACFSPWI — Test 11: JSN-SR04T Waterproof Ultrasonic Sensor Test & Calibration Suite

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **Module Under Test:** JSN-SR04T (v2.0 / v3.0) Integrated Waterproof Ultrasonic Transducer & Transceiver  
> **Role:** Non-Contact Standing Water Depth (Ponding Level) Controller for Miniature Rice Field  
> **Status:** Ready for Flashing & Verification ✅  

---

## 1. Overview: Why Migrate from HW-080 to JSN-SR04T?

The legacy HW-080 resistive sensor operates by measuring electrical conductivity across exposed copper tracks immersed directly in liquid. In an outdoor agricultural environment, continuous immersion causes four critical failure modes:
1. **Electrolytic Corrosion:** Passing DC voltage through submerged copper rapidly dissolves the traces.
2. **Capillary Splashing Error:** When the pump discharges water, droplets adhere to the dry upper PCB, causing the sensor to falsely report **100.0% flooded** and shut down prematurely.
3. **TDS Drift:** Mud, silt, and fertilizer drastically alter water conductivity, corrupting calibration within hours.
4. **Physical Lifespan:** Contact PCB probes corrode within days or weeks.

### The JSN-SR04T Solution
The **JSN-SR04T** is an **IP67 waterproof ultrasonic distance sensor** that mounts overhead **above** the water:
- **Zero Water Contact:** The sealed aluminum transducer never touches water or soil.
- **Immunity to Water Purity:** Operates purely on acoustic time-of-flight in air ($40\text{ kHz}$), completely unaffected by mud, algae, TDS, or salinity.
- **Splash Proof:** Surface ripples or condensation do not trigger false 100% full readings.
- **Longevity:** Delivers years of continuous outdoor maintenance-free operation.

---

## 2. The Critical "Blind Zone" (Dead Band) & Mounting Geometry

Ultrasonic transducers require a physical recovery time after transmitting a pulse before they can listen for the incoming echo. Because of this internal transducer ringing:
- **Blind Zone Limit:** The JSN-SR04T **cannot measure any object closer than 20 cm to 25 cm**.
- If water rises closer than 20 cm from the transducer, the sensor reports erratic or maximum distance (~0 cm or >400 cm).

### Correct Mounting Setup:
The transducer **must be mounted on a rigid overhead bracket at least 25 cm above the 100% maximum flood line**:

```
        ┌─────────────────────────┐
        │ JSN-SR04T Transducer    │
        └────────────┬────────────┘
                     │
                     │  ◄── SENSOR CLEARANCE (25.0 cm Air Gap)
                     │      [MUST ALWAYS BE AIR — NEVER WATER]
                     ▼
    ═════════════════════════════════════  ◄── 100% Full Flood Line (Distance = 25.0 cm)
    │ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ │
    │ ─── 50.0% Pump Shutoff Target ─── │  ◄── Distance = 25.0 cm + 8.0 cm = 33.0 cm
    │                                   │
    │ ─── 45.0% Pump Refill Trigger ─── │  ◄── Distance = 25.0 cm + 8.8 cm = 33.8 cm
    │                                   │
    │ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ │
    ═════════════════════════════════════  ◄── Container Floor (0% Empty, Distance = 41.0 cm)
```

---

## 3. Mathematical Formula (Distance &rarr; Water Level %)

The firmware calculates water depth and surface ponding percentage in real time:

1. **Measured Distance ($D_{\text{meas}}$):**
   $$D_{\text{meas}} = \frac{\text{Echo Pulse Duration (\mu s)} \times 0.0343\text{ cm/\mu s}}{2}$$

2. **Container Floor Distance ($D_{\text{floor}}$):**
   $$D_{\text{floor}} = \text{Sensor Clearance} + \text{Usable Container Depth}$$
   *(Example: $25.0\text{ cm} + 16.0\text{ cm} = 41.0\text{ cm}$)*

3. **Current Water Depth ($H_{\text{water}}$):**
   $$H_{\text{water}} = D_{\text{floor}} - D_{\text{meas}}$$

4. **Surface Ponding Percentage ($P_{\text{water}}$):**
   $$P_{\text{water}} = \left(\frac{H_{\text{water}}}{\text{Usable Container Depth}}\right) \times 100\%$$

---

## 4. Hardware Pinout & Wiring Table

The JSN-SR04T module consists of two parts: the sealed round transducer probe (connected via coaxial cable) and the blue driver PCB.

| JSN-SR04T Driver Pin | Arduino Uno Pin | Power Rail / Signal | Function & Safety Notes |
| :--- | :--- | :--- | :--- |
| **`5V`** | — | **Star 5.0V Rail (+)** | Powers module (~30mA). Connected to LM2596 Buck OUT+ (Red rail). |
| **`GND`** | — | **Star Ground Rail (-)** | Common 0V ground reference (Blue rail). |
| **`TRIG`** | **Pin A1** | Digital OUTPUT (5V TTL) | Repurposed Pin A1. Generates 10 µs high pulse to trigger ultrasonic burst. |
| **`ECHO`** | **Pin A4** | Digital INPUT (5V TTL) | Repurposed Pin A4. Measures high pulse duration proportional to distance. |

> [!NOTE]
> On the Arduino Uno, analog pins `A0`–`A5` can function identically as standard digital GPIOs (`D14`–`D19`). Repurposing **A1** (`D15`) and **A4** (`D18`) leaves all existing hardware pins intact:
> - `A0`: Root Capacitive Soil Sensor
> - `A2`: Battery Voltage Divider
> - `A3`: Solar Voltage Divider
> - `D2/D3`: GSM Cellular Modem
> - `D7`: DC Water Pump Relay
> - `D8`: Soil Sensor Power Gate
> - `D9/D10`: ESP8266 WiFi Bridge

---

## 5. Interactive Serial Calibration Console Walkthrough

1. Connect the Arduino Uno to your PC via USB cable.
2. Open the Arduino IDE (or VS Code / Serial Terminal) and set the baud rate to **`115200 baud`**.
3. The sketch continuously prints the live distance, water depth, percentage gauge, and simulated irrigation decision:
   ```text
   [ULTRASONIC] Dist: 33.0 cm | Depth:  8.0 cm | Level: 50.0% [==========          ] ⚖️  [BUFFER ZONE: 45.0%-50.0% (Holding previous state)]
   ```

### Calibration Commands:
| Key | Command | Description |
| :---: | :--- | :--- |
| **`c`** or **`C`** | **Calibrate 100% Mark** | Fill your container to the desired 100% maximum level. Press `c`. The current distance is immediately stored as the new `sensorClearanceCM`. |
| **`e`** or **`E`** | **Calibrate 0% Floor** | Empty the container completely. Press `e`. The sketch calculates `containerDepthCM = measured - clearance`. |
| **`t`** or **`T`** | **Burst Diagnostic** | Runs 10 consecutive pings and displays minimum, maximum, average, and jitter span to verify acoustic reflection stability. |
| **`+`** | **Offset Adjust (+0.5cm)** | Fine-tunes sensor clearance height upwards. |
| **`-`** | **Offset Adjust (-0.5cm)** | Fine-tunes sensor clearance height downwards. |
| **`h`** or **`?`** | **Help Menu** | Displays all commands and current calibration constants. |

---

## 6. Integration with 3-Layer Autonomous Irrigation Net

The output of `calculateWaterPercent()` feeds directly into the master irrigation logic defined in [`SYSTEM_MEMORY.md`](../../SYSTEM_MEMORY.md):
- **Pump Activation:** Level drops $< 45.0\%$.
- **Pump Shutoff:** Level reaches $\ge 50.0\%$.
- **Anti-Splash Guard:** `MIN_PUMP_RUN_MS = 5000ms` (minimum 5s runtime).
- **Wave Settling Window:** `SETTLING_DELAY_MS = 10000ms` (10s settling before accepting new decisions).
- **Cutoffs:** 3-minute continuous runtime cap, 1-minute thermal cooldown, low battery lockout (< 10.0V).
