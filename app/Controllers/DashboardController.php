<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Auth;
use Transport\Models\Booking;
use Transport\Models\Notification;
use Transport\Models\Schedule;
use Transport\Models\Ticket;
use Transport\Services\StatsService;

final class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $role = Auth::user()['role'];
        match ($role) {
            'super_admin', 'administrator' => $this->admin(),
            'ticket_officer' => $this->officer(),
            'driver' => $this->driver(),
            default => $this->passenger(),
        };
    }

    public function admin(): void
    {
        $this->requireRoles(['super_admin', 'administrator']);
        $stats = (new StatsService())->counts();
        $recentBookings = (new StatsService())->recentBookings();
        $recentPayments = (new StatsService())->recentPayments();
        $popularRoutes = (new StatsService())->popularRoutes();
        $this->view('dashboard.admin', compact('stats', 'recentBookings', 'recentPayments', 'popularRoutes'));
    }

    public function passenger(): void
    {
        $this->requireRoles(['passenger']);
        $user = $this->user();
        $bookings = (new Booking())->fetchAll("SELECT b.*, s.departure_date, s.departure_time, r.origin, r.destination, t.ticket_number
            FROM bookings b
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            LEFT JOIN tickets t ON t.booking_id = b.id
            WHERE b.passenger_id = :passenger_id AND b.deleted_at IS NULL
            ORDER BY b.id DESC", ['passenger_id' => $user['id']]);
        $notifications = (new Notification())->forUser((int) $user['id']);
        $upcomingTrips = (new Schedule())->fetchAll("SELECT s.*, r.origin, r.destination, b.bus_number
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.departure_date >= CURDATE() AND s.status = 'scheduled' AND s.deleted_at IS NULL
            ORDER BY s.departure_date ASC, s.departure_time ASC
            LIMIT 6");
        $ticketCount = (new Ticket())->fetchOne(
            "SELECT COUNT(*) AS total FROM tickets t INNER JOIN bookings b ON b.id = t.booking_id WHERE b.passenger_id = :passenger_id AND t.deleted_at IS NULL",
            ['passenger_id' => $user['id']]
        );
        $stats = [
            'bookings' => count($bookings),
            'tickets' => (int) ($ticketCount['total'] ?? 0),
        ];
        $this->view('dashboard.passenger', compact('bookings', 'notifications', 'upcomingTrips', 'stats'));
    }

    public function driver(): void
    {
        $this->requireRoles(['driver']);
        $user = $this->user();
        $trips = (new Schedule())->fetchAll("SELECT s.*, r.origin, r.destination, b.bus_number, COUNT(bo.id) AS bookings
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            LEFT JOIN bookings bo ON bo.schedule_id = s.id AND bo.deleted_at IS NULL
            WHERE s.driver_id = :driver_id AND s.deleted_at IS NULL
            GROUP BY s.id
            ORDER BY s.departure_date DESC, s.departure_time DESC", ['driver_id' => $user['id']]);
        $this->view('dashboard.driver', compact('trips'));
    }

    public function officer(): void
    {
        $this->requireRoles(['ticket_officer']);
        $todayTrips = (new Schedule())->fetchAll("SELECT s.*, r.origin, r.destination, b.bus_number
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.departure_date = CURDATE() AND s.status = 'scheduled' AND s.deleted_at IS NULL
            ORDER BY s.departure_time ASC");
        $stats = (new StatsService())->counts();
        $this->view('dashboard.officer', compact('todayTrips', 'stats'));
    }
}
