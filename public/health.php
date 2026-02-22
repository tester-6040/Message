<?php
require __DIR__ . '/../app/bootstrap.php';

try {
    App\Core\Database::connection()->query('SELECT 1');
    json_response(['status' => 'ok', 'database' => 'connected']);
} catch (Throwable $e) {
    json_response(['status' => 'degraded', 'database' => 'down', 'message' => $e->getMessage()], 500);
}
