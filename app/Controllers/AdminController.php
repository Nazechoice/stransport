<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Csrf;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Core\Validator;
use Transport\Models\ActivityLog;
use Transport\Models\Booking;
use Transport\Models\Bus;
use Transport\Models\Payment;
use Transport\Models\Route;
use Transport\Models\Schedule;
use Transport\Models\Setting;
use Transport\Models\User;
use Transport\Services\BookingService;
use Transport\Services\SeatLayoutService;
use Transport\Services\StatsService;

final class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        (new DashboardController())->admin();
    }

    public function users(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $pdo = Database::pdo();
        $users = $pdo->query("SELECT * FROM users WHERE deleted_at IS NULL ORDER BY id DESC")->fetchAll();
        $this->view('admin.users', compact('users'));
    }

    public function roles(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $pdo = Database::pdo();
        $roles = config('roles');
        $roleCounts = $pdo->query("SELECT role, COUNT(*) AS total FROM users WHERE deleted_at IS NULL GROUP BY role")->fetchAll();
        $roleCounts = array_column($roleCounts, 'total', 'role');
        $this->view('admin.roles', compact('roles', 'roleCounts'));
    }

    public function saveUser(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/users');
        }

        $data = Request::all();
        $errors = Validator::required($data, [
            'full_name' => 'Full name',
            'email' => 'Email',
            'phone' => 'Phone',
            'role' => 'Role',
            'status' => 'Status',
        ]);
        if ($errors) {
            Session::set('errors', $errors);
            Response::redirect('admin/users');
        }
        if (!Validator::email((string) ($data['email'] ?? ''))) {
            Session::set('error', 'A valid email address is required.');
            Response::redirect('admin/users');
        }
        $allowedRoles = array_keys(config('roles'));
        if (!in_array((string) $data['role'], $allowedRoles, true)) {
            Session::set('error', 'Invalid role selected.');
            Response::redirect('admin/users');
        }
        if (!in_array((string) $data['status'], ['active', 'inactive', 'suspended'], true)) {
            Session::set('error', 'Invalid user status selected.');
            Response::redirect('admin/users');
        }
        $existing = Database::pdo()->prepare("SELECT id FROM users WHERE email = :email AND deleted_at IS NULL" . (!empty($data['id']) ? " AND id != :id" : "") . " LIMIT 1");
        $params = ['email' => $data['email']];
        if (!empty($data['id'])) {
            $params['id'] = (int) $data['id'];
        }
        $existing->execute($params);
        if ($existing->fetch()) {
            Session::set('error', 'Email address already exists.');
            Response::redirect('admin/users');
        }
        $password = trim((string) ($data['password'] ?? ''));
        if (empty($data['id']) && $password === '') {
            Session::set('error', 'Password is required when creating a new user.');
            Response::redirect('admin/users');
        }
        if ($password !== '' && strlen($password) < 8) {
            Session::set('error', 'Password must be at least 8 characters.');
            Response::redirect('admin/users');
        }
        $payload = [
            'full_name' => trim((string) $data['full_name']),
            'email' => trim((string) $data['email']),
            'phone' => trim((string) $data['phone']),
            'role' => (string) $data['role'],
            'status' => (string) $data['status'],
            'photo' => null,
            'remember_token' => null,
        ];
        if (!empty($data['id'])) {
            $sql = "UPDATE users SET full_name = :full_name, email = :email, phone = :phone, role = :role, status = :status";
            $params = [
                'id' => (int) $data['id'],
                'full_name' => $payload['full_name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'],
                'role' => $payload['role'],
                'status' => $payload['status'],
            ];
            if ($password !== '') {
                $sql .= ", password_hash = :password_hash";
                $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $sql .= ", updated_at = NOW() WHERE id = :id";
            Database::pdo()->prepare($sql)->execute($params);
        } else {
            $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            (new User())->create($payload);
        }
        $this->logActivity('users', !empty($data['id']) ? 'update' : 'create', 'Saved user account ' . $payload['email']);
        Session::set('success', 'User saved successfully.');
        Response::redirect('admin/users');
    }

    public function buses(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $buses = Database::pdo()->query("SELECT b.*, u.full_name AS driver_name, u.phone AS driver_phone
            FROM buses b
            LEFT JOIN users u ON u.id = b.driver_id
            WHERE b.deleted_at IS NULL
            ORDER BY b.bus_number")->fetchAll();
        $drivers = (new User())->findByRole('driver');
        $this->view('admin.buses', compact('buses', 'drivers'));
    }

    public function saveBus(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/buses');
        }

        $data = Request::all();
        $errors = Validator::required($data, [
            'bus_number' => 'Bus number',
            'registration_number' => 'Registration number',
            'bus_type' => 'Bus type',
            'capacity' => 'Capacity',
            'status' => 'Status',
        ]);
        if ($errors) {
            Session::set('errors', $errors);
            Response::redirect('admin/buses');
        }
        if (!in_array((string) $data['status'], ['active', 'maintenance', 'inactive'], true)) {
            Session::set('error', 'Invalid bus status selected.');
            Response::redirect('admin/buses');
        }
        $image = $this->uploadBusImage();
        $busQuery = Database::pdo()->prepare("SELECT id FROM buses WHERE (bus_number = :bus_number OR registration_number = :registration_number) AND deleted_at IS NULL" . (!empty($data['id']) ? " AND id != :id" : "") . " LIMIT 1");
        $busParams = [
            'bus_number' => $data['bus_number'],
            'registration_number' => $data['registration_number'],
        ];
        if (!empty($data['id'])) {
            $busParams['id'] = (int) $data['id'];
        }
        $busQuery->execute($busParams);
        if ($busQuery->fetch()) {
            Session::set('error', 'Bus number or registration number already exists.');
            Response::redirect('admin/buses');
        }
        if (!empty($data['id'])) {
            $existing = Database::pdo()->prepare("SELECT image FROM buses WHERE id = :id LIMIT 1");
            $existing->execute(['id' => (int) $data['id']]);
            $existingRow = $existing->fetch() ?: [];
            $finalImage = $image ?? ($existingRow['image'] ?? null);
            Database::pdo()->prepare("UPDATE buses SET bus_number=:bus_number, registration_number=:registration_number, bus_type=:bus_type, capacity=:capacity, status=:status, maintenance_notes=:maintenance_notes, driver_id=:driver_id, image=:image, updated_at=NOW() WHERE id=:id")->execute([
                'id' => (int) $data['id'],
                'bus_number' => $data['bus_number'],
                'registration_number' => $data['registration_number'],
                'bus_type' => $data['bus_type'],
                'capacity' => (int) $data['capacity'],
                'status' => $data['status'],
                'maintenance_notes' => $data['maintenance_notes'] ?? null,
                'driver_id' => $data['driver_id'] ?: null,
                'image' => $finalImage,
            ]);
        } else {
            (new Bus())->create([
                'bus_number' => $data['bus_number'],
                'registration_number' => $data['registration_number'],
                'bus_type' => $data['bus_type'],
                'capacity' => (int) $data['capacity'],
                'status' => $data['status'],
                'maintenance_notes' => $data['maintenance_notes'] ?? null,
                'driver_id' => $data['driver_id'] ?: null,
                'image' => $image,
            ]);
        }
        $this->logActivity('buses', !empty($data['id']) ? 'update' : 'create', 'Saved bus ' . $data['bus_number']);
        Session::set('success', 'Bus saved successfully.');
        Response::redirect('admin/buses');
    }

    public function routes(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $routes = Database::pdo()->query("SELECT * FROM routes WHERE deleted_at IS NULL ORDER BY origin, destination")->fetchAll();
        $this->view('admin.routes', compact('routes'));
    }

    public function saveRoute(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/routes');
        }
        $data = Request::all();
        $errors = Validator::required($data, [
            'origin' => 'Origin',
            'destination' => 'Destination',
            'distance_km' => 'Distance',
            'estimated_minutes' => 'Estimated time',
            'fare' => 'Fare',
            'status' => 'Status',
        ]);
        if ($errors) {
            Session::set('errors', $errors);
            Response::redirect('admin/routes');
        }
        if (!in_array((string) $data['status'], ['active', 'inactive'], true)) {
            Session::set('error', 'Invalid route status selected.');
            Response::redirect('admin/routes');
        }
        if (!empty($data['id'])) {
            Database::pdo()->prepare("UPDATE routes SET origin=:origin, destination=:destination, stops=:stops, distance_km=:distance_km, estimated_minutes=:estimated_minutes, fare=:fare, status=:status, updated_at=NOW() WHERE id=:id")->execute([
                'id' => (int) $data['id'],
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'stops' => $data['stops'],
                'distance_km' => (float) $data['distance_km'],
                'estimated_minutes' => (int) $data['estimated_minutes'],
                'fare' => (float) $data['fare'],
                'status' => $data['status'],
            ]);
        } else {
            (new Route())->create([
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'stops' => $data['stops'],
                'distance_km' => (float) $data['distance_km'],
                'estimated_minutes' => (int) $data['estimated_minutes'],
                'fare' => (float) $data['fare'],
                'status' => $data['status'],
            ]);
        }
        $this->logActivity('routes', !empty($data['id']) ? 'update' : 'create', 'Saved route ' . $data['origin'] . ' to ' . $data['destination']);
        Session::set('success', 'Route saved successfully.');
        Response::redirect('admin/routes');
    }

    public function schedules(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $schedules = (new Schedule())->forDashboard();
        $buses = (new Bus())->available();
        $drivers = (new User())->findByRole('driver');
        $routes = (new Route())->searchable();
        $this->view('admin.schedules', compact('schedules', 'buses', 'drivers', 'routes'));
    }

    public function saveSchedule(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/schedules');
        }

        $data = Request::all();
        $errors = Validator::required($data, [
            'bus_id' => 'Bus',
            'driver_id' => 'Driver',
            'route_id' => 'Route',
            'departure_date' => 'Departure date',
            'departure_time' => 'Departure time',
            'arrival_time' => 'Arrival time',
            'available_seats' => 'Available seats',
            'fare' => 'Fare',
            'status' => 'Status',
        ]);
        if ($errors) {
            Session::set('errors', $errors);
            Response::redirect('admin/schedules');
        }
        if (!in_array((string) $data['status'], ['scheduled', 'boarding', 'in_transit', 'completed', 'cancelled'], true)) {
            Session::set('error', 'Invalid schedule status selected.');
            Response::redirect('admin/schedules');
        }
        $capacityStmt = Database::pdo()->prepare("SELECT capacity FROM buses WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $capacityStmt->execute(['id' => (int) $data['bus_id']]);
        $busCapacity = (int) ($capacityStmt->fetchColumn() ?: 0);
        if ($busCapacity <= 0) {
            Session::set('error', 'Selected bus was not found.');
            Response::redirect('admin/schedules');
        }
        if ((int) $data['available_seats'] > $busCapacity) {
            Session::set('error', 'Available seats cannot exceed the bus capacity.');
            Response::redirect('admin/schedules');
        }
        $isUpdate = !empty($data['id']);
        if ($isUpdate) {
            Database::pdo()->prepare("UPDATE schedules SET bus_id=:bus_id, driver_id=:driver_id, route_id=:route_id, departure_date=:departure_date, departure_time=:departure_time, arrival_time=:arrival_time, available_seats=:available_seats, fare=:fare, status=:status, updated_at=NOW() WHERE id=:id")->execute([
                'id' => (int) $data['id'],
                'bus_id' => (int) $data['bus_id'],
                'driver_id' => (int) $data['driver_id'],
                'route_id' => (int) $data['route_id'],
                'departure_date' => $data['departure_date'],
                'departure_time' => $data['departure_time'],
                'arrival_time' => $data['arrival_time'],
                'available_seats' => (int) $data['available_seats'],
                'fare' => (float) $data['fare'],
                'status' => $data['status'],
            ]);
        } else {
            $insert = Database::pdo()->prepare("INSERT INTO schedules (bus_id, driver_id, route_id, departure_date, departure_time, arrival_time, available_seats, fare, status, created_at, updated_at) VALUES (:bus_id, :driver_id, :route_id, :departure_date, :departure_time, :arrival_time, :available_seats, :fare, :status, NOW(), NOW())");
            $insert->execute([
                'bus_id' => (int) $data['bus_id'],
                'driver_id' => (int) $data['driver_id'],
                'route_id' => (int) $data['route_id'],
                'departure_date' => $data['departure_date'],
                'departure_time' => $data['departure_time'],
                'arrival_time' => $data['arrival_time'],
                'available_seats' => (int) $data['available_seats'],
                'fare' => (float) $data['fare'],
                'status' => $data['status'],
            ]);
            $data['id'] = (int) Database::pdo()->lastInsertId();
        }
        $this->ensureSeatLayout((int) $data['id']);
        $this->logActivity('schedules', $isUpdate ? 'update' : 'create', 'Saved schedule for route ' . $data['route_id']);
        Session::set('success', 'Schedule saved successfully.');
        Response::redirect('admin/schedules');
    }

    public function bookings(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $bookings = Database::pdo()->query("SELECT b.*, u.full_name AS passenger_name, s.departure_date, s.departure_time, r.origin, r.destination, t.ticket_number, t.status AS ticket_status
            FROM bookings b
            INNER JOIN users u ON u.id = b.passenger_id
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            LEFT JOIN tickets t ON t.booking_id = b.id AND t.deleted_at IS NULL
            WHERE b.deleted_at IS NULL
            ORDER BY b.id DESC
            LIMIT 100")->fetchAll();
        $this->view('admin.bookings', compact('bookings'));
    }

    public function approveBooking(int $id): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/bookings');
        }

        $booking = Database::pdo()->prepare("SELECT * FROM bookings WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $booking->execute(['id' => $id]);
        $booking = $booking->fetch();
        if (!$booking) {
            Session::set('error', 'Booking not found.');
            Response::redirect('admin/bookings');
        }

        try {
            (new BookingService())->issueTicketForBooking($id, $this->user() ?? []);
            $this->logActivity('bookings', 'approve', 'Approved booking #' . $id);
            Session::set('success', 'Booking approved and ticket issued.');
        } catch (\Throwable $throwable) {
            Session::set('error', 'Unable to approve booking: ' . $throwable->getMessage());
        }

        Response::redirect('admin/bookings');
    }

    public function rejectBooking(int $id): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/bookings');
        }

        $booking = Database::pdo()->prepare("SELECT * FROM bookings WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $booking->execute(['id' => $id]);
        $booking = $booking->fetch();
        if (!$booking) {
            Session::set('error', 'Booking not found.');
            Response::redirect('admin/bookings');
        }

        Database::pdo()->prepare("UPDATE bookings SET booking_status = 'cancelled', payment_status = 'refunded', updated_at = NOW() WHERE id = :id")->execute(['id' => $id]);
        Database::pdo()->prepare("UPDATE tickets SET status = 'void', updated_at = NOW() WHERE booking_id = :booking_id AND deleted_at IS NULL")->execute(['booking_id' => $id]);
        Database::pdo()->prepare("UPDATE payments SET status = 'reversed', updated_at = NOW() WHERE booking_id = :booking_id AND deleted_at IS NULL")->execute(['booking_id' => $id]);
        $this->restoreBookingSeat((int) $booking['schedule_id'], (string) $booking['seat_number']);
        $this->logActivity('bookings', 'reject', 'Rejected booking #' . $id);
        Session::set('success', 'Booking rejected.');
        Response::redirect('admin/bookings');
    }

    public function payments(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $payments = Database::pdo()->query("SELECT p.*, b.booking_number, u.full_name AS passenger_name FROM payments p INNER JOIN bookings b ON b.id = p.booking_id INNER JOIN users u ON u.id = b.passenger_id WHERE p.deleted_at IS NULL ORDER BY p.id DESC")->fetchAll();
        $this->view('admin.payments', compact('payments'));
    }

    public function reports(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $stats = (new StatsService())->counts();
        $summary = (new \Transport\Services\ReportService())->summary();
        $this->view('admin.reports', compact('stats', 'summary'));
    }

    public function settings(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $settings = (new Setting())->allKeyValue();
        $this->view('admin.settings', compact('settings'));
    }

    public function saveSettings(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/settings');
        }

        if (!in_array((string) Request::input('maintenance_mode', 'off'), ['on', 'off'], true)) {
            Session::set('error', 'Invalid maintenance mode selected.');
            Response::redirect('admin/settings');
        }

        $bookingWindowDays = (string) Request::input('booking_window_days', '30');
        if ($bookingWindowDays !== '' && !ctype_digit($bookingWindowDays)) {
            Session::set('error', 'Booking window must be a positive number.');
            Response::redirect('admin/settings');
        }

        $keys = [
            'company_name', 'contact_email', 'contact_phone', 'office_address',
            'currency_symbol', 'booking_window_days', 'timezone', 'maintenance_mode'
        ];
        $settingModel = new Setting();
        foreach ($keys as $key) {
            $settingModel->upsert($key, (string) Request::input($key, ''), 'general');
        }
        if (!empty($_FILES['logo']['name'])) {
            $logoPath = $this->uploadSettingImage('logo', 'logo');
            if ($logoPath !== null) {
                $settingModel->upsert('logo', $logoPath, 'general');
            }
        }
        $this->logActivity('settings', 'update', 'System settings updated');
        Session::set('success', 'System settings saved.');
        Response::redirect('admin/settings');
    }

    public function logs(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $logs = (new ActivityLog())->recent(100);
        $this->view('admin.logs', compact('logs'));
    }

    public function backup(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $this->view('admin.backup');
    }

    public function restore(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $this->view('admin.restore');
    }

    public function processRestore(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/restore');
        }

        if (empty($_FILES['backup_file']['name'])) {
            Session::set('error', 'Please upload a SQL backup file.');
            Response::redirect('admin/restore');
        }

        $file = $_FILES['backup_file'];
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'sql') {
            Session::set('error', 'Only SQL backup files are allowed.');
            Response::redirect('admin/restore');
        }

        try {
            $sql = file_get_contents($file['tmp_name']);
            if ($sql === false) {
                throw new \RuntimeException('Unable to read backup file.');
            }

            $pdo = Database::pdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            $statements = preg_split('/;\s*(\r?\n)+/', trim($sql)) ?: [];
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if ($statement === '' || str_starts_with($statement, '--')) {
                    continue;
                }
                $pdo->exec($statement);
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

            $this->logActivity('backup', 'restore', 'Database restored from uploaded backup');
            Session::set('success', 'Database restored successfully.');
            Response::redirect('admin/backup');
        } catch (\Throwable $throwable) {
            try {
                Database::pdo()->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable) {
            }
            Session::set('error', 'Restore failed: ' . $throwable->getMessage());
            Response::redirect('admin/restore');
        }
    }

    public function messages(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $messages = Database::pdo()->query("SELECT * FROM contact_messages WHERE deleted_at IS NULL ORDER BY id DESC")->fetchAll();
        $this->view('admin.messages', compact('messages'));
    }

    public function updateMessageStatus(int $id): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/messages');
        }

        $status = (string) Request::input('status', 'read');
        if (!in_array($status, ['new', 'read', 'closed'], true)) {
            Session::set('error', 'Invalid message status selected.');
            Response::redirect('admin/messages');
        }
        Database::pdo()->prepare("UPDATE contact_messages SET status = :status, updated_at = NOW() WHERE id = :id")->execute([
            'status' => $status,
            'id' => $id,
        ]);
        $this->logActivity('messages', 'update', 'Contact message #' . $id . ' marked as ' . $status);
        Session::set('success', 'Message updated.');
        Response::redirect('admin/messages');
    }

    public function createBackup(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/backup');
        }

        $tables = ['roles', 'users', 'password_resets', 'system_settings', 'buses', 'routes', 'schedules', 'schedule_seats', 'seat_blocks', 'bookings', 'payments', 'tickets', 'notifications', 'activity_logs', 'contact_messages'];
        $pdo = Database::pdo();
        $dump = "-- Public Bus Transport Ticketing System Backup\nSET FOREIGN_KEY_CHECKS=0;\n";
        foreach ($tables as $table) {
            $rows = $pdo->query("SELECT * FROM {$table}")->fetchAll(\PDO::FETCH_ASSOC);
            $dump .= "\nTRUNCATE TABLE {$table};\n";
            foreach ($rows as $row) {
                $columns = implode(', ', array_map(static fn($col) => "`{$col}`", array_keys($row)));
                $values = implode(', ', array_map(static fn($value) => $value === null ? 'NULL' : $pdo->quote((string) $value), array_values($row)));
                $dump .= "INSERT INTO {$table} ({$columns}) VALUES ({$values});\n";
            }
        }
        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="pbt_backup_' . date('Ymd_His') . '.sql"');
        echo $dump;
        $this->logActivity('backup', 'export', 'Database backup generated');
        exit;
    }

    public function delete(string $entity, int $id): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('admin/' . $entity);
        }

        $table = match ($entity) {
            'users' => 'users',
            'buses' => 'buses',
            'routes' => 'routes',
            'schedules' => 'schedules',
            'bookings' => 'bookings',
            'payments' => 'payments',
            'tickets' => 'tickets',
            'contact_messages' => 'contact_messages',
            default => null,
        };
        $redirectTarget = match ($entity) {
            'contact_messages' => 'admin/messages',
            'tickets' => 'admin/bookings',
            default => 'admin/' . $entity,
        };

        if ($table) {
            if ($entity === 'bookings') {
                $booking = Database::pdo()->prepare("SELECT schedule_id, seat_number FROM bookings WHERE id = :id AND deleted_at IS NULL LIMIT 1");
                $booking->execute(['id' => $id]);
                $booking = $booking->fetch();
                if ($booking) {
                    Database::pdo()->prepare("UPDATE bookings SET booking_status = 'cancelled', payment_status = 'refunded', deleted_at = NOW(), updated_at = NOW() WHERE id = :id")->execute(['id' => $id]);
                    Database::pdo()->prepare("UPDATE tickets SET status = 'void', updated_at = NOW() WHERE booking_id = :booking_id AND deleted_at IS NULL")->execute(['booking_id' => $id]);
                    Database::pdo()->prepare("UPDATE payments SET status = 'reversed', updated_at = NOW() WHERE booking_id = :booking_id AND deleted_at IS NULL")->execute(['booking_id' => $id]);
                    $this->restoreBookingSeat((int) $booking['schedule_id'], (string) $booking['seat_number']);
                    $this->logActivity('bookings', 'delete', 'Deleted booking record #' . $id);
                    Session::set('success', 'Booking deleted successfully.');
                    Response::redirect('admin/bookings');
                }
            }

            if ($entity === 'contact_messages') {
                Database::pdo()->prepare("UPDATE {$table} SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id")->execute(['id' => $id]);
                $this->logActivity($entity, 'delete', 'Deleted contact message #' . $id);
                Session::set('success', 'Message deleted successfully.');
                Response::redirect('admin/messages');
            }

            Database::pdo()->prepare("UPDATE {$table} SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id")->execute(['id' => $id]);
            $this->logActivity($entity, 'delete', 'Deleted ' . $entity . ' record #' . $id);
            Session::set('success', ucfirst($entity) . ' deleted successfully.');
        }

        Response::redirect($redirectTarget);
    }

    private function uploadBusImage(): ?string
    {
        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $file = $_FILES['image'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::set('error', 'Bus image upload failed.');
            Response::redirect('admin/buses');
        }

        if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
            Session::set('error', 'Bus image must be 3MB or smaller.');
            Response::redirect('admin/buses');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!in_array($mime, $allowed, true)) {
            Session::set('error', 'Invalid bus image.');
            Response::redirect('admin/buses');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'bus_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetDir = rtrim((string) config('app.upload_path'), '/\\');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $target = $targetDir . DIRECTORY_SEPARATOR . $name;
        move_uploaded_file($file['tmp_name'], $target);
        return 'storage/uploads/' . $name;
    }

    private function uploadSettingImage(string $field, string $prefix): ?string
    {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }

        $file = $_FILES[$field];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::set('error', 'Logo upload failed.');
            Response::redirect('admin/settings');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            Session::set('error', 'Logo must be 2MB or smaller.');
            Response::redirect('admin/settings');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!in_array($mime, $allowed, true)) {
            Session::set('error', 'Invalid logo file.');
            Response::redirect('admin/settings');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetDir = rtrim((string) config('app.upload_path'), '/\\');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $target = $targetDir . DIRECTORY_SEPARATOR . $name;
        move_uploaded_file($file['tmp_name'], $target);
        return 'storage/uploads/' . $name;
    }

    private function restoreBookingSeat(int $scheduleId, string $seatNumber): void
    {
        if ($scheduleId <= 0 || $seatNumber === '') {
            return;
        }

        $scheduleStmt = Database::pdo()->prepare("SELECT b.capacity AS bus_capacity
            FROM schedules s
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $scheduleStmt->execute(['id' => $scheduleId]);
        $schedule = $scheduleStmt->fetch();
        if (!$schedule) {
            return;
        }

        Database::pdo()->prepare("UPDATE schedules SET available_seats = available_seats + 1, updated_at = NOW() WHERE id = :id")->execute(['id' => $scheduleId]);
        (new SeatLayoutService())->ensureScheduleSeats($scheduleId, (int) $schedule['bus_capacity']);
        (new SeatLayoutService())->markAvailable($scheduleId, $seatNumber);
    }

    private function ensureSeatLayout(int $scheduleId): void
    {
        if ($scheduleId <= 0) {
            return;
        }

        $stmt = Database::pdo()->prepare("SELECT b.capacity AS bus_capacity
            FROM schedules s
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $scheduleId]);
        $schedule = $stmt->fetch();
        if (!$schedule) {
            return;
        }

        (new SeatLayoutService())->ensureScheduleSeats($scheduleId, (int) $schedule['bus_capacity']);
    }
}
