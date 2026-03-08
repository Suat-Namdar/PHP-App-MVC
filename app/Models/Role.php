<?php

declare(strict_types=1);

namespace App\Models;

class Role extends Model
{
    /**
     * @return array<int, array<string,mixed>>
     */
    public function all(): array
    {
        return $this->db->fetchAll('SELECT * FROM roles ORDER BY name');
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne('SELECT * FROM roles WHERE id = ?', [$id]);
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findByName(string $name): ?array
    {
        return $this->db->fetchOne('SELECT * FROM roles WHERE name = ?', [$name]);
    }

    /**
     * Create a new role and return its ID.
     */
    public function create(string $name, string $description = ''): int
    {
        $this->db->query(
            'INSERT INTO roles (name, description) VALUES (?, ?)',
            [$name, $description]
        );
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing role.
     */
    public function update(int $id, string $name, string $description = ''): void
    {
        $this->db->query(
            'UPDATE roles SET name = ?, description = ? WHERE id = ?',
            [$name, $description, $id]
        );
    }

    /**
     * Delete a role.
     */
    public function delete(int $id): void
    {
        $this->db->query('DELETE FROM roles WHERE id = ?', [$id]);
    }

    /**
     * Get all permissions assigned to a role.
     *
     * @return array<int, array<string,mixed>>
     */
    public function getPermissions(int $roleId): array
    {
        return $this->db->fetchAll(
            'SELECT p.* FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?
             ORDER BY p.name',
            [$roleId]
        );
    }

    /**
     * Sync permissions – replace a role's permissions with the provided list.
     *
     * @param array<int> $permissionIds
     */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->query('DELETE FROM role_permissions WHERE role_id = ?', [$roleId]);
        foreach ($permissionIds as $permId) {
            $this->db->query(
                'INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)',
                [$roleId, (int)$permId]
            );
        }
    }
}
