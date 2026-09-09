-- Adds water level tracking to an existing sensor_readings table.
-- Safe to run once against a database created before this migration existed.

ALTER TABLE sensor_readings
    ADD COLUMN water_level DECIMAL(5,2) NULL AFTER soil_moisture;
