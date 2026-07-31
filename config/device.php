<?php

// Shared secret the ESP8266/ESP32 firmware sends in the X-API-Key header.
// Override via environment variable in production; this default is for local dev only.
define('DEVICE_API_KEY', getenv('WBACFSPWI_DEVICE_API_KEY') ?: 'dev-local-device-key');

// ---------------------------------------------------------------------------
// Battery profile: 6 V 4.5 Ah sealed lead-acid (3 cells).
//
// The panel trickle-charges the pack through a blocking diode with no charge
// controller, so there is no hardware overcharge cut-off — ALERT_HIGH_BATTERY_VOLTS
// below is the only thing that will tell you the panel has been left connected
// too long. If you switch back to a 12 V pack, the original values were
// 11.5 / 11.0 / 14.4 and the high threshold would be 14.8.
// ---------------------------------------------------------------------------

// Alert thresholds
define('ALERT_LOW_MOISTURE_PCT', 20.0);
define('ALERT_LOW_BATTERY_VOLTS', 5.8);    // 6 V SLA is effectively empty below this
define('ALERT_HIGH_BATTERY_VOLTS', 7.4);   // sustained overcharge — disconnect the panel

// Voltage range used to convert a raw battery reading into a charge percentage for display.
define('BATTERY_MIN_VOLTS', 5.4);
define('BATTERY_MAX_VOLTS', 7.2);
