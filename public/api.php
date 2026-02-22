<?php
require __DIR__ . '/../app/bootstrap.php';

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
    'online-status' => $api->onlineStatus(),
    default => json_response(['message' => 'Unknown action'], 404),
};
