<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Auth;
use Transport\Core\Controller;
use Transport\Core\Request;
use Transport\Core\Response;
use Transport\Core\Session;
use Transport\Models\ActivityLog;

abstract class BaseController extends Controller
{
    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            Session::set('auth_intended', Request::uri());
            Response::redirect('login');
        }
    }

    protected function requireRoles(array|string $roles): void
    {
        Auth::requireRole($roles);
    }

    protected function user(): ?array
    {
        return Auth::user();
    }

    protected function flashOldInput(): void
    {
        Session::flashOld(Request::all());
    }

    protected function logActivity(string $module, string $action, string $description): void
    {
        try {
            $currentUser = $this->user();
            (new ActivityLog())->record([
                'user_id' => $currentUser['id'] ?? null,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]);
        } catch (\Throwable) {
            // Logging must never break the user flow.
        }
    }
}
