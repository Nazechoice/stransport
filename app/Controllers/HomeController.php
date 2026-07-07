<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Csrf;
use Transport\Core\Database;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Core\Validator;
use Transport\Models\Route;
use Transport\Services\StatsService;

final class HomeController extends BaseController
{
    public function index(): void
    {
        $stats = (new StatsService())->counts();
        $routes = (new Route())->searchable();
        $popularRoutes = (new StatsService())->popularRoutes();

        $this->view('landing.home', compact('stats', 'routes', 'popularRoutes'));
    }

    public function about(): void
    {
        $stats = (new StatsService())->counts();
        $routes = (new Route())->searchable();
        $popularRoutes = (new StatsService())->popularRoutes();

        $this->view('landing.about', compact('stats', 'routes', 'popularRoutes'));
    }

    public function routes(): void
    {
        $stats = (new StatsService())->counts();
        $routes = (new Route())->searchable();
        $popularRoutes = (new StatsService())->popularRoutes();

        $this->view('landing.routes', compact('stats', 'routes', 'popularRoutes'));
    }

    public function services(): void
    {
        $stats = (new StatsService())->counts();
        $popularRoutes = (new StatsService())->popularRoutes();

        $this->view('landing.services', compact('stats', 'popularRoutes'));
    }

    public function vehicles(): void
    {
        $stats = (new StatsService())->counts();
        $vehicles = Database::pdo()->query("SELECT b.*, u.full_name AS driver_name, u.phone AS driver_phone
            FROM buses b
            LEFT JOIN users u ON u.id = b.driver_id
            WHERE b.deleted_at IS NULL
            ORDER BY b.status = 'active' DESC, b.bus_number ASC")->fetchAll();
        $this->view('landing.vehicles', compact('stats', 'vehicles'));
    }

    public function contactPage(): void
    {
        $stats = (new StatsService())->counts();
        $this->view('landing.contact', compact('stats'));
    }

    public function search(): void
    {
        $this->view('booking.search');
    }

    public function contact(): void
    {
        if (!Csrf::verify(Request::input('_token'))) {
            Session::set('error', 'Invalid security token.');
            Response::redirect('contact');
        }

        $data = Request::all();
        $errors = Validator::required($data, [
            'full_name' => 'Full name',
            'email' => 'Email',
            'message' => 'Message',
        ]);
        if (!Validator::email((string) ($data['email'] ?? ''))) {
            $errors['email'] = 'A valid email address is required.';
        }

        if ($errors) {
            Session::flashOld($data);
            Session::set('errors', $errors);
            Response::redirect('contact');
        }

        Database::pdo()->prepare("INSERT INTO contact_messages (full_name, email, phone, message, status, created_at, updated_at) VALUES (:full_name, :email, :phone, :message, 'new', NOW(), NOW())")->execute([
            'full_name' => trim((string) $data['full_name']),
            'email' => trim((string) $data['email']),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'message' => trim((string) $data['message']),
        ]);
        Session::clearOld();
        Session::set('success', 'Thank you. Your message has been received.');
        Response::redirect('contact');
    }
}
