<?php

declare(strict_types=1);

namespace App\Models;

class Permission extends Model
{
    /**
     * @return array<int, array<string,mixed>>
     */
    public function all(): array
    {
        return $this->db->fetchAll('SELECT * FROM permissions ORDER BY name');
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne('SELECT * FROM permissions WHERE id = ?', [$id]);
    }

    /**
     * @return array<string,mixed>|null
     */
    public function findByName(string $name): ?array
    {
        return $this->db->fetchOne('SELECT * FROM permissions WHERE name = ?', [$name]);
    }

    /**
     * Create a new permission and return its ID.
     */
    public function create(string $name, string $description = ''): int
    {
        $this->db->query(
            'INSERT INTO permissions (name, description) VALUES (?, ?)',
            [$name, $description]
        );
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing permission.
     */
    public function update(int $id, string $name, string $description = ''): void
    {
        $this->db->query(
            'UPDATE permissions SET name = ?, description = ? WHERE id = ?',
            [$name, $description, $id]
        );
    }

    /**
     * Delete a permission.
     */
    public function delete(int $id): void
    {
        $this->db->query('DELETE FROM permissions WHERE id = ?', [$id]);
    }
}
