<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view file.
     *
     * @param array<string, mixed> $data
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: $view");
        }
        require $viewPath;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Get a POST value, sanitised.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Validate CSRF token and abort with 403 on failure.
     */
    protected function validateCsrf(): void
    {
        $token = $this->input('_csrf_token', '');
        if (!Session::validateCsrf((string)$token)) {
            http_response_code(403);
            require BASE_PATH . '/app/Views/errors/403.php';
            exit();
        }
    }
}
