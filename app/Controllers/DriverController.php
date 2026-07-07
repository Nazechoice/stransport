<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Csrf;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Models\Schedule;

final class DriverController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireRoles(['driver']);
        (new DashboardController())->driver();
    }

    public function trips(): void
    {
        $this->requireRoles(['driver']);
        $trips = Database::pdo()->prepare("SELECT s.*, r.origin, r.destination, b.bus_number
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.driver_id = :driver_id AND s.deleted_at IS NULL
            ORDER BY s.departure_date DESC, s.departure_time DESC");
        $trips->execute(['driver_id' => $this->user()['id']]);
        $trips = $trips->fetchAll();
        $this->view('driver.trips', compact('trips'));
    }

    public function manifest(?int $scheduleId = null): void
    {
        $this->requireRoles(['driver']);
        if ($scheduleId === null) {
            Response::redirect('driver/trips');
        }

        $schedule = Database::pdo()->prepare("SELECT s.*, r.origin, r.destination, b.bus_number
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.driver_id = :driver_id AND s.deleted_at IS NULL LIMIT 1");
        $schedule->execute(['id' => $scheduleId, 'driver_id' => $this->user()['id']]);
        $schedule = $schedule->fetch();
        if (!$schedule) {
            Session::set('error', 'Trip not found.');
            Response::redirect('driver/trips');
        }
        $manifest = Database::pdo()->prepare("SELECT b.booking_number, b.seat_number, b.booking_status, u.full_name, u.phone
            FROM bookings b
            INNER JOIN users u ON u.id = b.passenger_id
            WHERE b.schedule_id = :schedule_id AND b.deleted_at IS NULL
            ORDER BY b.seat_number ASC");
        $manifest->execute(['schedule_id' => $scheduleId]);
        $manifest = $manifest->fetchAll();
        $this->view('driver.manifest', compact('schedule', 'manifest'));
    }

    public function completeTrip(int $scheduleId): void
    {
        $this->requireRoles(['driver']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('driver/trips');
        }

        Database::pdo()->prepare("UPDATE schedules SET status = 'completed', updated_at = NOW() WHERE id = :id AND driver_id = :driver_id")->execute([
            'id' => $scheduleId,
            'driver_id' => $this->user()['id'],
        ]);
        $this->logActivity('trips', 'complete', 'Driver completed trip ' . $scheduleId);
        Session::set('success', 'Trip marked as completed.');
        Response::redirect('driver/trips');
    }
}
