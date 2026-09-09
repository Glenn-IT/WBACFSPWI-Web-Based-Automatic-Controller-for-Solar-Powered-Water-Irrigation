<?php

// Shared secret the ESP8266/ESP32 firmware sends in the X-API-Key header.
// Override via environment variable in production; this default is for local dev only.
define('DEVICE_API_KEY', getenv('WBACFSPWI_DEVICE_API_KEY') ?: 'dev-local-device-key');

// ---------------------------------------------------------------------------
// Battery Profile Configuration:
//   '12v_motorcycle': 12V Motorcycle Lead-Acid / AGM / Gel (10.5V min, 12.6V resting, 14.4V bulk solar charge).
//                     Ideal for automotive / motorcycle battery setups with 12V DC pump.
//   '3s_liion':       3S 18650 Li-ion battery pack (11.1V nominal, 12.6V max, 10.0V cutoff).
//                     Matches physical portable hardware build (10.0V - 12.6V).
//   '6v_sla':         6V 4.5Ah sealed lead-acid (3 cells) trickle-charged without controller.
// ---------------------------------------------------------------------------
$batteryProfile = getenv('WBACFSPWI_BATTERY_PROFILE') ?: '12v_motorcycle';
define('BATTERY_PROFILE', $batteryProfile);

if ($batteryProfile === '6v_sla') {
    // Alert thresholds for 6V SLA
    define('ALERT_LOW_MOISTURE_PCT', 20.0);
    define('ALERT_LOW_BATTERY_VOLTS', 5.8);    // 6 V SLA is effectively empty below this
    define('ALERT_HIGH_BATTERY_VOLTS', 7.4);   // sustained overcharge — disconnect the panel
    define('BATTERY_MIN_VOLTS', 5.4);
    define('BATTERY_MAX_VOLTS', 7.2);
} elseif ($batteryProfile === '3s_liion') {
    // Alert thresholds for 3S 18650 Li-ion (12V system)
    define('ALERT_LOW_MOISTURE_PCT', 20.0);
    define('ALERT_LOW_BATTERY_VOLTS', 10.0);   // 3S Li-ion empty / deep discharge protection
    define('ALERT_HIGH_BATTERY_VOLTS', 13.0);  // overvoltage protection for 3S Li-ion (> 12.6V)
    define('BATTERY_MIN_VOLTS', 10.0);
    define('BATTERY_MAX_VOLTS', 12.6);
} else {
    // Alert thresholds for 12V Motorcycle Lead-Acid / AGM / Gel (default)
    define('ALERT_LOW_MOISTURE_PCT', 20.0);
    define('ALERT_LOW_BATTERY_VOLTS', 10.8);   // Low voltage warning to prevent lead-acid sulfation
    define('ALERT_HIGH_BATTERY_VOLTS', 14.8);  // Overvoltage warning above 14.4V bulk solar charge
    define('BATTERY_MIN_VOLTS', 10.5);         // Discharged battery floor (0% charge)
    define('BATTERY_MAX_VOLTS', 14.4);         // Full solar absorption charge ceiling (100% charge)
}
