<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $basePath = dirname(__DIR__, 2);
        $viewPath = $basePath . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            exit('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewPath;
        $content = (string) ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = $basePath . '/app/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            http_response_code(500);
            exit('Layout not found: ' . $layout);
        }

        include $layoutPath;
    }
}
