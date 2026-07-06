<?php

// Shared secret the ESP8266/ESP32 firmware sends in the X-API-Key header.
// Override via environment variable in production; this default is for local dev only.
define('DEVICE_API_KEY', getenv('WBACFSPWI_DEVICE_API_KEY') ?: 'dev-local-device-key');

// Alert thresholds
define('ALERT_LOW_MOISTURE_PCT', 20.0);
define('ALERT_LOW_BATTERY_VOLTS', 11.5);
