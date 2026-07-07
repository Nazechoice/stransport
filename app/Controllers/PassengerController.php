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
use Transport\Models\Notification;
use Transport\Models\Ticket;
use Transport\Models\User;

final class PassengerController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireRoles(['passenger']);
        (new DashboardController())->passenger();
    }

    public function profile(): void
    {
        $this->requireRoles(['passenger']);
        $user = (new User())->find((int) $this->user()['id']);
        $this->view('passenger.profile', compact('user'));
    }

    public function updateProfile(): void
    {
        $this->requireRoles(['passenger']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('passenger/profile');
        }

        $user = $this->user();
        $errors = Validator::required(Request::all(), [
            'full_name' => 'Full name',
            'phone' => 'Phone',
        ]);
        if ($errors) {
            Session::set('errors', $errors);
            Response::redirect('passenger/profile');
        }
        $photo = $this->handleUpload('photo', $user['id']);
        (new User())->updateProfile((int) $user['id'], [
            'full_name' => trim((string) Request::input('full_name')),
            'phone' => trim((string) Request::input('phone')),
            'photo' => $photo ?? ($user['photo'] ?? null),
        ]);
        $this->logActivity('profile', 'update', 'Passenger profile updated');

        Session::set('success', 'Profile updated successfully.');
        Response::redirect('passenger/profile');
    }

    public function notifications(): void
    {
        $this->requireRoles(['passenger']);
        $notifications = (new Notification())->forUser((int) $this->user()['id']);
        $this->view('passenger.notifications', compact('notifications'));
    }

    public function tickets(): void
    {
        $this->requireRoles(['passenger']);
        $tickets = Database::pdo()->prepare("SELECT t.*, b.booking_number, b.seat_number, r.origin, r.destination, s.departure_date, s.departure_time
            FROM tickets t
            INNER JOIN bookings b ON b.id = t.booking_id
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            WHERE b.passenger_id = :passenger_id AND t.deleted_at IS NULL
            ORDER BY t.id DESC");
        $tickets->execute(['passenger_id' => $this->user()['id']]);
        $tickets = $tickets->fetchAll();
        $this->view('passenger.tickets', compact('tickets'));
    }

    public function travelHistory(): void
    {
        $this->requireRoles(['passenger']);
        $history = Database::pdo()->prepare("SELECT b.booking_number, b.seat_number, b.booking_status, b.payment_status, r.origin, r.destination, s.departure_date, s.departure_time, t.ticket_number, t.status AS ticket_status
            FROM bookings b
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            LEFT JOIN tickets t ON t.booking_id = b.id
            WHERE b.passenger_id = :passenger_id AND b.deleted_at IS NULL
            ORDER BY b.id DESC");
        $history->execute(['passenger_id' => $this->user()['id']]);
        $history = $history->fetchAll();
        $this->view('passenger.travel-history', compact('history'));
    }

    public function changePassword(): void
    {
        $this->requireRoles(['passenger']);
        $this->view('passenger.password');
    }

    public function updatePassword(): void
    {
        $this->requireRoles(['passenger']);
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('passenger/password');
        }

        $current = (string) Request::input('current_password');
        $new = (string) Request::input('new_password');
        $confirm = (string) Request::input('confirm_password');

        if ($new !== $confirm || strlen($new) < 8) {
            Session::set('error', 'Passwords do not match or are too short.');
            Response::redirect('passenger/password');
        }

        $user = (new User())->findByEmail($this->user()['email']);
        if (!$user || !password_verify($current, $user['password_hash'])) {
            Session::set('error', 'Current password is incorrect.');
            Response::redirect('passenger/password');
        }

        (new User())->updatePassword((int) $user['id'], password_hash($new, PASSWORD_DEFAULT));
        $this->logActivity('profile', 'password_change', 'Passenger password changed');
        Session::set('success', 'Password changed successfully.');
        Response::redirect('passenger/password');
    }

    private function handleUpload(string $field, int $userId): ?string
    {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }

        $file = $_FILES[$field];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::set('error', 'Upload failed.');
            Response::redirect('passenger/profile');
        }

        if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
            Session::set('error', 'Profile image must be 2MB or smaller.');
            Response::redirect('passenger/profile');
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!in_array($mime, $allowed, true)) {
            Session::set('error', 'Invalid image file type.');
            Response::redirect('passenger/profile');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'user_' . $userId . '_' . time() . '.' . $ext;
        $target = rtrim((string) config('app.upload_path'), '/\\') . DIRECTORY_SEPARATOR . $name;
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0775, true);
        }
        move_uploaded_file($file['tmp_name'], $target);
        return 'storage/uploads/' . $name;
    }
}
