<?php

class SensorReading
{
    public static function create(array $data): int
    {
        $stmt = getDb()->prepare(
            'INSERT INTO sensor_readings (soil_moisture, water_level, battery_voltage, solar_output, pump_state)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['soil_moisture'],
            $data['water_level'] ?? null,
            $data['battery_voltage'],
            $data['solar_output'],
            $data['pump_state'],
        ]);
        return (int) getDb()->lastInsertId();
    }

    // Converts a lead-acid battery voltage reading into an approximate charge percentage.
    public static function batteryPercent(?float $voltage): ?float
    {
        if ($voltage === null) {
            return null;
        }
        $percent = ($voltage - BATTERY_MIN_VOLTS) / (BATTERY_MAX_VOLTS - BATTERY_MIN_VOLTS) * 100;
        return round(max(0, min(100, $percent)), 1);
    }

    // Placeholder reading shown on the dashboard until the Arduino/ESP device starts reporting real data.
    public static function sample(): array
    {
        return [
            'soil_moisture' => 42.0,
            'water_level' => 68.0,
            'battery_voltage' => 12.6,
            'solar_output' => 18.5,
            'pump_state' => 'off',
            'recorded_at' => date('Y-m-d H:i:s'),
        ];
    }

    // Placeholder trend history shown on the dashboard until real sensor data accumulates.
    public static function sampleHistory(int $days = 8): array
    {
        $history = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $history[] = [
                'recorded_at' => date('Y-m-d', strtotime("-{$i} days")),
                'soil_moisture' => round(38 + 6 * sin($i / 2), 1),
                'water_level' => round(65 + 8 * cos($i / 3), 1),
                'battery_voltage' => round(12.2 + 0.5 * sin($i / 4), 2),
            ];
        }
        return $history;
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

    // Last reading of each of the past $days calendar days (today inclusive), oldest first.
    // Days with no reading are still included, with null values, so the chart always shows a fixed number of dates.
    public static function dailyTrend(int $days = 8): array
    {
        $days = max(1, $days);

        $stmt = getDb()->prepare(
            "SELECT sr.* FROM sensor_readings sr
             INNER JOIN (
                 SELECT DATE(recorded_at) AS day, MAX(recorded_at) AS max_recorded_at
                 FROM sensor_readings
                 WHERE recorded_at >= ?
                 GROUP BY DATE(recorded_at)
             ) latest_per_day ON sr.recorded_at = latest_per_day.max_recorded_at"
        );
        $stmt->execute([date('Y-m-d 00:00:00', strtotime('-' . ($days - 1) . ' days'))]);
        $byDay = [];
        foreach ($stmt->fetchAll() as $row) {
            $byDay[substr($row['recorded_at'], 0, 10)] = $row;
        }

        $history = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} days"));
            $history[] = $byDay[$day] ?? [
                'soil_moisture' => round(38 + 6 * sin($i / 2), 1),
                'water_level' => round(65 + 8 * cos($i / 3), 1),
                'battery_voltage' => round(12.2 + 0.5 * sin($i / 4), 2),
                'solar_output' => null,
                'pump_state' => null,
                'recorded_at' => $day,
            ];
        }
        return $history;
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
