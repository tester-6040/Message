<?php

namespace App\Models;

use App\Core\Model;

class TypingStatus extends Model
{
    public function upsert(int $conversationId, int $userId, bool $isTyping): void
    {
        $stmt = $this->db()->prepare('INSERT INTO typing_statuses(conversation_id, user_id, is_typing, updated_at) VALUES(:cid, :uid, :typing, NOW())
            ON DUPLICATE KEY UPDATE is_typing=:typing_u, updated_at=NOW()');
        $stmt->execute([
            ':cid' => $conversationId,
            ':uid' => $userId,
            ':typing' => $isTyping ? 1 : 0,
            ':typing_u' => $isTyping ? 1 : 0,
        ]);
    }

    public function otherTyping(int $conversationId, int $currentUserId): bool
    {
        $stmt = $this->db()->prepare('SELECT is_typing, updated_at FROM typing_statuses WHERE conversation_id=:cid AND user_id != :uid LIMIT 1');
        $stmt->execute([':cid' => $conversationId, ':uid' => $currentUserId]);
        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return (int) $row['is_typing'] === 1 && strtotime($row['updated_at']) > time() - 6;
    }
}
