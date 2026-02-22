<?php

$env = static function (string $key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($value === false || $value === null || $value === '') ? $default : $value;
};

return [
    'app' => [
        'name' => $env('APP_NAME', 'PinkSecret Messenger'),
        'url' => $env('APP_URL', 'http://localhost:8000'),
        'env' => $env('APP_ENV', 'local'),
        'debug' => filter_var($env('APP_DEBUG', '1'), FILTER_VALIDATE_BOOL),
        'encryption_key' => $env('APP_ENCRYPTION_KEY', 'change-this-to-a-32-char-secret-key'),
        'session_secure' => filter_var($env('SESSION_SECURE_COOKIE', '0'), FILTER_VALIDATE_BOOL),
    ],
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => $env('DB_PORT', '3306'),
        'name' => $env('DB_DATABASE', 'pink_secret_chat'),
        'user' => $env('DB_USERNAME', 'root'),
        'pass' => $env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
    'uploads' => [
        'max_size_bytes' => (int) $env('UPLOAD_MAX_SIZE', (string) (15 * 1024 * 1024)),
    ],
];
