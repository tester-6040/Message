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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validate_csrf($_POST['_csrf'] ?? null)) {
    json_response(['message' => 'Invalid CSRF token'], 422);
}

$api = new App\Controllers\ApiController();
$action = $_GET['action'] ?? '';

match ($action) {
    'start-conversation' => $api->startConversation(),
    'messages' => $api->messages(),
    'send-message' => $api->sendMessage(),
    'typing' => $api->typing(),
    'typing-status' => $api->typingStatus(),
    default => json_response(['message' => 'Unknown action'], 404),
};
