<?php

class SensorReading
{
    public static function create(array $data): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO sensor_readings (soil_moisture, battery_voltage, solar_output, pump_state)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['soil_moisture'],
            $data['battery_voltage'],
            $data['solar_output'],
            $data['pump_state'],
        ]);
        return (int) getDb()->lastInsertId();
    }

    public static function latest(): ?array
    {
        $stmt = getDb()->query('SELECT * FROM sensor_readings ORDER BY recorded_at DESC LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function history(int $limit = 100): array
    {
        $limit = max(1, $limit);
        $stmt = getDb()->query("SELECT * FROM sensor_readings ORDER BY recorded_at DESC LIMIT $limit");
        return $stmt->fetchAll();
    }

    // $from/$to are 'Y-m-d' date strings (inclusive). Returned oldest-first for charting.
    public static function rangeBetween(string $from, string $to, int $limit = 500): array
    {
        $limit = max(1, $limit);
        $stmt = getDb()->prepare(
            "SELECT * FROM (
                SELECT * FROM sensor_readings
                WHERE recorded_at >= ? AND recorded_at <= ?
                ORDER BY recorded_at DESC
                LIMIT $limit
             ) t ORDER BY recorded_at ASC"
        );
        $stmt->execute([$from . ' 00:00:00', $to . ' 23:59:59']);
        return $stmt->fetchAll();
    }
}
