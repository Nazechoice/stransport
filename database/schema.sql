CREATE DATABASE IF NOT EXISTS `transport_ticketing_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `transport_ticketing_db`;

SET FOREIGN_KEY_CHECKS = 0;


CREATE TABLE IF NOT EXISTS roles (
    role_key VARCHAR(50) PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO roles (role_key, role_name, created_at, updated_at) VALUES
('super_admin', 'Super Administrator', NOW(), NOW()),
('administrator', 'Administrator', NOW(), NOW()),
('ticket_officer', 'Ticket Officer', NOW(), NOW()),
('driver', 'Driver', NOW(), NOW()),
('passenger', 'Passenger', NOW(), NOW());

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    photo VARCHAR(255) NULL,
    remember_token VARCHAR(255) NULL,
    last_login_at DATETIME NULL,
    email_verified_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_users_role (role),
    INDEX idx_users_status (status),
    CONSTRAINT fk_users_role FOREIGN KEY (role) REFERENCES roles(role_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(100) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_settings_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS buses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bus_number VARCHAR(50) NOT NULL UNIQUE,
    registration_number VARCHAR(50) NOT NULL UNIQUE,
    bus_type VARCHAR(80) NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    status ENUM('active','maintenance','inactive') NOT NULL DEFAULT 'active',
    maintenance_notes TEXT NULL,
    driver_id BIGINT UNSIGNED NULL,
    image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_buses_status (status),
    INDEX idx_buses_driver (driver_id),
    CONSTRAINT fk_buses_driver FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS routes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(120) NOT NULL,
    destination VARCHAR(120) NOT NULL,
    stops TEXT NULL,
    distance_km DECIMAL(10,2) NOT NULL,
    estimated_minutes INT UNSIGNED NOT NULL,
    fare DECIMAL(10,2) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_routes_origin_destination (origin, destination),
    INDEX idx_routes_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bus_id BIGINT UNSIGNED NOT NULL,
    driver_id BIGINT UNSIGNED NOT NULL,
    route_id BIGINT UNSIGNED NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    available_seats INT UNSIGNED NOT NULL,
    fare DECIMAL(10,2) NOT NULL,
    status ENUM('scheduled','boarding','in_transit','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_schedules_date (departure_date, departure_time),
    INDEX idx_schedules_status (status),
    INDEX idx_schedules_route (route_id),
    INDEX idx_schedules_bus (bus_id),
    INDEX idx_schedules_driver (driver_id),
    CONSTRAINT fk_schedules_bus FOREIGN KEY (bus_id) REFERENCES buses(id),
    CONSTRAINT fk_schedules_driver FOREIGN KEY (driver_id) REFERENCES users(id),
    CONSTRAINT fk_schedules_route FOREIGN KEY (route_id) REFERENCES routes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schedule_seats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id BIGINT UNSIGNED NOT NULL,
    seat_row VARCHAR(10) NOT NULL,
    seat_column VARCHAR(10) NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    seat_status ENUM('available','booked','selected','blocked') NOT NULL DEFAULT 'available',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    UNIQUE KEY uq_schedule_seat (schedule_id, seat_number),
    INDEX idx_schedule_seats_status (seat_status),
    CONSTRAINT fk_schedule_seats_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS seat_blocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id BIGINT UNSIGNED NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    reason VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    UNIQUE KEY uq_block_schedule_seat (schedule_id, seat_number),
    INDEX idx_seat_blocks_schedule (schedule_id),
    CONSTRAINT fk_seat_blocks_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(60) NOT NULL UNIQUE,
    passenger_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NOT NULL,
    bus_id BIGINT UNSIGNED NOT NULL,
    route_id BIGINT UNSIGNED NOT NULL,
    seat_number VARCHAR(20) NOT NULL,
    booking_type ENUM('online','walk_in') NOT NULL DEFAULT 'online',
    booking_status ENUM('pending','confirmed','checked_in','cancelled','completed') NOT NULL DEFAULT 'confirmed',
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_bookings_passenger (passenger_id),
    INDEX idx_bookings_schedule (schedule_id),
    INDEX idx_bookings_schedule_seat (schedule_id, seat_number),
    INDEX idx_bookings_bus (bus_id),
    INDEX idx_bookings_route (route_id),
    INDEX idx_bookings_status (booking_status),
    CONSTRAINT fk_bookings_passenger FOREIGN KEY (passenger_id) REFERENCES users(id),
    CONSTRAINT fk_bookings_schedule FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    CONSTRAINT fk_bookings_bus FOREIGN KEY (bus_id) REFERENCES buses(id),
    CONSTRAINT fk_bookings_route FOREIGN KEY (route_id) REFERENCES routes(id),
    CONSTRAINT fk_bookings_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('cash','transfer','card') NOT NULL,
    payment_reference VARCHAR(80) NOT NULL UNIQUE,
    status ENUM('pending','successful','failed','reversed') NOT NULL DEFAULT 'pending',
    paid_at DATETIME NULL,
    received_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_payments_booking (booking_id),
    INDEX idx_payments_status (status),
    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_received_by FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(80) NOT NULL UNIQUE,
    booking_id BIGINT UNSIGNED NOT NULL,
    qr_token VARCHAR(255) NOT NULL UNIQUE,
    qr_data LONGTEXT NULL,
    status ENUM('active','used','void') NOT NULL DEFAULT 'active',
    issued_by BIGINT UNSIGNED NULL,
    issued_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_tickets_booking (booking_id),
    INDEX idx_tickets_status (status),
    CONSTRAINT fk_tickets_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_tickets_issued_by FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'info',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_read (is_read),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_module (module),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) NULL,
    message TEXT NOT NULL,
    status ENUM('new','read','closed') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    deleted_at DATETIME NULL,
    INDEX idx_contact_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
