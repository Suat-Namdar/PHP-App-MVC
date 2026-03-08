<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Database;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Database $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset singleton so each test gets a fresh in-memory DB
        Database::reset();

        $this->db = Database::getInstance();

        // Apply schema
        $schema = file_get_contents(BASE_PATH . '/database/schema.sql');
        $this->db->getPdo()->exec($schema);
    }

    protected function tearDown(): void
    {
        Database::reset();
        parent::tearDown();
    }
}
