<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Db;
use App\Core\View;
use PDO;

final class CustomerController
{
    public function index(array $app, array $vars = []): void
    {
        $pdo = Db::getInstance($app['db']);

        if (!Auth::hasPermission($pdo, 'customers.view')) {
            http_response_code(403);
            exit('Forbidden');
        }

        View::render('admin.customers.index', [
            'title' => 'Customers',
            'app' => $app,
        ], 'admin');
    }

    public function dataTable(array $app, array $vars = []): void
    {
        $pdo = Db::getInstance($app['db']);

        if (!Auth::hasPermission($pdo, 'customers.view')) {
            $this->json([
                'error' => 'Forbidden',
            ], 403);
            return;
        }

        $draw = (int) ($_POST['draw'] ?? 1);
        $start = max(0, (int) ($_POST['start'] ?? 0));
        $length = (int) ($_POST['length'] ?? 10);
        $length = $length > 0 ? min($length, 100) : 10;

        $searchValue = trim((string) ($_POST['search']['value'] ?? ''));
        $orderColumnIndex = (int) ($_POST['order'][0]['column'] ?? 0);
        $orderDir = strtolower((string) ($_POST['order'][0]['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

        $allowedColumns = [
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            4 => 'created_at',
        ];
        $orderColumn = $allowedColumns[$orderColumnIndex] ?? 'id';

        $totalRecords = (int) $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();

        $whereSql = '';
        $params = [];
        if ($searchValue !== '') {
            $whereSql = ' WHERE name LIKE :search OR email LIKE :search OR phone LIKE :search ';
            $params['search'] = '%' . $searchValue . '%';
        }

        $filteredSql = 'SELECT COUNT(*) FROM customers' . $whereSql;
        $filteredStmt = $pdo->prepare($filteredSql);
        foreach ($params as $key => $value) {
            $filteredStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $filteredStmt->execute();
        $filteredRecords = (int) $filteredStmt->fetchColumn();

        $dataSql = sprintf(
            'SELECT id, name, email, phone, created_at FROM customers %s ORDER BY %s %s LIMIT :length OFFSET :start',
            $whereSql,
            $orderColumn,
            $orderDir
        );
        $dataStmt = $pdo->prepare($dataSql);
        foreach ($params as $key => $value) {
            $dataStmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $dataStmt->bindValue(':length', $length, PDO::PARAM_INT);
        $dataStmt->bindValue(':start', $start, PDO::PARAM_INT);
        $dataStmt->execute();
        $rows = $dataStmt->fetchAll();

        $this->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows,
        ]);
    }

    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
