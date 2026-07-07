<?php

declare(strict_types=1);

namespace Transport\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $template) . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean() ?: '';
        $templateName = $template;

        if (in_array($templateName, ['tickets.print', 'reports.pdf'], true)) {
            echo $content;
            return;
        }

        require __DIR__ . '/../Views/layouts/app.php';
    }
}
