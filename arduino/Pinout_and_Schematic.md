# Arduino Uno Hardware Pinout & Star Wiring Schematic

This document provides the complete hardware wiring architecture, pin assignments, resistor divider calculations, and **Star Wiring (Star Ground & Star Power)** topology for the **Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)** based on [`docs/Components.md`](file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/docs/Components.md).

---

## 1. Power Architecture & Star Wiring Principle

### Main Power vs. USB Data Bridge

> [!IMPORTANT]
> **Main Power Source**: The entire system is powered by the **3S 18650 Battery Pack (11.1V–12.6V)** stepped down to a clean, regulated **5.00V DC** via the **LM2596 Buck Converter**. The Buck Converter supplies power to the central Star Rails on the solderless breadboard, which powers the Arduino Uno, Relay module, and sensors.
> 
> **USB Cable Role**: The USB connection to the computer/laptop serves **ONLY as a Serial Data Bridge** (115200 baud UART) to transmit sensor telemetry to the web dashboard and receive schedule/override commands. It is **NOT** the primary power supply for the field actuators or sensors.

### Star Wiring (Star Ground & Star Power) Topology

To prevent inductive motor spikes and high-current relay switching from causing voltage drops, ground loops, or analog sensor measurement fluctuations, all power and ground lines connect to a central **Star Point** on the breadboard:

```
                                [30W Solar Panel (18V-21.6V)]
                                              │
                                              ▼
                                 [Solar Charger Module / 3S BMS]
                                              │
                                              ▼
                    ┌─────────────────[3S 18650 Battery Pack]──────────────────┐
                    │                    (11.1V - 12.6V)                        │
                    │                                                           │
                    ▼                                                           ▼
         [LM2596 Buck Converter]                                      [Relay COM Terminal]
          (IN: 12V  ->  OUT: 5.0V)                                              │
          │                   │                                                 │ [NO] (Switched 12V)
          ▼ (5.0V)            ▼ (0V)                                            ▼
┌──────────────────┐ ┌──────────────────┐                             [DC Water Pump (+)]
│  STAR 5V POWER   │ │   STAR GROUND    │                                       │
│ (Breadboard (+)) │ │ (Breadboard (-)) │◄──────────────────────────────────────┘ (Pump Ground Return)
└────────┬─────────┘ └────────┬─────────┘
         │                    │
         ├────────────────────┼────────► Arduino Uno (5V & GND Pins)
         │                    │          └── [USB Cable] ──► PC / Web Data Bridge (Serial Only)
         ├────────────────────┼────────► 5V Relay Module (VCC & GND)
         ├────────────────────┼────────► Capacitive Soil Moisture Sensor v1.2 (via D8 Power Gate)
         ├────────────────────┼────────► HW-080 Moisture Sensor (VCC & GND)
         │                    ├────────► Battery Resistor Divider R2 (33kΩ Bottom)
         │                    └────────► Solar Resistor Divider R4 (20kΩ Bottom)
```

---

## 2. Complete Master Breadboard Pin & Component Connection Matrix

| Wire ID | Origin Node (From) | Breadboard Tie-Point | Destination Node (To) | Wire Color | Signal Type / Voltage | Operational Function & Safety Rule |
|---|---|---|---|---|---|---|
| **PWR-01** | 3S Battery (+) [12.6V] | Direct Wire | LM2596 Buck IN+ | **Red** | 11.1V – 12.6V DC | Raw high-capacity battery supply to Buck step-down converter. |
| **PWR-02** | LM2596 Buck OUT+ | Top Red Rail (`+`) | Arduino 5V Pin | **Red** | 5.00V DC Regulated | **Primary Logic Power:** Powers Arduino ATmega328P MCU. |
| **PWR-03** | Top Red Rail (`+`) | Direct Jumper | 5V Relay Module VCC | **Red** | 5.00V DC | Powers optocoupler coil driver circuitry. |
| **PWR-04** | Top Red Rail (`+`) | Direct Jumper | HW-080 Driver VCC | **Red** | 5.00V DC | Powers LM393 surface moisture comparator driver board. |
| **GND-01** | LM2596 Buck OUT- | Top Blue Rail (`-`) | Arduino GND Pin | **Blue / Slate** | 0.00V (Star GND) | **Star Ground Reference:** Central zero-volt reference point. |
| **GND-02** | 3S Battery (-) Terminal | Top Blue Rail (`-`) | Buck IN- Terminal | **Blue / Black** | 0.00V (Common) | Ties battery negative return directly into central star ground. |
| **GND-03** | 30W Solar (-) Terminal | Top Blue Rail (`-`) | Star Common GND | **Blue / Black** | 0.00V (Common) | Ties solar panel return into common ground bus. |
| **GND-04** | Relay GND & Sensors GND | Top Blue Rail (`-`) | Star Common GND | **Blue / Slate** | 0.00V | Ground returns for relay coil, capacitive sensor, and HW-080. |
| **ACT-01** | 3S Battery (+) [12V] | Direct Heavy Wire | Relay COM Terminal | **Purple / Red** | 12.6V High Current | Feeds raw un-stepped battery power to relay switch contacts. |
| **ACT-02** | Relay NO Terminal | Row 35 (Diode Cathode) | DC Pump (+) Lead | **Blue** | Switched 12V DC | Powers water pump motor when relay is engaged. |
| **ACT-03** | DC Pump (-) Lead | Row 40 (Diode Anode) | Star GND Rail (`-`) | **Blue / Black** | Motor Return GND | Motor return current flows into Star GND, bypassing Arduino MCU. |
| **ACT-04** | Row 35 (Cathode) | 1N4007 Diode Body | Row 40 (Anode) | Diode Component | Flyback Clamp | **Inductive Spike Suppression:** Clamps reverse-EMF kickback. |
| **SIG-01** | Arduino Pin D7 | Direct Jumper | Relay IN Pin | **Amber** | 5V Digital Out | Active LOW trigger with 3-minute continuous runtime safety cap. |
| **SIG-02** | Arduino Pin D8 | Direct Jumper | Capacitive Sensor VCC | **Pink / Red** | 5V Digital Gate | Powers capacitive sensor only during sampling (anti-corrosion). |
| **SIG-03** | Capacitive Sensor AOUT | Direct Jumper | Arduino Pin A0 | **Green** | 0V – 3.0V Analog | Root zone soil moisture reading (Air ~417, Water ~153). |
| **SIG-04** | HW-080 Sensor AO | Direct Jumper | Arduino Pin A1 | **Cyan** | 0V – 5.0V Analog | Surface ponding depth controller (Dry ~1019, Wet ~508). ON $\le$ 30%, OFF $\ge$ 85%. |
| **SIG-05** | Row 10 (R1/R2 Junction) | 100kΩ / 33kΩ Divider | Arduino Pin A2 | **Purple** | 0V – 3.13V Analog | Battery voltage monitor ($V_{\text{batt}} / 4.0303$). Cutoff < 10.0V. |
| **SIG-06** | Row 25 (R3/R4 Junction) | 100kΩ / 20kΩ Divider | Arduino Pin A3 | **Gold / Yellow** | 0V – 3.67V Analog | Solar panel monitor ($V_{\text{solar}} / 6.000$). Harvesting > 12.0V. |
| **COMM-01**| USB Port | Direct USB Cable | Computer / Web Bridge | **Blue Cable** | UART (115200 baud)| **Telemetry & Command Bridge Only** (Not primary power). |

---

## 3. Voltage Divider Circuits (Arduino 5V ADC Protection)

Arduino Uno analog input pins accept a maximum of **5.0V**. Directly connecting the 3S Battery ($\le 12.6\text{V}$) or 30W Solar Panel ($\le 22.0\text{V}$) will permanently damage the microcontroller. Use resistor dividers plugged into the breadboard:

### A. 3S Battery Voltage Divider (Pin A2)

```
3S Battery (+) [11.1V - 12.6V] (Breadboard Row 5)
       │
      ┌┴┐
      │ │  R1 = 100 kΩ (1/4 W)
      └┬┘
       ├───► Breadboard Row 10 (Junction Tap) ──► Arduino Pin A2 (Max ~3.125V at 12.6V)
      ┌┴┐
      │ │  R2 = 33 kΩ (1/4 W)
      └┬┘
       │
Star GND Rail (-) (Breadboard Row 15)
```

- **Divider Factor**: $\frac{R_1 + R_2}{R_2} = \frac{100\text{k} + 33\text{k}}{33\text{k}} = \frac{133}{33} \approx 4.0303$
- **Code Calculation**: `float vBatt = (analogRead(A2) / 1023.0) * 5.0 * 4.0303;`

---

### B. 30W Solar Panel Voltage Divider (Pin A3)

```
30W Solar Panel (+) [Up to 22.0V Voc] (Breadboard Row 20)
       │
      ┌┴┐
      │ │  R3 = 100 kΩ (1/4 W)
      └┬┘
       ├───► Breadboard Row 25 (Junction Tap) ──► Arduino Pin A3 (Max ~3.667V at 22V)
      ┌┴┐
      │ │  R4 = 20 kΩ (or 22 kΩ, 1/4 W)
      └┬┘
       │
Star GND Rail (-) (Breadboard Row 30)
```

- **Divider Factor**: $\frac{R_3 + R_4}{R_4} = \frac{100\text{k} + 20\text{k}}{20\text{k}} = \frac{120}{20} = 6.000$
- **Code Calculation**: `float vSolar = (analogRead(A3) / 1023.0) * 5.0 * 6.000;`

---

## 4. DC Pump Power & Flyback Diode Wiring

```
[3S 18650 Battery (+)] ────────► Relay [COM] Terminal
                                      │
                                      │ [NO] Terminal (Normally Open)
                                      ▼
                      Breadboard Row 35 (Pump + & Diode Cathode [Silver Band])
                                      │
                                      ▼
                              [DC Water Pump (+)]
                                      │
                              [DC Water Pump (-)]
                                      │
                                      ▼
                      Breadboard Row 40 (Pump - & Diode Anode)
                                      │
                                      ▼
                             [STAR GROUND RAIL (-)]
```

> [!WARNING]
> **Flyback Diode (1N4007) Orientation**: The diode must be plugged across the pump power rails on the breadboard:
> - **Cathode (Silver/White Band)**: Connect to Breadboard Row 35 (Pump `+`).
> - **Anode (Black Side)**: Connect to Breadboard Row 40 (Star GND / Pump `-`).
> - This absorbs the reverse-EMF collapse when the pump switches off, preventing Arduino MCU resets.

---

## 5. Breadboard Assembly Step-by-Step Checklist

1. [ ] **Step 1: Buck Converter Voltage Calibration**
   - Connect 3S Battery ($11.1\text{V}-12.6\text{V}$) to Buck Converter `IN+` and `IN-`.
   - Measure `OUT+` and `OUT-` with a digital multimeter.
   - Adjust the potentiometer brass screw until the multimeter reads **exactly 5.00V**.
2. [ ] **Step 2: Connect Star Power Rails**
   - Connect Buck `OUT+` to Breadboard Red Rail (`+`) [Star 5V].
   - Connect Buck `OUT-` to Breadboard Blue Rail (`-`) [Star GND].
   - Connect Breadboard Red Rail to Arduino **5V Pin** and Blue Rail to Arduino **GND Pin**.
3. [ ] **Step 3: Insert Resistors & Flyback Diode**
   - Battery Divider: $100\text{k}\Omega$ (Row 5 to 10), $33\text{k}\Omega$ (Row 10 to Star GND). Wire Row 10 to Arduino **A2**.
   - Solar Divider: $100\text{k}\Omega$ (Row 20 to 25), $20\text{k}\Omega$ (Row 25 to Star GND). Wire Row 25 to Arduino **A3**.
   - Flyback Diode: 1N4007 between Row 35 (Cathode [Silver band]) and Row 40 (Anode / Star GND).
4. [ ] **Step 4: Connect Sensors & Relay**
   - Root Capacitive Sensor: VCC to Arduino **D8**, GND to Star GND, AOUT to Arduino **A0**.
   - HW-080 Surface Sensor: VCC to Star 5V, GND to Star GND, AO to Arduino **A1**.
   - 5V Relay: VCC to Star 5V, GND to Star GND, IN to Arduino **D7**.
5. [ ] **Step 5: Connect USB for Web/Serial Bridge**
   - Connect USB cable between Arduino Uno and the computer to enable serial data communication at 115200 baud for web interface integration.
