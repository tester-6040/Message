<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    public function create(int $conversationId, int $senderId, ?string $text, ?string $mediaPath, ?string $mediaType): int
    {
        $stmt = $this->db()->prepare('INSERT INTO messages(conversation_id, sender_id, content_encrypted, media_path, media_type) VALUES(:conversation_id, :sender_id, :content, :media_path, :media_type)');
        $stmt->execute([
            ':conversation_id' => $conversationId,
            ':sender_id' => $senderId,
            ':content' => $text ? encrypt_text($text) : null,
            ':media_path' => $mediaPath,
            ':media_type' => $mediaType,
        ]);

        return (int) $this->db()->lastInsertId();
    }

    public function list(int $conversationId, int $limit = 80): array
    {
        $stmt = $this->db()->prepare('SELECT id, sender_id, content_encrypted, media_path, media_type, created_at FROM messages WHERE conversation_id=:cid ORDER BY id DESC LIMIT :lim');
        $stmt->bindValue(':cid', $conversationId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return array_reverse($stmt->fetchAll());
    }
}
