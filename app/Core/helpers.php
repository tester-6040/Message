<?php

function config(?string $path = null, $default = null)
{
    static $config;
    if (!$config) {
        $config = require __DIR__ . '/../../config/config.php';
    }

    if ($path === null) {
        return $config;
    }

    $segments = explode('.', $path);
    $value = $config;
    foreach ($segments as $segment) {
        if (!isset($value[$segment])) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $base = rtrim(config('app.url'), '/');
    return $base . '/' . ltrim($path, '/');
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../Views/' . $template . '.php';
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function validate_csrf(?string $token): bool
{
    return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], (string) $token);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function require_auth(): void
{
    if (!current_user_id()) {
        redirect('login.php');
    }
}

function encrypt_text(string $plain): string
{
    $key = hash('sha256', config('app.encryption_key'), true);
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipher);
}

function decrypt_text(string $encoded): string
{
    $raw = base64_decode($encoded, true);
    if ($raw === false || strlen($raw) < 17) {
        return '';
    }
    $iv = substr($raw, 0, 16);
    $cipher = substr($raw, 16);
    $key = hash('sha256', config('app.encryption_key'), true);
    return openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv) ?: '';
}
