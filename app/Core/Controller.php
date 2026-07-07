<?php

declare(strict_types=1);

namespace Transport\Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function json(array $payload, int $status = 200): void
    {
        Response::json($payload, $status);
    }

    protected function redirect(string $path): void
    {
        Response::redirect($path);
    }
}

