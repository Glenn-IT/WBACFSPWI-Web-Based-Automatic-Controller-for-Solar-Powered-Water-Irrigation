-- 002_add_overcharge_alert.sql
--
-- The 6 V SLA is trickle-charged straight from the solar panel through a blocking
-- diode, with no charge controller in the path. Nothing in hardware stops an
-- overcharge, so report.php raises a software alert instead when the pack climbs
-- above ALERT_HIGH_BATTERY_VOLTS (config/device.php).
--
-- Adds 'overcharge' to the alerts type enum. Safe to re-run.

ALTER TABLE alerts
    MODIFY COLUMN type ENUM(
        'low_moisture',
        'low_battery',
        'overcharge',
        'pump_fail',
        'schedule_conflict'
    ) NOT NULL;
