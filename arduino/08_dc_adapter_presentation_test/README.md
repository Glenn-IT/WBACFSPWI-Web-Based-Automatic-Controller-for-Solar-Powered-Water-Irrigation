# WBACFSPWI — Test 08: Direct Current (12V DC Adapter) Presentation Test

This test suite is designed specifically for **live indoor presentations, panel defenses, and bench demonstrations** where you demonstrate the **Capacitive Soil Moisture Sensor**, **HW-080 Surface Water Level Sensor**, and the **12V DC Water Pump** continuously using a **12V DC Power Adapter** and your **5A DC-DC Step-Down Power Module (12V to 5V)** instead of a 3S battery pack.

---

## 1. Module Overview: 5A DC-DC Step-Down Power Module (12V -> 5V)

Your module has:
- **DC Barrel Jack (5.5mm × 2.1mm)**: Plug your 12V Power Adapter directly here.
- **Input Terminal (Next to DC Jack)**: Direct pass-through of raw 12V DC to power the Relay & Water Pump.
- **Output Terminal & USB Port (Other End)**: Factory-regulated **5.00V DC (up to 5A)** to power the Arduino Uno and 5V sensors (no multimeter / potentiometer adjustments needed!).

```
   ┌──────────────────────────────────────────────────────────────────────────┐
   │                  5A DC-DC STEP-DOWN POWER MODULE (12V -> 5V)             │
   ├──────────────────────────────┬────────────────────────────┬──────────────┤
   │      [12V INPUT SIDE]        │      INTERNAL STEP-DOWN    │[5V OUT SIDE] │
   │                              │                            │              │
   │  ┌──────────────┐            │   ┌────────┐   ┌───────┐   │ ┌──────────┐ │
   │  │ [●] DC JACK  │ ◄─ PLUG IN │   │ CAP    │   │ IND-  │   │ │ USB-A    │ │
   │  │ (5.5x2.1mm)  │    12V DC  │   │ 50V    │   │ UCTOR │   │ │ PORT 5V  │ │
   │  └──────────────┘    ADAPTER │   └────────┘   │ [330] │   │ └──────────┘ │
   │                              │                └───────┘   │              │
   │  ┌──────────────┐            │                            │ ┌──────────┐ │
   │  │ [IN+]  [IN-] │ ─────────┐ │                            │ │[5V+] [GND│ │
   │  │ BLUE SCREW   │          │ │                            │ │BLUE SCREW│ │
   │  │ TERMINAL     │          │ │                            │ └────┬───┬─┘ │
   │  └──────┬───────┘          │ └────────────────────────────┘      │   │   │
   └─────────┼──────────────────┼─────────────────────────────────────┼───┼───┘
             │                  │                                     │   │
             │ (Raw 12V Out)    │ (Raw GND)                           │   │
             ▼                  ▼                                     ▼   ▼
      Relay COM Terminal    Star GND Rail                         Breadboard Rails
      (Powers 12V Pump)     (Common Ground)                       (5.0V & GND)
```

---

## 2. Complete System Connection Architecture

```
[12V DC Power Adapter] ──► PLUGS DIRECTLY INTO MODULE'S DC BARREL JACK
                                     │
   ┌─────────────────────────────────┴────────────────────────────────┐
   │                                                                  │
   ▼                                                                  ▼
[MODULE: 12V Input Screw Terminal]                    [MODULE: 5.0V Output Screw Terminal]
(Direct 12V Pass-Through)                             (Regulated 5.00V DC)
   │                                                                  │
   ├───► (+) 12V Line ─────────► Relay [COM] Terminal                 ├───► (+) 5.0V Line ──► Breadboard Red Rail (+)
   │                                  │                               │                         ├──► Arduino 5V Pin
   │                                  │ [NO] Terminal (Switched 12V)  │                         ├──► 5V Relay VCC
   │                                  ▼                               │                         └──► HW-080 VCC
   │                           [DC Water Pump (+)]                    │
   │                                  │                               └───► (-) GND Line  ──► Breadboard Blue Rail (-)
   │                                  │ (Motor Return GND)                                      ├──► Arduino GND Pin
   └───► (-) GND Line ────────────────┴─────────────────────────────────────────────────────────┼──► Capacitive Sensor GND
                                                                                                ├──► HW-080 GND
                                                                                                └──► Relay GND
```

---

## 3. Master Connection Mapping Table

| Origin / Terminal | Voltage / Signal | Target Destination |
|---|---|---|
| **12V Adapter Plug** | 12.0V DC Input | Plugs directly into Module **DC Barrel Jack** |
| **Module Input Terminal `[IN+]`** | 12.0V Raw Pass-Through | Relay **`COM`** Terminal |
| **Module Input Terminal `[IN-]`** | 0.0V Ground Return | Breadboard Blue Ground Rail **`(-)`** [Star GND] |
| **Module Output Terminal `[5V+]`** | 5.00V Regulated Output | Breadboard Red Power Rail **`(+)`** & Arduino **`5V`** |
| **Module Output Terminal `[GND]`** | 0.0V Regulated Ground | Breadboard Blue Ground Rail **`(-)`** & Arduino **`GND`** |
| **Relay Module `[NO]` Terminal** | Switched 12V DC | DC Water Pump **`(+) Red Wire`** |
| **DC Water Pump `(-)` Wire** | Motor Ground Return | Breadboard Blue Ground Rail **`(-)`** [Star GND] |
| **Flyback Diode (1N4007)** | Snubber Spike Clamp | Cathode (Silver band) -> Pump (+), Anode -> Star GND |
| **Capacitive Soil Sensor** | Root Moisture (A0) | `AOUT` -> Arduino **`A0`**, `VCC` -> Arduino **`D8`**, `GND` -> Star GND |
| **HW-080 Surface Sensor** | Water Depth (A1) | `AO` -> Arduino **`A1`**, `VCC` -> Star 5V, `GND` -> Star GND |
| **5V Relay Control Pin** | Pump Actuation Signal | `IN` -> Arduino **`D7`**, `VCC` -> Star 5V, `GND` -> Star GND |
| **USB Cable to PC** | Serial Bridge (115200 baud)| Arduino Uno USB Port -> Laptop / PC USB Port |

---

## 4. Live Presentation & Serial Monitor Commands

Open the Arduino IDE Serial Monitor at **115200 baud** (with "Newline" or "Both NL & CR" selected).

### Available Demo Commands:

| Key Press | Action / Presentation Behavior |
|:---:|---|
| **`1`** | **5-Second Safe Demo Pulse**: Turns the 12V pump ON for exactly 5 seconds, then automatically shuts off. Perfect for demonstrating live pumping action on a table without overflow risk. |
| **`0`** | **Manual Emergency Stop**: Immediately shuts the pump OFF. |
| **`A`** | **Autonomous Mode**: Runs automated rice field irrigation logic (Refills when water drops < 75%, stops at $\ge 80\%$). |
| **`M`** | **Manual Standby Mode**: Disables automatic pumping; allows steady sensor tests. |
| **`T`** | **5-Second Hardware Self-Test**: Automatically checks sensor readings and clicks the relay once to prove circuit integrity. |
| **`S`** | **Status Summary Card**: Prints a clean summary of current sensor percentages, raw ADC values, and relay state. |
| **`?`** | Displays the command menu. |

---

## 5. Live Panel Demonstration Script

1. **Demonstrate Soil Sensor**:
   - Hold capacitive sensor probe in air: Serial Monitor shows `[          ] 0.0% (Air)`.
   - Dip the probe into moist soil / water: Serial Monitor instantly jumps to `[========  ] 80.0%`.
2. **Demonstrate Surface Water Sensor (Ponding Depth)**:
   - Lower the HW-080 probe into a cup of water: Watch the visual progress bar fill up in real time from `0%` to `80%+`.
3. **Demonstrate Automated Pump Trigger**:
   - Set mode to **`A`** (Autonomous).
   - Lift the HW-080 sensor out of water (<75%): The pump turns **ON** immediately to simulate refilling the rice field.
   - Submerge the sensor back into water ($\ge 80\%$): The pump turns **OFF** automatically.
4. **Demonstrate Safe Manual Pulse**:
   - Type **`1`** in the Serial Monitor: The pump runs for 5 seconds and stops automatically.
