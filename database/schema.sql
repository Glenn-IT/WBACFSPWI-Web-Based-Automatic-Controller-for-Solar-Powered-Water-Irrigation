-- WBACFSPWI database schema
-- Run this against a fresh MySQL database, e.g. `wbacfspwi`

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'viewer') NOT NULL DEFAULT 'viewer',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    security_question VARCHAR(50) NULL,
    security_answer_hash VARCHAR(255) NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    days_of_week SET('mon','tue','wed','thu','fri','sat','sun') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sensor_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    soil_moisture DECIMAL(5,2) NULL,
    battery_voltage DECIMAL(5,2) NULL,
    solar_output DECIMAL(6,2) NULL,
    pump_state ENUM('on','off') NOT NULL DEFAULT 'off',
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS irrigation_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NULL,
    trigger_type ENUM('scheduled', 'manual') NOT NULL,
    started_at DATETIME NOT NULL,
    ended_at DATETIME NULL,
    status ENUM('running', 'completed', 'aborted') NOT NULL DEFAULT 'running',
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS overrides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action ENUM('on', 'off') NOT NULL,
    reason VARCHAR(255) NULL,
    auto_revert_minutes INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('low_moisture', 'low_battery', 'pump_fail', 'schedule_conflict') NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
