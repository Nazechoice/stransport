<?php

declare(strict_types=1);

namespace Transport\Support;

use Transport\Core\Database;

final class Seeder
{
    public static function run(): void
    {
        try {
            $pdo = Database::pdo();
        } catch (\Throwable) {
            return;
        }

        try {
            $count = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch (\Throwable) {
            return;
        }

        if ($count > 0) {
            return;
        }

        $roles = [
            ['super_admin', 'Super Administrator'],
            ['administrator', 'Administrator'],
            ['ticket_officer', 'Ticket Officer'],
            ['driver', 'Driver'],
            ['passenger', 'Passenger'],
        ];

        foreach ($roles as [$key, $name]) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO roles (role_key, role_name, created_at, updated_at) VALUES (:role_key, :role_name, NOW(), NOW())");
            $stmt->execute(['role_key' => $key, 'role_name' => $name]);
        }

        $defaults = [
            ['Super Admin', 'superadmin@transport.test', 'superadmin@123', 'super_admin'],
            ['System Admin', 'admin@transport.test', 'admin123', 'administrator'],
            ['Ticket Officer', 'officer@transport.test', 'officer@12345', 'ticket_officer'],
            ['Driver One', 'driver@transport.test', 'driver@12345', 'driver'],
            ['Demo Passenger', 'passenger@transport.test', 'passenger@12345', 'passenger'],
        ];

        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, status, created_at, updated_at) VALUES (:full_name, :email, :phone, :password_hash, :role, 'active', NOW(), NOW())");
        foreach ($defaults as [$name, $email, $password, $role]) {
            $stmt->execute([
                'full_name' => $name,
                'email' => $email,
                'phone' => '08000000000',
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
            ]);
        }

        $driverId = (int) $pdo->query("SELECT id FROM users WHERE email = 'driver@transport.test' LIMIT 1")->fetchColumn();
        $adminId = (int) $pdo->query("SELECT id FROM users WHERE email = 'admin@transport.test' LIMIT 1")->fetchColumn();

        $routeCount = (int) $pdo->query("SELECT COUNT(*) FROM routes")->fetchColumn();
        if ($routeCount === 0) {
            $routeStmt = $pdo->prepare("INSERT INTO routes (origin, destination, stops, distance_km, estimated_minutes, fare, status, created_at, updated_at) VALUES (:origin, :destination, :stops, :distance_km, :estimated_minutes, :fare, 'active', NOW(), NOW())");
            $routes = [
                ['Lagos', 'Abuja', 'Ibadan, Ilorin', 760, 720, 28000],
                ['Lagos', 'Port Harcourt', 'Benin, Owerri', 620, 630, 24000],
                ['Abuja', 'Kano', 'Kaduna, Zaria', 320, 330, 15000],
            ];
            foreach ($routes as [$origin, $destination, $stops, $distance, $minutes, $fare]) {
                $routeStmt->execute([
                    'origin' => $origin,
                    'destination' => $destination,
                    'stops' => $stops,
                    'distance_km' => $distance,
                    'estimated_minutes' => $minutes,
                    'fare' => $fare,
                ]);
            }
        }

        $busCount = (int) $pdo->query("SELECT COUNT(*) FROM buses")->fetchColumn();
        if ($busCount === 0) {
            $busStmt = $pdo->prepare("INSERT INTO buses (bus_number, registration_number, bus_type, capacity, status, maintenance_notes, driver_id, image, created_at, updated_at) VALUES (:bus_number, :registration_number, :bus_type, :capacity, :status, :maintenance_notes, :driver_id, NULL, NOW(), NOW())");
            $buses = [
                ['PBT-001', 'RG-001-PBT', 'Luxury Coach', 48, 'active', ''],
                ['PBT-002', 'RG-002-PBT', 'Executive Coach', 52, 'active', ''],
                ['PBT-003', 'RG-003-PBT', 'Standard Bus', 42, 'maintenance', 'Scheduled maintenance'],
            ];
            foreach ($buses as [$busNumber, $reg, $type, $capacity, $status, $notes]) {
                $busStmt->execute([
                    'bus_number' => $busNumber,
                    'registration_number' => $reg,
                    'bus_type' => $type,
                    'capacity' => $capacity,
                    'status' => $status,
                    'maintenance_notes' => $notes,
                    'driver_id' => $driverId ?: null,
                ]);
            }
        }

        $scheduleCount = (int) $pdo->query("SELECT COUNT(*) FROM schedules")->fetchColumn();
        if ($scheduleCount === 0) {
            $routeRows = $pdo->query("SELECT id, origin, destination, fare FROM routes ORDER BY id ASC")->fetchAll();
            $busRows = $pdo->query("SELECT id FROM buses ORDER BY id ASC")->fetchAll();
            if ($routeRows && $busRows) {
                $scheduleStmt = $pdo->prepare("INSERT INTO schedules (bus_id, driver_id, route_id, departure_date, departure_time, arrival_time, available_seats, fare, status, created_at, updated_at) VALUES (:bus_id, :driver_id, :route_id, :departure_date, :departure_time, :arrival_time, :available_seats, :fare, 'scheduled', NOW(), NOW())");
                $scheduleRows = [
                    [$busRows[0]['id'], $driverId, $routeRows[0]['id'], date('Y-m-d', strtotime('+1 day')), '08:00:00', '20:00:00', 48, $routeRows[0]['fare']],
                    [$busRows[1]['id'], $driverId, $routeRows[1]['id'], date('Y-m-d', strtotime('+1 day')), '09:30:00', '19:30:00', 52, $routeRows[1]['fare']],
                    [$busRows[0]['id'], $driverId, $routeRows[2]['id'], date('Y-m-d', strtotime('+2 days')), '07:00:00', '12:30:00', 48, $routeRows[2]['fare']],
                ];
                foreach ($scheduleRows as [$busId, $driverIdValue, $routeId, $departureDate, $departureTime, $arrivalTime, $availableSeats, $fare]) {
                    $scheduleStmt->execute([
                        'bus_id' => $busId,
                        'driver_id' => $driverIdValue,
                        'route_id' => $routeId,
                        'departure_date' => $departureDate,
                        'departure_time' => $departureTime,
                        'arrival_time' => $arrivalTime,
                        'available_seats' => $availableSeats,
                        'fare' => $fare,
                    ]);
                }
            }
        }

        if ($adminId) {
            $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_group, created_at, updated_at) VALUES 
                ('company_name', 'Public Bus Transport Ticketing System', 'general', NOW(), NOW()),
                ('contact_email', 'support@transport.test', 'general', NOW(), NOW()),
                ('contact_phone', '+2348000000000', 'general', NOW(), NOW()),
                ('office_address', 'Transport Headquarters, Lagos, Nigeria', 'general', NOW(), NOW()),
                ('currency_symbol', '₦', 'general', NOW(), NOW()),
                ('booking_window_days', '30', 'general', NOW(), NOW()),
                ('timezone', 'Africa/Lagos', 'general', NOW(), NOW()),
                ('maintenance_mode', 'off', 'general', NOW(), NOW())")->execute();
        }
    }
}
