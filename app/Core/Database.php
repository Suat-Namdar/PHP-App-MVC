<?php

declare(strict_types=1);

namespace App\Core;

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $driver = $_ENV['DB_DRIVER'] ?? 'sqlite';

        if ($driver === 'sqlite') {
            $rawPath = $_ENV['DB_PATH'] ?? 'database/app.db';
            if ($rawPath === ':memory:') {
                $dsn = 'sqlite::memory:';
            } else {
                $path = BASE_PATH . '/' . $rawPath;
                $dir  = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $dsn = "sqlite:$path";
            }
            $this->pdo = new \PDO($dsn);
            $this->pdo->exec('PRAGMA foreign_keys = ON;');
        } else {
            $host     = $_ENV['DB_HOST']     ?? '127.0.0.1';
            $port     = $_ENV['DB_PORT']     ?? '3306';
            $dbname   = $_ENV['DB_DATABASE'] ?? 'php_mvc';
            $charset  = 'utf8mb4';
            $dsn      = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
            $this->pdo = new \PDO(
                $dsn,
                $_ENV['DB_USERNAME'] ?? 'root',
                $_ENV['DB_PASSWORD'] ?? '',
            );
        }

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a query and return the statement.
     *
     * @param array<int|string, mixed> $params
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     *
     * @param array<int|string, mixed> $params
     * @return array<string,mixed>|null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Fetch all rows.
     *
     * @param array<int|string, mixed> $params
     * @return array<int, array<string,mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    /** Reset singleton – for testing only. */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
