# WBACFSPWI — Test Integration Web Panel

A lightweight, standalone, static integration testing environment for the **Web-Based Automatic Controller for Solar-Powered Water Irrigation (WBACFSPWI)**.

## Purpose

This test integration panel allows you to visually test and interact with **all system features, functions, decision rules, and telemetry outputs** before connecting the live backend / MySQL database and physical ESP8266 node.

---

## How to Open and Run

### Option 1: Direct File Open in Browser
Simply double-click [`index.html`](index.html) or open it directly in Chrome/Edge/Firefox:
```
file:///C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/test_integration/index.html
```

### Option 2: Via Local Server (XAMPP / PHP Server)
```bash
# If using PHP CLI:
cd C:\xampp\htdocs\WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation\test_integration
php -S 127.0.0.1:8080
```
Then visit `http://127.0.0.1:8080` in your browser.

---

## Included Features to Test

### 1. 📊 Live Dashboard
- **Real-Time Sensor Gauges:**
  - Root Soil Moisture (%)
  - Surface Ponding Water Level (%)
  - 3S 18650 Battery Voltage (V) & Capacity (%)
  - Solar Panel Voltage (V) & Charging State
- **Pump & Relay Status:**
  - Active Relay state (Pin D7), Active Runtime counter (0–180s cap), and Decision reason.
- **Safety Interlock Checklist:**
  - Real-time badges for Low Battery Lockout (<10.0V), Water Ponding Overflow (>80%), and Max Continuous Runtime Cap (180s).
- **Control Modes:**
  - Switch between **Autonomous Mode**, **Manual Force ON**, and **Manual Force OFF**.

### 2. 🎛️ Interactive Sensor Simulator
- **Live Range Sliders:**
  - Manually change Soil Moisture, Water Level, Battery Voltage, and Solar Panel Voltage to see the autonomous controller respond instantly.
- **1-Click Quick Preset Buttons:**
  - 🏜️ *Dry Soil (<35%)* $\rightarrow$ Automatically starts pump
  - 🌿 *Target Moisture (>=65%)* $\rightarrow$ Automatically stops pump
  - 🔴 *Low Battery (<10V)* $\rightarrow$ Immediate safety lockout
  - 🌊 *Surface Flooded (>80%)* $\rightarrow$ Overfill prevention lockout
  - ☀️ *Bright Sun (>18V)* $\rightarrow$ Active solar charging
  - 🌙 *Night Mode (0V)* $\rightarrow$ Battery discharging state

### 3. ⏰ Schedule Manager
- Add new irrigation schedules (Name, Start Time, Duration in minutes, Active Days).
- Toggle schedules ON / OFF.
- Delete schedules.
- Real-time integration with dashboard upcoming schedule widget.

### 4. 🚨 Alerts & Safety Notifications
- Automatic trigger and logging of Critical, Warning, Info, and Success alerts.
- Live dismiss / Clear all alerts.

### 5. 📈 Trends & Reports (Chart.js)
- Multi-curve historical sensor graph (Soil Moisture, Solar Output, Battery Voltage).
- Past irrigation event history log.
- **Working CSV Export:** Generates and downloads real `.csv` reports directly in your browser.

### 6. 📜 Arduino Serial Telemetry Console
- Real-time stream of `[TELEMETRY]` strings matching the exact format of Arduino sketch tests 01 to 06 and `wbacfspwi_arduino_controller.ino`.
- System audit log tracking all user overrides and autonomous triggers.

### 7. 🔌 Hardware Pinouts & Logic Reference
- Complete reference table for Arduino Uno pins, divider ratios ($100\text{k}\Omega / 33\text{k}\Omega$ and $100\text{k}\Omega / 20\text{k}\Omega$), and decision rules.
