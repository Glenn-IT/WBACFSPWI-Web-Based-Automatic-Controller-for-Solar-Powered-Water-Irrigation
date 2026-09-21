--------------------------------------------------
Time: 170s
Root Moisture   : 0.0 %
Surface Water   : 5.5 % [Dist: 24.2cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.54 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
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

[EVENT] Pump STARTED.

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 14.9% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 14.9% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
--------------------------------------------------
Time: 48s
Root Moisture   : 0.0 %
Surface Water   : 14.9 % [Dist: 23.8cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.83 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 48s
Root Moisture   : 0.0 %
Surface Water   : 44.0 % [Dist: 22.6cm]
Battery Voltage : 11.09 V 
Solar Output    : 13.19 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
[EVENT] Pump STOPPED.

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 60.8%
Soil moisture: 0.0%
Battery: 11.10V
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 60.8%
Soil moisture: 0.0%
Battery: 11.10V
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
>>> [TARGET REACHED] Starting 10s settling verification...
--------------------------------------------------
Time: 55s
Root Moisture   : 0.0 %
Surface Water   : 60.8 % [Dist: 22.0cm]
Battery Voltage : 11.10 V 
Solar Output    : 13.53 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 56s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.43 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 57s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.46 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 58s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.46 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 59s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.50 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
>>> [STABLE] 10s Settling complete! Level settled >= 45.0% -> Pump stays OFF
--------------------------------------------------
Time: 60s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.49 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 61s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 62s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 63s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 64s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 65s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 66s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 67s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.47 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 68s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.52 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 69s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.50 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 70s
Root Moisture   : 0.0 %
Surface Water   : 51.8 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.52 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 71s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.55 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 72s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 73s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 74s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 75s
Root Moisture   : 0.0 %
Surface Water   : 53.1 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 76s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 77s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 78s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 79s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 80s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 81s
Root Moisture   : 0.0 %
Surface Water   : 53.1 % [Dist: 22.3cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 82s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 83s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.61 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 84s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.53 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 85s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.53 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 86s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 87s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 88s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 89s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 90s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 91s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 92s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 93s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 94s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 95s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 96s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 97s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 98s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 99s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 100s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.56 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 101s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.56 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 102s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 103s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 104s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 105s
Root Moisture   : 0.0 %
Surface Water   : 51.8 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 106s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 107s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 108s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 109s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 110s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 111s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 112s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 113s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 114s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 115s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 116s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 117s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 118s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.53 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 119s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 120s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 121s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 122s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 123s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 124s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 125s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 126s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 127s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 128s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 129s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 130s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 131s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 132s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 133s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 134s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 135s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.51 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 136s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.61 V 
Solar Output    : 13.56 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 137s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 138s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 139s
Root Moisture   : 0.0 %
Surface Water   : 50.5 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 140s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 141s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 142s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 143s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 144s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 145s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 146s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 147s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 148s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 149s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 150s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 151s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 152s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.63 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 153s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 154s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 155s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 156s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 157s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 158s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 159s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 160s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 161s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 162s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 163s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.62 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 164s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 165s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 166s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.75 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 167s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 168s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 169s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 170s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 171s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 172s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 173s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 174s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 175s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 176s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 177s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 178s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 179s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 180s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 181s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 182s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 183s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 184s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.75 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 185s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 186s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 187s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.54 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 188s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 189s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 190s
Root Moisture   : 0.0 %
Surface Water   : 54.8 % [Dist: 22.2cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 191s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 192s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 193s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 194s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 195s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 196s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 197s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 198s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 199s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 200s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 201s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 202s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.72 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 203s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 204s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 205s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.60 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 206s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.60 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 207s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 208s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 209s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.61 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 210s
Root Moisture   : 0.0 %
Surface Water   : 53.9 % [Dist: 22.2cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 211s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 212s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 213s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 214s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 215s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 216s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 217s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 218s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 219s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 220s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 221s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 222s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 223s
Root Moisture   : 0.0 %
Surface Water   : 53.9 % [Dist: 22.2cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.59 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 224s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 225s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 226s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 227s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 228s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 229s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 230s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 231s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 232s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 233s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 234s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 235s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 236s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 237s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 238s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 239s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 240s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 241s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 242s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 243s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 244s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 245s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 246s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 247s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 248s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 249s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 250s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 251s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 252s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 253s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 254s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 255s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 256s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 257s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 258s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 259s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 260s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 261s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 262s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 263s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 264s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 265s
Root Moisture   : 0.0 %
Surface Water   : 50.5 % [Dist: 22.4cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 266s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 267s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 268s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 269s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 270s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 271s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 272s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 273s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 274s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 275s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 276s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 277s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 278s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 279s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.60 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 280s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 281s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 282s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 283s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 284s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 285s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 286s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 287s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 288s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 289s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 290s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 291s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 292s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 293s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 294s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 295s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.58 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 296s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.57 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 297s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 298s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 299s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 300s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 301s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 302s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 303s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 304s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 305s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.62 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 306s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 307s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 308s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 309s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 310s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 311s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 312s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 313s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 314s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 315s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 316s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 317s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 318s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 319s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 320s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 321s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 322s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 323s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.75 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 324s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 325s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 326s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 327s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 328s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 329s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 330s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 331s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 332s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 333s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 334s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 335s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 336s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 337s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 338s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 339s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.63 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 340s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 341s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 342s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 343s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 344s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 345s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 346s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 348s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 349s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.61 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 350s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 351s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 352s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 353s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.62 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 354s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 355s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 356s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 357s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 358s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.72 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 359s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 360s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 361s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 362s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 363s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 364s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 365s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 366s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 367s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 368s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 369s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 370s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 371s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 372s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 373s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 374s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 375s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 376s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 377s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 378s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 379s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 380s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 381s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 382s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 383s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 384s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 385s
Root Moisture   : 0.0 %
Surface Water   : 51.3 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 386s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 387s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 388s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 389s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 390s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 391s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 392s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 393s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 394s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 395s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 396s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 397s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 398s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 399s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.62 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 400s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 401s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 402s
Root Moisture   : 0.0 %
Surface Water   : 51.3 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 403s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.59 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 404s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 405s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 406s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 407s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 408s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 409s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 410s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 411s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 412s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 413s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 414s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.63 V 
Solar Output    : 13.56 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 415s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 416s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 417s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 418s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 419s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.61 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 420s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 421s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 422s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 423s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 424s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 425s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 426s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 427s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 428s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 429s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 430s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 431s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 432s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.60 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 433s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 434s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 435s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.72 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 436s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 437s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 438s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 439s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 440s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 441s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 442s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 443s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 444s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 445s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 446s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 447s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 448s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 449s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.64 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 450s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 451s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 452s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 453s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 454s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 455s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 456s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 457s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 458s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 459s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 460s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 461s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 462s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 463s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.63 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 464s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 465s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 466s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.68 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 467s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 468s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 469s
Root Moisture   : 0.0 %
Surface Water   : 52.6 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 470s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.72 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 471s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 472s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.70 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 473s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 474s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 475s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 476s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 477s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 478s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.66 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 479s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 480s
Root Moisture   : 0.0 %
Surface Water   : 53.5 % [Dist: 22.3cm]
Battery Voltage : 11.65 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 481s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.63 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 482s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 483s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.65 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 484s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.61 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 485s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.67 V 
Solar Output    : 13.64 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 486s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.72 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 487s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 488s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.72 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 489s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.70 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 490s
Root Moisture   : 0.0 %
Surface Water   : 53.1 % [Dist: 22.3cm]
Battery Voltage : 11.69 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 491s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 492s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 14.16 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 493s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 494s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.75 V 
Solar Output    : 14.10 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 495s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.08 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 496s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 497s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.71 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 498s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 499s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 500s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 501s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 502s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 503s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 504s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 505s
Root Moisture   : 0.0 %
Surface Water   : 50.9 % [Dist: 22.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.70 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 506s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 507s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 508s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.67 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 509s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.69 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 510s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 511s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.66 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 512s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.72 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 513s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.75 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 514s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 515s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.68 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 516s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 517s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 518s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 519s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 520s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.76 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 521s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 522s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.10 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 523s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 524s
Root Moisture   : 0.0 %
Surface Water   : 52.2 % [Dist: 22.3cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.08 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 525s
Root Moisture   : 0.0 %
Surface Water   : 71.5 % [Dist: 21.5cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 526s
Root Moisture   : 0.0 %
Surface Water   : 61.2 % [Dist: 22.0cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 527s
Root Moisture   : 0.0 %
Surface Water   : 53.9 % [Dist: 22.2cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.10 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
[EVENT] Pump STARTED.

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation RESTARTED (Cycle #2).
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation RESTARTED (Cycle #2).
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
--------------------------------------------------
Time: 532s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.9cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.15 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 533s
Root Moisture   : 0.0 %
Surface Water   : 4.2 % [Dist: 24.2cm]
Battery Voltage : 11.29 V 
Solar Output    : 13.22 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 534s
Root Moisture   : 0.0 %
Surface Water   : 1.6 % [Dist: 24.3cm]
Battery Voltage : 11.19 V 
Solar Output    : 13.56 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 535s
Root Moisture   : 0.0 %
Surface Water   : 3.7 % [Dist: 24.3cm]
Battery Voltage : 11.29 V 
Solar Output    : 13.88 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 537s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.6cm]
Battery Voltage : 11.31 V 
Solar Output    : 13.78 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 538s
Root Moisture   : 0.0 %
Surface Water   : 3.7 % [Dist: 24.3cm]
Battery Voltage : 11.36 V 
Solar Output    : 13.45 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 539s
Root Moisture   : 0.0 %
Surface Water   : 1.2 % [Dist: 24.4cm]
Battery Voltage : 11.29 V 
Solar Output    : 13.61 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 540s
Root Moisture   : 0.0 %
Surface Water   : 2.5 % [Dist: 24.3cm]
Battery Voltage : 11.32 V 
Solar Output    : 13.71 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 541s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.4cm]
Battery Voltage : 11.32 V 
Solar Output    : 13.81 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 543s
Root Moisture   : 0.0 %
Surface Water   : 2.5 % [Dist: 24.3cm]
Battery Voltage : 11.31 V 
Solar Output    : 13.54 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 544s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.4cm]
Battery Voltage : 11.32 V 
Solar Output    : 13.47 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 545s
Root Moisture   : 0.0 %
Surface Water   : 2.5 % [Dist: 24.3cm]
Battery Voltage : 11.32 V 
Solar Output    : 13.37 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 546s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.4cm]
Battery Voltage : 11.36 V 
Solar Output    : 13.39 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 547s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.4cm]
Battery Voltage : 11.36 V 
Solar Output    : 13.84 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
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

--------------------------------------------------
Time: 43s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 45s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 46s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 47s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 48s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 49s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 50s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 51s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 52s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 53s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 54s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.1cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 55s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 56s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 57s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 58s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 59s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 60s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 61s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 62s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 63s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 64s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.73 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 65s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 66s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 67s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 68s
Root Moisture   : 0.0 %
Surface Water   : 100.0 % [Dist: 20.2cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 69s
Root Moisture   : 0.0 %
Surface Water   : 98.9 % [Dist: 20.4cm]
Battery Voltage : 11.97 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
[EVENT] Pump STARTED.

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 0.0% (< 45%).
Soil moisture: 0.0%
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
--------------------------------------------------
Time: 74s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.85 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 75s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.98 V 
Solar Output    : 13.81 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 76s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.93 V 
Solar Output    : 13.93 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 77s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.97 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 78s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.7cm]
Battery Voltage : 11.92 V 
Solar Output    : 13.98 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 79s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.7cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.89 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 80s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.83 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 81s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.7cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.91 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 82s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.94 V 
Solar Output    : 14.00 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 83s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.91 V 
Solar Output    : 13.96 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 84s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.92 V 
Solar Output    : 13.97 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 85s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 12.00 V 
Solar Output    : 13.89 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 86s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.1cm]
Battery Voltage : 11.95 V 
Solar Output    : 13.87 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 87s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.2cm]
Battery Voltage : 11.95 V 
Solar Output    : 14.00 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 88s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.1cm]
Battery Voltage : 11.91 V 
Solar Output    : 13.93 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 89s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.1cm]
Battery Voltage : 11.95 V 
Solar Output    : 13.89 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 90s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.2cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.11 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 91s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.3cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.23 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 92s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 25.1cm]
Battery Voltage : 11.91 V 
Solar Output    : 14.27 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 93s
Root Moisture   : 0.0 %
Surface Water   : 0.0 % [Dist: 24.8cm]
Battery Voltage : 11.38 V 
Solar Output    : 13.80 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 95s
Root Moisture   : 0.0 %
Surface Water   : 20.0 % [Dist: 23.6cm]
Battery Voltage : 11.47 V 
Solar Output    : 13.62 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 96s
Root Moisture   : 0.0 %
Surface Water   : 25.2 % [Dist: 23.4cm]
Battery Voltage : 11.39 V 
Solar Output    : 13.45 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 97s
Root Moisture   : 0.0 %
Surface Water   : 35.5 % [Dist: 23.0cm]
Battery Voltage : 11.43 V 
Solar Output    : 13.50 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 98s
Root Moisture   : 0.0 %
Surface Water   : 31.6 % [Dist: 23.1cm]
Battery Voltage : 11.39 V 
Solar Output    : 13.51 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
--------------------------------------------------
Time: 99s
Root Moisture   : 0.0 %
Surface Water   : 49.2 % [Dist: 22.4cm]
Battery Voltage : 11.48 V 
Solar Output    : 13.66 V
Pump Relay State: ON (Irrigating)
--------------------------------------------------
[EVENT] Pump STOPPED.

==================================================
[SMS DISPATCH] Recipient: +639158127228
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 64.2%
Soil moisture: 0.0%
Battery: 11.43V
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 64.2%
Soil moisture: 0.0%
Battery: 11.43V
==================================================
[SMS STATUS] >>> SMS SENT SUCCESSFULLY! <<<
>>> [TARGET REACHED] Starting 10s settling verification...
--------------------------------------------------
Time: 105s
Root Moisture   : 0.0 %
Surface Water   : 64.2 % [Dist: 21.8cm]
Battery Voltage : 11.43 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 105s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 106s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.76 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 107s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 108s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 109s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
>>> [STABLE] 10s Settling complete! Level settled >= 45.0% -> Pump stays OFF
--------------------------------------------------
Time: 111s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 112s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 113s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 114s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 115s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 116s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 117s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 118s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 119s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 120s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 121s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.40 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 122s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 123s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 124s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.91 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 125s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.07 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 126s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 127s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.93 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 128s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 129s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 130s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 131s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 132s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 133s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 134s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 135s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 136s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.79 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 137s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 138s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.80 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 139s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 140s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 141s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 142s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.74 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 143s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 144s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 145s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.09 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 146s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 147s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.09 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 148s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.93 V 
Solar Output    : 14.10 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 149s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 150s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 151s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 152s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 153s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 154s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 155s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 156s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 157s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 158s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 159s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 160s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.08 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 161s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 162s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 163s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 164s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 165s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 166s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 167s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 168s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 169s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 170s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 171s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 172s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 173s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 174s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.09 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 175s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 176s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.08 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 177s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 178s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 179s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 180s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 181s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 182s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 183s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 184s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 185s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 186s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 187s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.34 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 188s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 189s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 190s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 191s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 192s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 193s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 194s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 195s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.93 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 196s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.91 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 197s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 198s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 199s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 200s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 201s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 202s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 203s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 204s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 205s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 206s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 207s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 208s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.09 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 209s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 210s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 211s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 212s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.91 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 213s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 214s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 215s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.30 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 216s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.34 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 217s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.31 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 218s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.93 V 
Solar Output    : 14.31 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 219s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 220s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 221s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.97 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 222s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.91 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 223s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.33 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 224s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 225s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 226s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 227s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.91 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 228s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 229s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 230s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 231s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 232s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 233s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 234s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 235s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 236s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 237s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.71 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 238s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 239s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 240s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 241s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.77 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 242s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.01 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 243s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 244s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 245s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.92 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 246s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 247s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 248s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 249s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 250s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 251s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 252s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 253s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 254s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 255s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.35 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 256s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 257s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 258s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 259s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 260s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 261s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.98 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 262s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 263s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 264s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 265s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 266s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 267s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 268s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 269s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.91 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 270s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.16 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 271s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 272s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 273s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 274s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 275s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 276s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 277s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 278s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 279s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 280s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 281s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 282s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 283s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 284s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 285s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 286s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 287s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 288s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 289s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 290s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 291s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 292s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 293s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 294s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 295s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 296s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 297s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 298s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 299s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 300s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 301s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.82 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 302s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 303s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 304s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 305s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 306s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 307s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 308s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 309s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 310s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 311s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 312s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 313s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 314s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 315s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 316s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 317s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 318s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 319s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 320s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.77 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 321s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 322s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 323s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 324s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 325s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 326s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 327s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.08 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 328s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 329s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 330s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 331s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 332s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 333s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 334s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.96 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 335s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.93 V 
Solar Output    : 14.16 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 336s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.10 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 337s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 338s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 339s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 340s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 341s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.30 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 342s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 343s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 344s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 345s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 346s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 347s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 348s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.78 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 349s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 350s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 351s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 352s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 353s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 354s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 355s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 356s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 357s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 358s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 359s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 360s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.02 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 361s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 362s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 363s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 364s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 365s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 366s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 367s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 368s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 369s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 370s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 371s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 372s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 373s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 374s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 375s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 376s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 377s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 378s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 379s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 380s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 381s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 382s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 383s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 384s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.89 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 385s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 386s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 387s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 388s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 389s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 390s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 391s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 392s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 393s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.30 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 394s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.28 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 395s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 396s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 397s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.18 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 398s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 399s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 400s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.77 V 
Solar Output    : 13.95 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 401s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 402s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.75 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 403s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.90 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 404s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 405s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.90 V 
Solar Output    : 13.92 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 406s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 13.88 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 407s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 408s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 409s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.11 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 410s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 411s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.21 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 412s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 413s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 414s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 415s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.30 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 416s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 417s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.76 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 418s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 419s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 420s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 421s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.00 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 422s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 423s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 424s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 425s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.81 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 426s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 427s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 428s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.85 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 429s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 430s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 431s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 432s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 433s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.13 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 434s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.89 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 435s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 436s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 437s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.27 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 438s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 439s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.25 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 440s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.77 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 441s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 442s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.99 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 443s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.98 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 444s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 445s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.82 V 
Solar Output    : 13.87 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 446s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 447s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 448s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 449s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.86 V 
Solar Output    : 13.91 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 450s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.97 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 451s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.94 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 452s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.85 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 453s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.04 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 454s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 455s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 456s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.87 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 457s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 458s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 459s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.80 V 
Solar Output    : 14.26 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 460s
Root Moisture   : 0.0 %
Surface Water   : 76.2 % [Dist: 21.4cm]
Battery Voltage : 11.74 V 
Solar Output    : 14.24 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 461s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.29 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 462s
Root Moisture   : 0.0 %
Surface Water   : 76.6 % [Dist: 21.3cm]
Battery Voltage : 11.81 V 
Solar Output    : 14.31 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
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
+CSQ: 0,0

OK

[INFO] Checking network registration (AT+CREG?)...
  CREG response: 
+CREG: 0,2

OK

  [INFO] Modem searching for operator network... waiting 2s.
  CREG response: 
+CREG: 0,2

OK
□������
  [INFO] Modem searching for operator network... waiting 2s.
  CREG response: AT+CREG?

+CREG: 0,2

OK

  [INFO] Modem searching for operator network... waiting 2s.
  CREG response: AT+CREG?

+CREG: 0,1

OK
□�����
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
Water level: 6.7% (< 45%).
Soil moisture: 4.7%
==================================================
�WBACFSPWI Alert:
Irrigati��STARTED.
Motor is now ON.
Water level: 6.7% (< 45%).
Soil moisture: 4.7%
+CMS ERROR: 
[SMS STATUS] >>> SMS FAILED TO SEND. Modem Response: �WBACFSPWI Alert:
Irrigati��STARTED.
Motor is now ON.
Water level: 6.7% (< 45%).
Soil moisture: 4.7%
+CMS ERROR: 

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STARTED.
Motor is now ON.
Water level: 6.7% (< 45%).
Soil moisture: 4.7%
==================================================
[ERROR] Failed to set SMS text mode (AT+CMGF=1).
--------------------------------------------------
Time: 69s
Root Moisture   : 4.7 %
Surface Water   : 6.7 % [Dist: 24.1cm]
Battery Voltage : 11.81 V 
Solar Output    : 13.94 V
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
Water level: 86.9%
Soil moisture: 0.0%
Battery: 11.42V
==================================================
�WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 86.9%
Soil moisture: 0.0%
Battery: 11.42V
+CMS ERROR: oper
[SMS STATUS] >>> SMS FAILED TO SEND. Modem Response: �WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 86.9%
Soil moisture: 0.0%
Battery: 11.42V
+CMS ERROR: oper

==================================================
[SMS DISPATCH] Recipient: +639242074903
[SMS CONTENT]  WBACFSPWI Alert:
Irrigation STOPPED.
Motor is now OFF.
Water level: 86.9%
Soil moisture: 0.0%
Battery: 11.42V
==================================================
[ERROR] Failed to set SMS text mode (AT+CMGF=1).
>>> [TARGET REACHED] Starting 10s settling verification...
--------------------------------------------------
Time: 76s
Root Moisture   : 0.0 %
Surface Water   : 86.9 % [Dist: 20.9cm]
Battery Voltage : 11.42 V 
Solar Output    : 13.55 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 77s
Root Moisture   : 6.4 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.79 V 
Solar Output    : 13.83 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 78s
Root Moisture   : 4.2 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.84 V 
Solar Output    : 13.84 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
--------------------------------------------------
Time: 79s
Root Moisture   : 4.2 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.88 V 
Solar Output    : 13.86 V
Pump Relay State: OFF (10s Settling Verification)
--------------------------------------------------
>>> [STABLE] 10s Settling complete! Level settled >= 45.0% -> Pump stays OFF
--------------------------------------------------
Time: 80s
Root Moisture   : 3.8 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.80 V 
Solar Output    : 13.93 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 81s
Root Moisture   : 3.8 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.79 V 
Solar Output    : 14.14 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 82s
Root Moisture   : 3.4 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.84 V 
Solar Output    : 14.15 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 83s
Root Moisture   : 1.3 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.78 V 
Solar Output    : 14.20 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 84s
Root Moisture   : 2.5 %
Surface Water   : 77.9 % [Dist: 21.3cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.23 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 85s
Root Moisture   : 3.4 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.73 V 
Solar Output    : 14.19 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 86s
Root Moisture   : 3.0 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.83 V 
Solar Output    : 14.22 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 87s
Root Moisture   : 3.4 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.17 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 88s
Root Moisture   : 3.4 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.77 V 
Solar Output    : 14.12 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 89s
Root Moisture   : 3.0 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.90 V 
Solar Output    : 14.06 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 90s
Root Moisture   : 3.0 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.86 V 
Solar Output    : 14.05 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
--------------------------------------------------
Time: 91s
Root Moisture   : 1.7 %
Surface Water   : 79.2 % [Dist: 21.2cm]
Battery Voltage : 11.85 V 
Solar Output    : 14.03 V
Pump Relay State: OFF (Standby)
--------------------------------------------------
