<?php

declare(strict_types=1);

use App\Core\Session;
use App\Models\User;

if (!function_exists('auth_user')) {
    /**
     * Get the currently authenticated user array (or null).
     *
     * @return array<string,mixed>|null
     */
    function auth_user(): ?array
    {
        static $user = null;
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        if ($user === null) {
            $userModel = new User();
            $user = $userModel->findById((int)$userId);
        }
        return $user;
    }
}

if (!function_exists('can')) {
    /**
     * Check whether the current user has a given permission.
     */
    function can(string $permission): bool
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return false;
        }
        $userModel = new User();
        return $userModel->hasPermission((int)$userId, $permission);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check whether the current user has a given role.
     */
    function has_role(string $role): bool
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return false;
        }
        $userModel = new User();
        return $userModel->hasRole((int)$userId, $role);
    }
}

if (!function_exists('e')) {
    /**
     * HTML-escape a value.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render a hidden CSRF input field.
     */
    function csrf_field(): string
    {
        $token = Session::csrfToken();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

if (!function_exists('asset')) {
    /**
     * Return the URL to a public asset.
     */
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}
