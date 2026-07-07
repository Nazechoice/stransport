<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Auth;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Models\Ticket as TicketModel;
use Transport\Services\PdfService;
use Transport\Services\QrCodeService;

final class TicketController extends BaseController
{
    public function show(int $ticketId): void
    {
        $this->requireLogin();
        $ticket = $this->ticketData($ticketId);
        if (!$ticket || !$this->canAccessTicket($ticket)) {
            Session::set('error', 'Ticket not found or access denied.');
            Response::redirect($this->ticketRedirectTarget());
        }

        $svg = (new QrCodeService())->renderSvg(url('tickets/verify/' . $ticket['qr_token']));
        $this->view('tickets.show', compact('ticket', 'svg'));
    }

    public function print(int $ticketId): void
    {
        $this->requireLogin();
        $ticket = $this->ticketData($ticketId);
        if (!$ticket || !$this->canAccessTicket($ticket)) {
            Session::set('error', 'Ticket not found or access denied.');
            Response::redirect($this->ticketRedirectTarget());
        }
        $svg = (new QrCodeService())->renderSvg(url('tickets/verify/' . $ticket['qr_token']));
        $this->view('tickets.print', compact('ticket', 'svg'));
    }

    public function download(int $ticketId): void
    {
        $this->requireLogin();
        $ticket = $this->ticketData($ticketId);
        if (!$ticket || !$this->canAccessTicket($ticket)) {
            Session::set('error', 'Ticket not found or access denied.');
            Response::redirect($this->ticketRedirectTarget());
        }
        $svg = (new QrCodeService())->renderSvg(url('tickets/verify/' . $ticket['qr_token']));
        ob_start();
        $this->view('tickets.print', compact('ticket', 'svg'));
        $html = ob_get_clean() ?: '';
        (new PdfService())->downloadHtmlAsPdfLikeResponse($html, $ticket['ticket_number'] . '.pdf');
    }

    public function verify(string $token): void
    {
        if (!Auth::check() || !in_array(Auth::user()['role'] ?? '', ['super_admin', 'administrator', 'ticket_officer'], true)) {
            Response::json(['found' => false, 'message' => 'Unauthorized'], 403);
        }

        $row = Database::pdo()->prepare("SELECT t.*, b.booking_number, b.seat_number, u.full_name AS passenger_name
            FROM tickets t
            INNER JOIN bookings b ON b.id = t.booking_id
            INNER JOIN users u ON u.id = b.passenger_id
            WHERE t.qr_token = :token AND t.deleted_at IS NULL LIMIT 1");
        $row->execute(['token' => $token]);
        $ticket = $row->fetch();
        $this->json(['found' => (bool) $ticket, 'ticket' => $ticket]);
    }

    private function ticketData(int $ticketId): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT t.*, b.booking_number, b.passenger_id, b.seat_number, b.total_amount, b.payment_status, s.departure_date, s.departure_time, s.arrival_time, r.origin, r.destination, bus.bus_number, bus.registration_number, u.full_name AS passenger_name, u.phone AS passenger_phone
            FROM tickets t
            INNER JOIN bookings b ON b.id = t.booking_id
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            INNER JOIN buses bus ON bus.id = b.bus_id
            INNER JOIN users u ON u.id = b.passenger_id
            WHERE t.id = :id AND t.deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => $ticketId]);
        $ticket = $stmt->fetch();
        return $ticket ?: null;
    }

    private function canAccessTicket(array $ticket): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if (in_array($user['role'] ?? '', ['super_admin', 'administrator', 'ticket_officer'], true)) {
            return true;
        }

        return ($user['role'] ?? '') === 'passenger' && (int) ($ticket['passenger_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }

    private function ticketRedirectTarget(): string
    {
        $role = Auth::user()['role'] ?? null;

        return $role === 'passenger' ? 'passenger/tickets' : 'dashboard';
    }
}
