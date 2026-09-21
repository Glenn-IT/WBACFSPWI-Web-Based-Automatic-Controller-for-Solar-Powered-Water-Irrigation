  -> Calibrating sensors & stabilizing network links... 25s remaining
  -> Calibrating sensors & stabilizing network links... 24s remaining
  -> Calibrating sensors & stabilizing network links... 23s remaining
  -> Calibrating sensors & stabilizing network links... 22s remaining
  -> Calibrating sensors & stabilizing network links... 21s remaining
==================================================
 WBACFSPWI: Solar Rice Irrigation Controller     
 Standalone Arduino Uno Automation Firmware      
 Dual Telemetry: NodeMCU WiFi Bridge & SIM900A GSM
3-Layer Automatic Surface Water Level Control:
  - TARGET MAX (PUMP OFF) : >= 50.0% Surface Water
  - REFILL MIN (PUMP ON)  : < 45.0% Surface Water (5% Hysteresis Gap)
  - MINIMUM RUNTIME       : 5 Seconds Anti-Splash Protection
  - SETTLING WINDOW       : 10 Seconds Wave Stabilization
==================================================

--- Initializing GSM Module (SIM900A) ---
[INFO] Auto-detecting GSM baud rate...
[INFO] Testing baud: 9600
[GSM DETECTED] Connected successfully at 9600 baud!
[INFO] Checking signal quality (AT+CSQ)...
  Signal response: 
+CSQ: 16,0

OK

[INFO] Checking network registration (AT+CREG?)...
  CREG response: 
+CREG: 0,1

OK

  [SUCCESS] SIM registered on cellular carrier network!
--- GSM Module Ready & Configured ---

[SYSTEM] GSM SMS Alert Module: ACTIVE
  • Primary Admin   : +639158127228
  • Secondary Admin : +639242074903
[STARTUP] 30-Second Sensor Calibration & Network Stabilization Window...
[STARTUP] Allowing SIM900A GSM module to lock cell tower and NodeMCU to connect to WiFi...
  -> Calibrating sensors & stabilizing network links... 30s remaining
  -> Calibrating sensors & stabilizing network links... 29s remaining
  -> Calibrating sensors & stabilizing network links... 28s remaining
  -> Calibrating sensors & stabilizing network links... 27s remaining
  -> Calibrating sensors & stabilizing network links... 26s remaining
  -> Calibrating sensors & stabilizing network links... 25s remaining
  -> Calibrating sensors & stabilizing network links... 24s remaining
  -> Calibrating sensors & stabilizing network links... 23s remaining
  -> Calibrating sensors & stabilizing network links... 22s remaining
  -> Calibrating sensors & stabilizing network links... 21s remaining
  -> Calibrating sensors & stabilizing network links... 20s remaining
  -> Calibrating sensors & stabilizing network links... 19s remaining
  -> Calibrating sensors & stabilizing network links... 18s remaining
  -> Calibrating sensors & stabilizing network links... 17s remaining
  -> Calibrating sensors & stabilizing network links... 16s remaining
  -> Calibrating sensors & stabilizing network links... 15s remaining
  -> Calibrating sensors & stabilizing network links... 14s remaining
  -> Calibrating sensors & stabilizing network links... 13s remaining
  -> Calibrating sensors & stabilizing network links... 12s remaining
  -> Calibrating sensors & stabilizing network links... 11s remaining
  -> Calibrating sensors & stabilizing network links... 10s remaining
  -> Calibrating sensors & stabilizing network links... 9s remaining
  -> Calibrating sensors & stabilizing network links... 8s remaining
  -> Calibrating sensors & stabilizing network links... 7s remaining
  -> Calibrating sensors & stabilizing network links... 6s remaining
  -> Calibrating sensors & stabilizing network links... 5s remaining
  -> Calibrating sensors & stabilizing network links... 4s remaining
  -> Calibrating sensors & stabilizing network links... 3s remaining
  -> Calibrating sensors & stabilizing network links... 2s remaining
  -> Calibrating sensors & stabilizing network links... 1s remaining
[STARTUP] 30-second calibration window complete! Starting autonomous maintenance...

[EVENT] Pump STARTED immediately.
  [POWER STABILIZATION] Pausing 3.5s for motor startup inrush to settle before cellular transmit...
  [POWER STABILIZATION] Motor current stabilized. Transmitting alert SMS...

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 2.5%
==================================================
�
+CMGS:
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 2.5%
==================================================
�
+CMGS: 4
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
--------------------------------------------------
Time: 61s
Root Moisture   : 2.5 %
Surface Water   : 0.0 % [Dist: 25.2cm]
Battery Voltage : 11.68 V 
Solar Output    : 14.12 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
[EVENT] Pump STOPPED immediately (Zero overflow delay).
  [POWER STABILIZATION] Pausing 1.5s for inductive kickback & power rail bounce-back...
  [POWER STABILIZATION] Power rail clean. Transmitting stop alert SMS...

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 100.0%
Soil moisture: 0.0%
Battery: 11.14V
==================================================
�
+CMGS: 45
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 100.0%
Soil moisture: 0.0%
Battery: 11.14V
==================================================
�
+CMGS:
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
>>> [TARGET REACHED] Starting 10s settling verification...
--------------------------------------------------
Time: 76s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.14 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
>>> [STABLE] 10s Settling complete! Level settled >= 45.0% -> Pump stays OFF
--------------------------------------------------
Time: 76s
Root Moisture   : 0.0 %
Surface Water   : 98.9 % [Dist: 20.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 77s
Root Moisture   : 0.0 %
Surface Water   : 98.9 % [Dist: 20.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 78s
Root Moisture   : 0.4 %
Surface Water   : 98.9 % [Dist: 20.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 79s
Root Moisture   : 0.8 %
Surface Water   : 98.9 % [Dist: 20.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
