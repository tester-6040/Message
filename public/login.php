<?php
require __DIR__ . '/../app/bootstrap.php';

$controller = new App\Controllers\AuthController();
$action = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['action'] ?? '') : '';

if ($action === 'login') {
    $controller->login();
}
if ($action === 'register') {
    $controller->register();
}
if ($action === 'logout') {
    $controller->logout();
}

$controller->loginPage();
