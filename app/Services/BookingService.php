<?php

declare(strict_types=1);

namespace Transport\Services;

use Exception;
use Transport\Models\Booking;
use Transport\Models\Notification;
use Transport\Models\Payment;
use Transport\Models\Schedule;
use Transport\Models\Ticket;
use Transport\Models\User;
use Transport\Services\SeatLayoutService;

final class BookingService
{
    private Booking $booking;
    private Payment $payment;
    private Ticket $ticket;
    private Schedule $schedule;
    private User $user;
    private Notification $notification;
    private SeatLayoutService $seatLayout;

    public function __construct()
    {
        $this->booking = new Booking();
        $this->payment = new Payment();
        $this->ticket = new Ticket();
        $this->schedule = new Schedule();
        $this->user = new User();
        $this->notification = new Notification();
        $this->seatLayout = new SeatLayoutService();
    }

    public function createBooking(array $input, array $actor): array
    {
        $pdo = \Transport\Core\Database::pdo();
        $pdo->beginTransaction();

        try {
            $scheduleStmt = $pdo->prepare("SELECT s.*, b.capacity AS bus_capacity
                FROM schedules s
                INNER JOIN buses b ON b.id = s.bus_id
                WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1 FOR UPDATE");
            $scheduleStmt->execute(['id' => (int) $input['schedule_id']]);
            $schedule = $scheduleStmt->fetch();
            if (!$schedule) {
                throw new Exception('Selected schedule not found.');
            }

            $this->seatLayout->ensureScheduleSeats((int) $schedule['id'], (int) $schedule['bus_capacity']);

            $validSeatNumbers = array_column($this->seatLayout->matrixForSchedule((int) $schedule['id'], (int) $schedule['bus_capacity']), 'seat');
            if (!in_array((string) $input['seat_number'], $validSeatNumbers, true)) {
                throw new Exception('Please choose a valid seat on the selected bus.');
            }

            if ((int) ($schedule['available_seats'] ?? 0) <= 0) {
                throw new Exception('No seats are available on this schedule.');
            }

            if ($this->booking->seatTaken((int) $schedule['id'], $input['seat_number'])) {
                throw new Exception('Seat already booked for this schedule.');
            }

            $blocked = $this->booking->fetchOne(
                "SELECT id FROM seat_blocks WHERE schedule_id = :schedule_id AND seat_number = :seat_number AND deleted_at IS NULL LIMIT 1",
                ['schedule_id' => (int) $schedule['id'], 'seat_number' => $input['seat_number']]
            );
            if ($blocked) {
                throw new Exception('Seat is blocked for this schedule.');
            }

            $bookingNumber = $this->generateBookingNumber();
            $bookingStatus = $input['payment_status'] === 'paid' ? 'confirmed' : 'pending';
            $bookingId = $this->booking->create([
                'booking_number' => $bookingNumber,
                'passenger_id' => (int) $input['passenger_id'],
                'schedule_id' => (int) $schedule['id'],
                'bus_id' => (int) $schedule['bus_id'],
                'route_id' => (int) $schedule['route_id'],
                'seat_number' => $input['seat_number'],
                'booking_type' => $input['booking_type'],
                'booking_status' => $bookingStatus,
                'total_amount' => (float) $input['total_amount'],
                'payment_status' => $input['payment_status'],
                'notes' => $input['notes'] ?? null,
                'created_by' => $actor['id'],
            ]);

            $paymentId = $this->payment->create([
                'booking_id' => $bookingId,
                'amount' => (float) $input['total_amount'],
                'method' => $input['payment_method'],
                'payment_reference' => $this->generatePaymentReference(),
                'status' => $input['payment_status'] === 'paid' ? 'successful' : 'pending',
                'paid_at' => $input['payment_status'] === 'paid' ? date('Y-m-d H:i:s') : null,
                'received_by' => $actor['id'],
            ]);

            $ticketId = null;
            $ticketNumber = null;
            $qrToken = null;
            if ($input['payment_status'] === 'paid') {
                $ticket = $this->issueTicketForBooking($bookingId, $actor, $bookingNumber, (int) $schedule['id'], $input['seat_number']);
                $ticketId = $ticket['ticket_id'];
                $ticketNumber = $ticket['ticket_number'];
                $qrToken = $ticket['qr_token'];
            }

            $pdo->prepare("UPDATE schedules SET available_seats = GREATEST(available_seats - 1, 0), updated_at = NOW() WHERE id = :id")
                ->execute(['id' => (int) $schedule['id']]);
            $this->seatLayout->markBooked((int) $schedule['id'], (string) $input['seat_number']);

            $this->notification->create([
                'user_id' => (int) $input['passenger_id'],
                'title' => $input['payment_status'] === 'paid' ? 'Booking confirmed' : 'Booking pending approval',
                'message' => $input['payment_status'] === 'paid'
                    ? 'Your booking ' . $bookingNumber . ' has been confirmed for seat ' . $input['seat_number'] . '.'
                    : 'Your booking ' . $bookingNumber . ' is waiting for approval before ticket issuance.',
                'type' => 'booking',
                'is_read' => 0,
            ]);

            $pdo->commit();

            return [
                'booking_id' => $bookingId,
                'payment_id' => $paymentId,
                'ticket_id' => $ticketId,
                'booking_number' => $bookingNumber,
                'ticket_number' => $ticketNumber,
                'qr_token' => $qrToken,
            ];
        } catch (\Throwable $throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $throwable;
        }
    }

    private function generateBookingNumber(): string
    {
        return config('ticket.prefix') . '-BKG-' . strtoupper(bin2hex(random_bytes(4)));
    }

    private function generatePaymentReference(): string
    {
        return config('ticket.prefix') . '-PAY-' . strtoupper(bin2hex(random_bytes(4)));
    }

    private function generateTicketNumber(): string
    {
        return config('ticket.prefix') . '-TKT-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function issueTicketForBooking(int $bookingId, array $actor, ?string $bookingNumber = null, ?int $scheduleId = null, ?string $seatNumber = null): array
    {
        $pdo = \Transport\Core\Database::pdo();
        $startedTransaction = false;
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
            $startedTransaction = true;
        }

        try {
            $bookingStmt = $pdo->prepare("SELECT b.*, s.bus_id, s.route_id, s.fare, s.departure_date, s.departure_time, bus.capacity AS bus_capacity
                FROM bookings b
                INNER JOIN schedules s ON s.id = b.schedule_id
                INNER JOIN buses bus ON bus.id = s.bus_id
                WHERE b.id = :id AND b.deleted_at IS NULL LIMIT 1 FOR UPDATE");
            $bookingStmt->execute(['id' => $bookingId]);
            $booking = $bookingStmt->fetch();
            if (!$booking) {
                throw new Exception('Booking not found.');
            }

            $this->seatLayout->ensureScheduleSeats((int) $booking['schedule_id'], (int) $booking['bus_capacity']);

            $existingTicket = $this->ticket->fetchOne("SELECT * FROM tickets WHERE booking_id = :booking_id AND deleted_at IS NULL LIMIT 1", [
                'booking_id' => $bookingId,
            ]);
            if ($existingTicket) {
                if ($startedTransaction) {
                    $pdo->commit();
                }
                return [
                    'ticket_id' => (int) $existingTicket['id'],
                    'ticket_number' => $existingTicket['ticket_number'],
                    'qr_token' => $existingTicket['qr_token'],
                ];
            }

            $bookingNumber = $bookingNumber ?: (string) $booking['booking_number'];
            $scheduleId = $scheduleId ?: (int) $booking['schedule_id'];
            $seatNumber = $seatNumber ?: (string) $booking['seat_number'];
            $ticketNumber = $this->generateTicketNumber();
            $qrToken = bin2hex(random_bytes(16));

            $ticketId = $this->ticket->create([
                'ticket_number' => $ticketNumber,
                'booking_id' => $bookingId,
                'qr_token' => $qrToken,
                'qr_data' => json_encode([
                    'booking_number' => $bookingNumber,
                    'ticket_number' => $ticketNumber,
                    'schedule_id' => $scheduleId,
                    'seat_number' => $seatNumber,
                    'verification_url' => url('tickets/verify/' . $qrToken),
                ], JSON_UNESCAPED_SLASHES),
                'status' => 'active',
                'issued_by' => $actor['id'],
                'issued_at' => date('Y-m-d H:i:s'),
            ]);

            $pdo->prepare("UPDATE bookings SET booking_status = 'confirmed', payment_status = 'paid', updated_at = NOW() WHERE id = :id")
                ->execute(['id' => $bookingId]);
            $latestPayment = $pdo->prepare("SELECT id FROM payments WHERE booking_id = :booking_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
            $latestPayment->execute(['booking_id' => $bookingId]);
            $paymentId = (int) ($latestPayment->fetchColumn() ?: 0);
            if ($paymentId > 0) {
                $pdo->prepare("UPDATE payments SET status = 'successful', paid_at = NOW(), received_by = :received_by, updated_at = NOW() WHERE id = :id")
                    ->execute(['id' => $paymentId, 'received_by' => $actor['id']]);
            }

            $this->notification->create([
                'user_id' => (int) $booking['passenger_id'],
                'title' => 'Ticket approved',
                'message' => 'Your ticket for booking ' . $bookingNumber . ' has been approved and issued.',
                'type' => 'booking',
                'is_read' => 0,
            ]);

            if ($startedTransaction) {
                $pdo->commit();
            }

            return [
                'ticket_id' => $ticketId,
                'ticket_number' => $ticketNumber,
                'qr_token' => $qrToken,
            ];
        } catch (\Throwable $throwable) {
            if ($startedTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $throwable;
        }
    }
}
