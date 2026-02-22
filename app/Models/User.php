<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function create(string $name, string $email, string $password): int
    {
        $stmt = $this->db()->prepare('INSERT INTO users(name, email, password_hash) VALUES(:name, :email, :password_hash)');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return (int) $this->db()->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function allExcept(int $id): array
    {
        $stmt = $this->db()->prepare('SELECT id, name, email, avatar FROM users WHERE id != :id ORDER BY name ASC');
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare('SELECT id, name, email, avatar FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
