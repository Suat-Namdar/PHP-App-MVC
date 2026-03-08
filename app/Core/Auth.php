<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function user(PDO $pdo): ?array
    {
        $userId = self::id();
        if ($userId === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        return $user !== false ? $user : null;
    }

    public static function attempt(PDO $pdo, string $email, string $password): bool
    {
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user === false || !password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public static function hasRole(PDO $pdo, string $roleName): bool
    {
        $userId = self::id();
        if ($userId === null) {
            return false;
        }

        $stmt = $pdo->prepare(
            'SELECT 1
             FROM roles r
             INNER JOIN role_user ru ON ru.role_id = r.id
             WHERE ru.user_id = :user_id AND LOWER(r.name) = LOWER(:name)
             LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'name' => $roleName,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public static function hasPermission(PDO $pdo, string $permissionName): bool
    {
        if (!self::check()) {
            return false;
        }

        // Super-admin bypass: role name "Admin" grants all permissions.
        if (self::hasRole($pdo, 'Admin')) {
            return true;
        }

        $userId = self::id();

        $stmt = $pdo->prepare(
            'SELECT 1
             FROM permissions p
             INNER JOIN permission_role pr ON pr.permission_id = p.id
             INNER JOIN role_user ru ON ru.role_id = pr.role_id
             WHERE ru.user_id = :user_id AND p.name = :permission
             LIMIT 1'
        );
        $stmt->execute([
            'user_id' => $userId,
            'permission' => $permissionName,
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
