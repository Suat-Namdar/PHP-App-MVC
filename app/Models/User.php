<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    /**
     * Find a user by ID.
     *
     * @return array<string,mixed>|null
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /**
     * Find a user by username.
     *
     * @return array<string,mixed>|null
     */
    public function findByUsername(string $username): ?array
    {
        return $this->db->fetchOne('SELECT * FROM users WHERE username = ?', [$username]);
    }

    /**
     * Find a user by email.
     *
     * @return array<string,mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    /**
     * Return all users.
     *
     * @return array<int, array<string,mixed>>
     */
    public function all(): array
    {
        return $this->db->fetchAll('SELECT id, username, email, created_at FROM users ORDER BY id');
    }

    /**
     * Create a new user.
     */
    public function create(string $username, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->query(
            'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
            [$username, $email, $hash]
        );
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update user details.
     */
    public function update(int $id, string $username, string $email): void
    {
        $this->db->query(
            'UPDATE users SET username = ?, email = ? WHERE id = ?',
            [$username, $email, $id]
        );
    }

    /**
     * Update user password.
     */
    public function updatePassword(int $id, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $id]);
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): void
    {
        $this->db->query('DELETE FROM users WHERE id = ?', [$id]);
    }

    /**
     * Verify a user's password.
     */
    public function verifyPassword(string $plainPassword, string $hash): bool
    {
        return password_verify($plainPassword, $hash);
    }

    /**
     * Get all roles assigned to a user.
     *
     * @return array<int, array<string,mixed>>
     */
    public function getRoles(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT r.* FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = ?
             ORDER BY r.name',
            [$userId]
        );
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(int $userId, int $roleId): void
    {
        $existing = $this->db->fetchOne(
            'SELECT 1 FROM user_roles WHERE user_id = ? AND role_id = ?',
            [$userId, $roleId]
        );
        if (!$existing) {
            $this->db->query(
                'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)',
                [$userId, $roleId]
            );
        }
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(int $userId, int $roleId): void
    {
        $this->db->query(
            'DELETE FROM user_roles WHERE user_id = ? AND role_id = ?',
            [$userId, $roleId]
        );
    }

    /**
     * Sync roles – replace the user's roles with the provided list.
     *
     * @param array<int> $roleIds
     */
    public function syncRoles(int $userId, array $roleIds): void
    {
        $this->db->query('DELETE FROM user_roles WHERE user_id = ?', [$userId]);
        foreach ($roleIds as $roleId) {
            $this->db->query(
                'INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)',
                [$userId, (int)$roleId]
            );
        }
    }

    /**
     * Check whether a user has a given permission (through any of their roles).
     */
    public function hasPermission(int $userId, string $permission): bool
    {
        $row = $this->db->fetchOne(
            'SELECT 1 FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             INNER JOIN user_roles ur ON ur.role_id = rp.role_id
             WHERE ur.user_id = ? AND p.name = ?
             LIMIT 1',
            [$userId, $permission]
        );
        return $row !== null;
    }

    /**
     * Check whether a user has a given role.
     */
    public function hasRole(int $userId, string $roleName): bool
    {
        $row = $this->db->fetchOne(
            'SELECT 1 FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = ? AND r.name = ?
             LIMIT 1',
            [$userId, $roleName]
        );
        return $row !== null;
    }
}
