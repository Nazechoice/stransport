<?php

declare(strict_types=1);

namespace Transport\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): self
    {
        $path = '/' . ltrim($path, '/');
        $this->routes[] = compact('method', 'path', 'handler');
        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);
            $matches = array_map(static function ($value) {
                $stringValue = (string) $value;
                return ctype_digit($stringValue) ? (int) $stringValue : $stringValue;
            }, $matches);
            $handler = $route['handler'];

            if (is_array($handler) && is_string($handler[0])) {
                $controller = new $handler[0]();
                $action = $handler[1];
                $controller->{$action}(...$matches);
                return;
            }

            if (is_callable($handler)) {
                $handler(...$matches);
                return;
            }
        }

        http_response_code(404);
        View::render('errors.404');
    }
}
