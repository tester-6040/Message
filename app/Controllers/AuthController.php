<?php

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function landing(): void
    {
        view('auth/landing', ['title' => 'For You, My Heart']);
    }

    public function loginPage(): void
    {
        if (current_user_id()) {
            redirect('chat.php');
        }

        view('auth/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        if (!validate_csrf($_POST['_csrf'] ?? null)) {
            json_response(['message' => 'Invalid CSRF token'], 422);
        }

        $user = (new User())->findByEmail(trim($_POST['email'] ?? ''));
        if (!$user || !password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            json_response(['message' => 'Invalid credentials'], 401);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        (new User())->heartbeat((int) $user['id']);
        json_response(['message' => 'Logged in']);
    }

    public function register(): void
    {
        if (!validate_csrf($_POST['_csrf'] ?? null)) {
            json_response(['message' => 'Invalid CSRF token'], 422);
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$name || !$email || strlen($password) < 6) {
            json_response(['message' => 'Enter valid name/email/password (6+)'], 422);
        }

        $users = new User();
        if ($users->findByEmail($email)) {
            json_response(['message' => 'Email already in use'], 409);
        }

        $_SESSION['user_id'] = $users->create($name, $email, $password);
        json_response(['message' => 'Registered']);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('index.php');
    }
}
