# WBACFSPWI — Test 10: GSM Module (SIM800L / SIM900) SMS Alert Test Suite

> **Project:** Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)  
> **Module Under Test:** SIM800L / SIM900 GSM/GPRS Cellular Module  
> **Target:** Automated Real-Time SMS Text Message Alerts sent to the Admin's Mobile Phone  
> **Status:** Ready for Flashing & Verification ✅  

---

## 1. Overview & Notification Trigger Architecture

This test suite verifies bidirectional GSM cellular integration for the miniature rice field irrigation system. When operating in remote field environments without WiFi coverage, the GSM module acts as the critical primary telemetry and emergency alert channel to notify the farm manager or administrator.

```
                  ┌────────────────────────────────────────────────────────┐
                  │              AUTOMATED IRRIGATION CYCLE                │
                  └────────────────────────────────────────────────────────┘

    [Water Level Drops < 45.0%]
                 │
                 ▼
    ┌─────────────────────────┐          AT+CMGS
    │  PUMP TURNS ON (START)  │ ────────────────────────► [ADMIN MOBILE PHONE]
    └─────────────────────────┘                            SMS: "Irrigation STARTED.
                 │                                               Water level: 42.1% (< 45%)."
                 ▼
     (Pumps Water into Field)
                 │
                 ▼
    [Water Reaches >= 50.0%]
                 │
                 ▼
    ┌─────────────────────────┐          AT+CMGS
    │  PUMP TURNS OFF (STOP)  │ ────────────────────────► [ADMIN MOBILE PHONE]
    └─────────────────────────┘                            SMS: "Irrigation STOPPED.
                 │                                               Target reached: 50.4% (>= 50%)."
                 ▼
    (Settling & Evaporation / Seepage)
                 │
                 ▼
    [Water Level Drops < 45.0% Again]
                 │
                 ▼
    ┌─────────────────────────┐          AT+CMGS
    │ PUMP TURNS ON (RESTART) │ ────────────────────────► [ADMIN MOBILE PHONE]
    └─────────────────────────┘                            SMS: "Irrigation RESTARTED (Cycle #2).
                                                                 Water dropped to 43.8% (< 45%)."
```

---

## 2. Hardware Overview & Power Requirements (CRITICAL)

### The SIM800L & SIM900A (S2-1040U-Z1K0B) Cellular Transceivers
- **SIM800L:** Quad-Band (850/900/1800/1900MHz), 3.7V–4.4V DC direct input, 2.8V–3.3V UART.
- **SIM900A (S2-1040U-Z1K0B):** Dual-Band EGSM (900MHz / 1800MHz) designed for Asian cellular networks (e.g. Philippines Smart/Globe/TNT/TM). The metal RF shield is factory stamped `SIM900A` with part number `S2-1040U-Z1K0B`.
- **Peak Current:** Up to **2.0A burst current** during cellular RF network registration and SMS dispatches.
- **Logic Level:** Supports 5V TTL (`5VR` / `5VT` pins) on mini development boards, and 2.8V–3.3V UART on raw pins.

> [!CAUTION]
> **DO NOT power SIM800L or SIM900A from the Arduino Uno 5V or 3.3V header pins!**  
> The Arduino Uno onboard 5V regulator can only deliver ~400mA. Attempting to power a GSM module from the Arduino header will cause immediate brownouts, resets, and dropped cell tower connections during transmission bursts.

### Powering the SIM900A (S2-1040U-Z1K0B) Mini Development Board:
- **Option A (Board with 5V / VCC5 Input - Most Common):**  
  If your SIM900A mini board features a `5V` or `VCC5` pin (with an onboard MIC29302 or AMS1117 high-current regulator):
  - Power it from the **LM2596 Buck Converter tuned to 5.00V DC** (capable of delivering 2A+).
  - Place a **$1000\mu\text{F}$ low-ESR buffer capacitor** across `5V` and `GND`.
- **Option B (Direct Chip VBAT / VCC4 Input):**  
  If your board powers the raw SIM900A chip via `VCC4` / `VBAT` ($3.7\text{V} - 4.4\text{V}$):
  - Tune the **LM2596 Buck Converter to 4.00V DC** (or use a 3.7V Li-ion battery).
  - Connect a **$1000\mu\text{F}$ capacitor** across `VCC4` and `GND`.
- **PWRKEY Power-On Behavior:**  
  Some SIM900A boards automatically turn on when power is connected. If the `PWR` or `STATUS` LED does not illuminate, momentarily short the **`PWRKEY` pin to `GND` for 1.5 seconds** (or press the onboard `POWER` tactile button) to boot the modem.

### UART Pinout Mapping for SIM900A (S2-1040U-Z1K0B):
| SIM900A Mini Pin | Arduino Uno Pin | Function / Logic Level | Notes |
| :--- | :--- | :--- | :--- |
| **`5VT`** (or `TXD`) | **Pin D2 (RX)** | 5V TTL Transmit from GSM &rarr; Uno RX | Direct jumper wire (Safe). |
| **`5VR`** (or `RXD`) | **Pin D3 (TX)** | 5V TTL Receive into GSM &larr; Uno TX | Direct jumper wire if board has onboard level shifter; use 1k/2k divider if raw 3.3V `3VR` pin. |
| **`GND`** | **Star GND / Arduino GND** | Common Ground (0V Reference) | **Mandatory:** All grounds must be tied together. |
| **`5V` / `VCC5`** | **Star 5.0V (from LM2596)** | Main 2A+ Power Input | Powered from LM2596 tuned to 5.0V with 1000&mu;F cap. |

---

## 3. Complete Breadboard & Wiring Guide

### Master Pin Connection Table

| Arduino Uno R3 Pin | Breadboard Component / Wire | GSM Module (SIM800L) Pin | Operational Function & Safety Rule |
| :--- | :--- | :--- | :--- |
| **LM2596 / Buck (4.0V)** | Star Power Rail (`+`) | **`VCC` / `NET`** | Clean $4.0\text{V}$ high-current supply with $1000\mu\text{F}$ buffer cap |
| **Star Common GND** | Star GND Rail (`-`) | **`GND`** | **CRITICAL:** Arduino GND and GSM GND MUST be tied together! |
| **Pin D2 (SoftwareSerial RX)** | Direct Jumper Wire | **`TXD` (or `TX`)** | Receives AT responses from GSM module (SIM800L 2.8V logic is safely read as HIGH by Arduino) |
| **Pin D3 (SoftwareSerial TX)** | Voltage Divider Junction | **`RXD` (or `RX`)** | **5V to 3.3V Logic Level Shift:** Arduino D3 $\rightarrow 1\text{k}\Omega \rightarrow$ GSM RXD $\rightarrow 2\text{k}\Omega \rightarrow$ GND |
| **Pin A1** | Direct Jumper Wire | HW-080 `AO` | Surface Water Ponding Depth Sensor ($45\%$ Refill / $50\%$ Target) |
| **Pin D7** | Direct Jumper Wire | Relay Module `IN` | Active LOW trigger for 12V DC Water Pump Relay |
| **Pin D13** | Built-in Indicator | — | Mirrors pump state and blinks during SMS dispatch |

### 5V-to-3.3V Voltage Divider Diagram (Pin D3 TX to GSM RXD)

```
Arduino Uno Pin D3 (5V TX)
       │
      ┌┴┐
      │ │  1 kΩ Resistor (1/4 W)
      └┬┘
       ├───► Connects directly to GSM SIM800L Pin RXD (3.3V Max Safe Input)
      ┌┴┐
      │ │  2 kΩ (or 2.2 kΩ) Resistor
      └┬┘
       │
Star GND Rail (0V)
```

---

## 4. How to Configure and Run Test 10

### Step 1: Prepare the SIM Card
1. Insert a standard **Micro-SIM card** (2G / 3G / 4G compatible with 2G fallback: Smart, Globe, TNT, TM, etc.).
2. Ensure the SIM card has:
   - **Active prepaid load / regular credits** (or an active unlimited SMS promo).
   - **PIN lock disabled** (insert into any smartphone first and disable SIM PIN lock in security settings).

### Step 2: Set Your Mobile Phone Number
Open `arduino/10_gsm_sms_irrigation_alert_test/10_gsm_sms_irrigation_alert_test.ino` and update line 46:
```cpp
char ADMIN_PHONE[20] = "+639123456789"; // Replace with your mobile phone number
```

### Step 3: Flash Sketch in Arduino IDE
1. Open **Arduino IDE**.
2. Select **Tools $\rightarrow$ Board $\rightarrow$ Arduino Uno**.
3. Select the Arduino COM port under **Tools $\rightarrow$ Port**.
4. Click **Upload** (Ctrl+U).
5. Open the **Serial Monitor** at **115200 baud**.

---

## 5. Interactive Serial Monitor Commands

The sketch includes an interactive bench testing menu. You can test and verify the entire GSM alert sequence immediately without needing to submerge probes in water:

| Command Key | Action Performed | Expected Serial Output & SMS |
| :---: | :--- | :--- |
| **`s`** | **Instant Test SMS** | Sends an immediate verification SMS: `"[WBACFSPWI TEST] GSM Module SIM800L communication link verified..."` |
| **`c`** | **Module Diagnostics** | Queries signal strength (`AT+CSQ`), network registration status (`AT+CREG?`), and internal voltage (`AT+CBC`). |
| **`p`** | **Pump Toggle** | Toggles 12V DC Pump Relay manually between ON and OFF. |
| **`w`** | **Sensor Readings** | Prints instantaneous surface water level (%) and root soil moisture (%). |
| **`1`** | **Simulate Trigger 1** | Simulates water dropping to 42.0% ($< 45.0\%$). Pump turns ON and dispatches **Irrigation STARTED** SMS. |
| **`2`** | **Simulate Trigger 2** | Simulates water rising to 50.4% ($\ge 50.0\%$). Pump turns OFF and dispatches **Irrigation STOPPED** SMS. |
| **`3`** | **Simulate Trigger 3** | Simulates water dropping to 43.5% ($< 45.0\%$) again. Pump turns ON and dispatches **Irrigation RESTARTED** SMS. |
| **`n`** | **Change Phone** | Allows typing a new admin phone number dynamically via Serial. |
| **`h`** | **Help Menu** | Prints command guide. |

---

## 6. Real-World Physical Bench Test Sequence

1. **Power Up**: Power up the LM2596 buck converter and Arduino. Observe the SIM800L onboard LED:
   - **Blinking once per second**: Searching for cellular network.
   - **Blinking once every 3 seconds**: Successfully registered to cellular network!
2. **Press `s`**: Check your mobile phone. You will receive the test SMS within 5–10 seconds.
3. **Submerge HW-080 Probe in Water**:
   - Lift probe into dry air (Water level drops $< 45\%$): The relay clicks ON, water pump activates, and you receive the **Irrigation STARTED** SMS!
   - Dip probe past the 50% mark (middle of probe): Anti-splash delay holds for 5 seconds, then the relay clicks OFF, water pump stops, and you receive the **Irrigation STOPPED** SMS!
   - Lift probe again: Relay clicks ON, water pump refills, and you receive the **Irrigation RESTARTED (Cycle #2)** SMS!
