<?php
session_start();

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $path = __DIR__ . '/../app/' . str_replace('App\\', '', $class) . '.php';
        $path = str_replace('\\', '/', $path);
        if (file_exists($path)) {
            require $path;
        }
    }
});

require __DIR__ . '/../app/Core/helpers.php';

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

$controller->index();
