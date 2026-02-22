<?php

namespace App\Models;

use App\Core\Model;

class Conversation extends Model
{
    public function findOrCreatePrivate(int $userA, int $userB): int
    {
        [$a, $b] = $userA < $userB ? [$userA, $userB] : [$userB, $userA];
        $stmt = $this->db()->prepare('SELECT id FROM conversations WHERE user_a_id=:a AND user_b_id=:b LIMIT 1');
        $stmt->execute([':a' => $a, ':b' => $b]);
        $existing = $stmt->fetch();
        if ($existing) {
            return (int) $existing['id'];
        }

        $insert = $this->db()->prepare('INSERT INTO conversations(user_a_id, user_b_id) VALUES(:a, :b)');
        $insert->execute([':a' => $a, ':b' => $b]);
        return (int) $this->db()->lastInsertId();
    }

    public function forUser(int $userId): array
    {
        $sql = "SELECT c.id,
                       CASE WHEN c.user_a_id = :uid THEN u2.name ELSE u1.name END AS partner_name,
                       CASE WHEN c.user_a_id = :uid THEN u2.id ELSE u1.id END AS partner_id,
                       m.content_encrypted,
                       m.media_path,
                       m.created_at AS last_at
                FROM conversations c
                JOIN users u1 ON u1.id = c.user_a_id
                JOIN users u2 ON u2.id = c.user_b_id
                LEFT JOIN messages m ON m.id = (
                    SELECT id FROM messages
                    WHERE conversation_id = c.id
                    ORDER BY id DESC LIMIT 1
                )
                WHERE c.user_a_id = :uid OR c.user_b_id = :uid
                ORDER BY last_at DESC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
}
