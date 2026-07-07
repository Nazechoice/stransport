<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Auth;
use Transport\Core\Csrf;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Core\Validator;
use Transport\Models\Booking;
use Transport\Models\Bus;
use Transport\Models\Route;
use Transport\Models\Schedule;
use Transport\Models\SeatBlock;
use Transport\Services\BookingService;
use Transport\Services\QrCodeService;
use Transport\Services\SeatLayoutService;
use Transport\Services\StatsService;

final class BookingController extends BaseController
{
    public function search(): void
    {
        $origin = trim((string) Request::input('origin', ''));
        $destination = trim((string) Request::input('destination', ''));
        $date = trim((string) Request::input('date', ''));
        $busId = (int) Request::input('bus_id', 0);
        $routeId = (int) Request::input('route_id', 0);
        $hasFilters = $origin !== '' || $destination !== '' || $date !== '' || $busId > 0 || $routeId > 0;
        $routes = (new Route())->searchable();
        $stats = (new StatsService())->counts();

        if (!$hasFilters) {
            $featuredTrips = (new Schedule())->upcomingTrips(6);
            $this->view('booking.search', compact('origin', 'destination', 'date', 'busId', 'routeId', 'routes', 'stats', 'featuredTrips'));
            return;
        }

        $schedules = (new Schedule())->searchByRoute($origin, $destination, $date ?: null, $busId > 0 ? $busId : null, $routeId > 0 ? $routeId : null);
        $featuredTrips = (new Schedule())->upcomingTrips(6);
        $this->view('booking.results', compact('origin', 'destination', 'date', 'busId', 'routeId', 'schedules', 'routes', 'stats', 'featuredTrips'));
    }

    public function show(int $scheduleId): void
    {
        $this->requireLogin();
        $schedule = Database::pdo()->prepare("SELECT s.*, r.origin, r.destination, r.fare AS route_fare, b.bus_number, b.bus_type, b.capacity
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $schedule->execute(['id' => $scheduleId]);
        $schedule = $schedule->fetch();
        if (!$schedule) {
            Session::set('error', 'Schedule not found.');
            Response::redirect('journey/search');
        }

        $bookedSeats = Database::pdo()->prepare("SELECT seat_number FROM bookings WHERE schedule_id = :schedule_id AND booking_status IN ('pending','confirmed','checked_in') AND deleted_at IS NULL");
        $bookedSeats->execute(['schedule_id' => $scheduleId]);
        $bookedSeats = array_column($bookedSeats->fetchAll(), 'seat_number');

        $blockedSeats = array_map(static fn($row) => $row['seat_number'], (new SeatBlock())->blockedForSchedule($scheduleId));
        $seatRows = (new SeatLayoutService())->seatsForSchedule($scheduleId, (int) $schedule['capacity']);
        $this->view('booking.seat-map', compact('schedule', 'bookedSeats', 'blockedSeats', 'seatRows'));
    }

    public function details(int $scheduleId): void
    {
        $schedule = Database::pdo()->prepare("SELECT s.*, r.origin, r.destination, r.stops, r.distance_km, r.estimated_minutes, r.fare AS route_fare, b.bus_number, b.registration_number, b.bus_type, b.capacity, b.image, b.status AS bus_status, d.full_name AS driver_name, d.phone AS driver_phone
            FROM schedules s
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses b ON b.id = s.bus_id
            LEFT JOIN users d ON d.id = s.driver_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $schedule->execute(['id' => $scheduleId]);
        $schedule = $schedule->fetch();
        if (!$schedule) {
            Session::set('error', 'Schedule not found.');
            Response::redirect('journey/search');
        }

        $bookedSeats = Database::pdo()->prepare("SELECT COUNT(*) FROM bookings WHERE schedule_id = :schedule_id AND booking_status IN ('pending','confirmed','checked_in') AND deleted_at IS NULL");
        $bookedSeats->execute(['schedule_id' => $scheduleId]);
        $bookedCount = (int) $bookedSeats->fetchColumn();
        $availableSeats = max(0, (int) ($schedule['available_seats'] ?? 0));
        $amenities = $this->deriveAmenities((string) ($schedule['bus_type'] ?? ''));

        $this->view('booking.details', compact('schedule', 'bookedCount', 'availableSeats', 'amenities'));
    }

    public function confirm(): void
    {
        $this->requireLogin();
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('journey/search');
        }

        $data = Request::all();
        $errors = Validator::required($data, [
            'schedule_id' => 'Schedule',
            'seat_number' => 'Seat number',
            'passenger_id' => 'Passenger',
            'payment_method' => 'Payment method',
            'payment_status' => 'Payment status',
        ]);

        if ($errors) {
            Session::flashOld($data);
            Session::set('errors', $errors);
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        if (!in_array($data['payment_method'] ?? '', ['cash', 'transfer', 'card'], true)) {
            Session::set('error', 'Invalid payment method selected.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        if (!in_array($data['booking_type'] ?? 'online', ['online', 'walk_in'], true)) {
            Session::set('error', 'Invalid booking type selected.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        if (!in_array($data['payment_status'] ?? 'paid', ['pending', 'paid'], true)) {
            Session::set('error', 'Invalid payment status selected.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        if (!preg_match('/^[A-Z]\d{1,2}$/i', (string) ($data['seat_number'] ?? ''))) {
            Session::set('error', 'Invalid seat number selected.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        $currentUser = Auth::user();
        $actorRole = $currentUser['role'] ?? null;
        if (($data['booking_type'] ?? 'online') === 'online' && $actorRole !== 'passenger') {
            Session::set('error', 'Only passengers can create online bookings.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        if (($data['booking_type'] ?? 'online') === 'walk_in' && !in_array($actorRole, ['super_admin', 'administrator', 'ticket_officer'], true)) {
            Session::set('error', 'Only authorized staff can create walk-in bookings.');
            Response::redirect('booking/' . (int) $data['schedule_id']);
        }

        $schedule = Database::pdo()->prepare("SELECT s.*, b.capacity AS bus_capacity
            FROM schedules s
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $schedule->execute(['id' => (int) $data['schedule_id']]);
        $schedule = $schedule->fetch();
        if (!$schedule) {
            Session::set('error', 'Schedule not found.');
            Response::redirect('journey/search');
        }

        $availableSeats = (new SeatLayoutService())->matrixForSchedule((int) $schedule['id'], (int) $schedule['bus_capacity']);
        $validSeatNumbers = array_column($availableSeats, 'seat');
        if (!in_array((string) $data['seat_number'], $validSeatNumbers, true)) {
            Session::set('error', 'Please choose a valid seat on the selected bus.');
            Response::redirect('booking/' . (int) $schedule['id']);
        }

        if ((new Booking())->seatTaken((int) $schedule['id'], (string) $data['seat_number'])) {
            Session::set('error', 'This seat has already been booked.');
            Response::redirect('booking/' . (int) $schedule['id']);
        }

        $actor = $currentUser ?? [];
        $passengerId = (int) $data['passenger_id'];
        if (($data['booking_type'] ?? 'online') === 'online') {
            $passengerId = (int) ($actor['id'] ?? 0);
        }
        $input = [
            'schedule_id' => (int) $schedule['id'],
            'passenger_id' => $passengerId,
            'seat_number' => trim((string) $data['seat_number']),
            'booking_type' => $data['booking_type'] ?? 'online',
            'total_amount' => (float) ($schedule['fare'] ?? $schedule['route_fare'] ?? 0),
            'payment_status' => $data['payment_status'],
            'payment_method' => $data['payment_method'],
            'notes' => $data['notes'] ?? null,
        ];

        try {
            $result = (new BookingService())->createBooking($input, $actor);
            $this->logActivity('bookings', 'create', 'Created booking ' . $result['booking_number'] . ' for seat ' . $input['seat_number']);
            Session::set('success', $input['payment_status'] === 'paid'
                ? 'Booking completed successfully.'
                : 'Booking submitted for approval. Your ticket will be issued once payment is confirmed.');
            Response::redirect('bookings/success/' . $result['booking_id']);
        } catch (\Throwable $throwable) {
            Session::set('error', $throwable->getMessage());
            Response::redirect('booking/' . (int) $schedule['id']);
        }
    }

    public function success(int $bookingId): void
    {
        $this->requireLogin();
        $summary = $this->bookingSummary($bookingId);
        if (!$summary || !$this->canAccessBooking($summary)) {
            Session::set('error', 'Booking not found or access denied.');
            Response::redirect('bookings/history');
        }

        $this->view('booking.success', compact('summary'));
    }

    public function history(): void
    {
        $this->requireLogin();
        $user = $this->user();
        $bookings = (new Booking())->fetchAll("SELECT b.*, s.departure_date, s.departure_time, r.origin, r.destination
            FROM bookings b
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            WHERE b.passenger_id = :passenger_id AND b.deleted_at IS NULL
            ORDER BY b.id DESC", ['passenger_id' => $user['id']]);
        $this->view('booking.history', compact('bookings'));
    }

    public function walkIn(): void
    {
        $this->requireRoles(['super_admin', 'administrator', 'ticket_officer']);
        $schedules = (new Schedule())->forDashboard();
        $passengers = (new \Transport\Models\User())->findByRole('passenger');
        $this->view('booking.walkin', compact('schedules', 'passengers'));
    }

    public function searchPassengers(): void
    {
        $this->requireRoles(['super_admin', 'administrator', 'ticket_officer']);
        $term = trim((string) Request::input('term', ''));
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT id, full_name, email, phone FROM users WHERE deleted_at IS NULL AND role = 'passenger' AND (full_name LIKE :term OR email LIKE :term OR phone LIKE :term) ORDER BY full_name ASC LIMIT 10");
        $stmt->execute(['term' => '%' . $term . '%']);
        $this->json(['data' => $stmt->fetchAll()]);
    }

    private function bookingSummary(int $bookingId): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT b.*, s.departure_date, s.departure_time, s.arrival_time, s.available_seats, r.origin, r.destination, r.stops, r.distance_km, r.estimated_minutes, r.fare AS route_fare, bus.bus_number, bus.registration_number, bus.bus_type, bus.capacity, bus.image, p.payment_reference, p.method AS payment_method, p.status AS payment_record_status, t.id AS ticket_id, t.ticket_number, t.qr_token, t.status AS ticket_status, u.full_name AS passenger_name, u.phone AS passenger_phone
            FROM bookings b
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses bus ON bus.id = s.bus_id
            INNER JOIN users u ON u.id = b.passenger_id
            LEFT JOIN payments p ON p.booking_id = b.id AND p.deleted_at IS NULL
            LEFT JOIN tickets t ON t.booking_id = b.id AND t.deleted_at IS NULL
            WHERE b.id = :id AND b.deleted_at IS NULL
            ORDER BY p.id DESC, t.id DESC
            LIMIT 1");
        $stmt->execute(['id' => $bookingId]);
        $summary = $stmt->fetch();

        return $summary ?: null;
    }

    private function canAccessBooking(array $booking): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if (in_array($user['role'] ?? '', ['super_admin', 'administrator', 'ticket_officer'], true)) {
            return true;
        }

        return ($user['role'] ?? '') === 'passenger' && (int) ($booking['passenger_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }

    private function deriveAmenities(string $busType): array
    {
        $busType = strtolower($busType);

        return match (true) {
            str_contains($busType, 'luxury') => ['Air conditioning', 'Reclining seats', 'Charging ports', 'Luggage support'],
            str_contains($busType, 'executive') => ['Air conditioning', 'Comfort seating', 'Luggage support'],
            default => ['Comfort seating', 'Luggage support', 'Professional driver support'],
        };
    }
}
