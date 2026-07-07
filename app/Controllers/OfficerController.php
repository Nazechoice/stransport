<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Csrf;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Models\Booking;
use Transport\Models\Schedule;

final class OfficerController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireRoles(['ticket_officer']);
        (new DashboardController())->officer();
    }

    public function walkIn(): void
    {
        $this->requireRoles(['ticket_officer', 'administrator', 'super_admin']);
        $schedules = (new Schedule())->forDashboard();
        $passengers = (new \Transport\Models\User())->findByRole('passenger');
        $this->view('officer.walkin', compact('schedules', 'passengers'));
    }

    public function verify(): void
    {
        $this->requireRoles(['ticket_officer']);

        if (Request::isPost() && !Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('officer/verify');
        }

        $ticketNumber = trim((string) Request::input('ticket_number', ''));
        $ticket = null;
        if ($ticketNumber !== '') {
            $stmt = Database::pdo()->prepare("SELECT t.*, b.seat_number, b.booking_number, u.full_name AS passenger_name
                FROM tickets t
                INNER JOIN bookings b ON b.id = t.booking_id
                INNER JOIN users u ON u.id = b.passenger_id
                WHERE t.ticket_number = :ticket_number AND t.deleted_at IS NULL LIMIT 1");
            $stmt->execute(['ticket_number' => $ticketNumber]);
            $ticket = $stmt->fetch();
        }
        $this->view('officer.verify', compact('ticket', 'ticketNumber'));
    }


    public function scanner(): void
    {
        $this->requireRoles(['ticket_officer']);
        $this->view('officer.scanner');
    }

    public function boardingList(?int $scheduleId = null): void
    {
        $this->requireRoles(['ticket_officer']);
        if ($scheduleId === null) {
            $todayTrips = Database::pdo()->query("SELECT s.*, r.origin, r.destination, b.bus_number
                FROM schedules s
                INNER JOIN routes r ON r.id = s.route_id
                INNER JOIN buses b ON b.id = s.bus_id
                WHERE s.departure_date = CURDATE() AND s.status = 'scheduled' AND s.deleted_at IS NULL
                ORDER BY s.departure_time ASC")->fetchAll();
            $this->view('officer.boarding-index', compact('todayTrips'));
            return;
        }

        $schedule = (new Schedule())->find($scheduleId);
        if (!$schedule) {
            Session::set('error', 'Trip not found.');
            Response::redirect('officer/boarding-list');
        }

        $manifest = Database::pdo()->prepare("SELECT b.booking_number, b.seat_number, b.booking_status, u.full_name, u.phone, t.ticket_number
            FROM bookings b
            INNER JOIN users u ON u.id = b.passenger_id
            LEFT JOIN tickets t ON t.booking_id = b.id
            WHERE b.schedule_id = :schedule_id AND b.deleted_at IS NULL
            ORDER BY b.seat_number ASC");
        $manifest->execute(['schedule_id' => $scheduleId]);
        $manifest = $manifest->fetchAll();
        $this->view('officer.boarding-list', compact('schedule', 'manifest'));
    }

    public function markTicketUsed(int $ticketId): void
    {
        $this->requireRoles(['ticket_officer']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('officer/verify');
        }

        $stmt = Database::pdo()->prepare("SELECT t.id, t.ticket_number, t.status, b.id AS booking_id
            FROM tickets t
            INNER JOIN bookings b ON b.id = t.booking_id
            WHERE t.id = :id AND t.deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $ticketId]);
        $ticket = $stmt->fetch();
        if (!$ticket) {
            Session::set('error', 'Ticket not found.');
            Response::redirect('officer/verify');
        }

        if (($ticket['status'] ?? '') === 'used') {
            Session::set('error', 'This ticket has already been marked as used.');
            Response::redirect('officer/verify?ticket_number=' . rawurlencode((string) $ticket['ticket_number']));
        }

        Database::pdo()->prepare("UPDATE tickets SET status = 'used', updated_at = NOW() WHERE id = :id")->execute(['id' => $ticketId]);
        Database::pdo()->prepare("UPDATE bookings SET booking_status = 'checked_in', updated_at = NOW() WHERE id = :id")->execute(['id' => (int) $ticket['booking_id']]);
        $this->logActivity('tickets', 'use', 'Marked ticket #' . $ticketId . ' as used');
        Session::set('success', 'Ticket marked as used.');
        Response::redirect('officer/verify?ticket_number=' . rawurlencode((string) $ticket['ticket_number']));
    }
}
