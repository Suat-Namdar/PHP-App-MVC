#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Database seeder – creates tables and populates initial data.
 *
 * Usage:
 *   php database/seed.php
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Load .env
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();

// Run schema
$schema = file_get_contents(BASE_PATH . '/database/schema.sql');
$pdo->exec($schema);
echo "Schema applied.\n";

// ---------- Permissions ----------
$permissions = [
    ['user.view',           'Kullanıcıları görüntüle'],
    ['user.create',         'Kullanıcı oluştur'],
    ['user.edit',           'Kullanıcı düzenle'],
    ['user.delete',         'Kullanıcı sil'],
    ['role.manage',         'Rolleri yönet'],
    ['permission.manage',   'İzinleri yönet'],
];

$permIds = [];
foreach ($permissions as [$name, $desc]) {
    $existing = $db->fetchOne('SELECT id FROM permissions WHERE name = ?', [$name]);
    if ($existing) {
        $permIds[$name] = (int)$existing['id'];
    } else {
        $db->query('INSERT INTO permissions (name, description) VALUES (?, ?)', [$name, $desc]);
        $permIds[$name] = (int)$db->lastInsertId();
        echo "Permission created: $name\n";
    }
}

// ---------- Roles ----------
$roles = [
    ['admin',   'Tam yetki',             array_values($permIds)],
    ['editor',  'İçerik yöneticisi',     [$permIds['user.view'], $permIds['user.edit']]],
    ['viewer',  'Salt okunur erişim',    [$permIds['user.view']]],
];

$roleIds = [];
foreach ($roles as [$name, $desc, $rolePermissions]) {
    $existing = $db->fetchOne('SELECT id FROM roles WHERE name = ?', [$name]);
    if ($existing) {
        $roleId = (int)$existing['id'];
    } else {
        $db->query('INSERT INTO roles (name, description) VALUES (?, ?)', [$name, $desc]);
        $roleId = (int)$db->lastInsertId();
        echo "Role created: $name\n";
    }
    $roleIds[$name] = $roleId;

    // Sync permissions
    $db->query('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);
    foreach ($rolePermissions as $permId) {
        $db->query(
            'INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)',
            [$roleId, $permId]
        );
    }
}

// ---------- Default Users ----------
$users = [
    ['admin',   'admin@example.com',   'admin123',   ['admin']],
    ['editor',  'editor@example.com',  'editor123',  ['editor']],
    ['viewer',  'viewer@example.com',  'viewer123',  ['viewer']],
];

foreach ($users as [$username, $email, $password, $userRoles]) {
    $existing = $db->fetchOne('SELECT id FROM users WHERE username = ?', [$username]);
    if ($existing) {
        $userId = (int)$existing['id'];
        echo "User already exists: $username\n";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db->query(
            'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
            [$username, $email, $hash]
        );
        $userId = (int)$db->lastInsertId();
        echo "User created: $username (password: $password)\n";
    }

    // Sync roles
    $db->query('DELETE FROM user_roles WHERE user_id = ?', [$userId]);
    foreach ($userRoles as $roleName) {
        if (isset($roleIds[$roleName])) {
            $db->query(
                'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)',
                [$userId, $roleIds[$roleName]]
            );
        }
    }
}

echo "\nSeed completed successfully!\n";
echo "Login credentials:\n";
echo "  admin  / admin123\n";
echo "  editor / editor123\n";
echo "  viewer / viewer123\n";
