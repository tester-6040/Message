<?php

namespace App\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\TypingStatus;

class ApiController
{
    private function ensureAuth(): int
    {
        $uid = current_user_id();
        if (!$uid) {
            json_response(['message' => 'Unauthenticated'], 401);
        }
        return $uid;
    }

    public function startConversation(): void
    {
        $uid = $this->ensureAuth();
        $partnerId = (int) ($_POST['partner_id'] ?? 0);
        if ($partnerId < 1 || $partnerId === $uid) {
            json_response(['message' => 'Invalid partner'], 422);
        }
        $id = (new Conversation())->findOrCreatePrivate($uid, $partnerId);
        json_response(['conversation_id' => $id]);
    }

    public function messages(): void
    {
        $uid = $this->ensureAuth();
        $conversationId = (int) ($_GET['conversation_id'] ?? 0);
        if (!$conversationId) {
            json_response(['message' => 'Missing conversation_id'], 422);
        }

        $rows = (new Message())->list($conversationId);
        $data = array_map(fn ($m) => [
            'id' => (int) $m['id'],
            'sender_id' => (int) $m['sender_id'],
            'text' => $m['content_encrypted'] ? decrypt_text($m['content_encrypted']) : null,
            'media_path' => $m['media_path'],
            'media_type' => $m['media_type'],
            'is_me' => (int) $m['sender_id'] === $uid,
            'created_at' => $m['created_at'],
        ], $rows);
        json_response(['messages' => $data]);
    }

    public function sendMessage(): void
    {
        $uid = $this->ensureAuth();
        $conversationId = (int) ($_POST['conversation_id'] ?? 0);
        $text = trim($_POST['text'] ?? '');

        $mediaPath = null;
        $mediaType = null;
        if (!empty($_FILES['media']['tmp_name'])) {
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'video/mp4' => 'mp4',
            ];
            $mime = mime_content_type($_FILES['media']['tmp_name']);
            if (!isset($allowed[$mime])) {
                json_response(['message' => 'Unsupported media type'], 422);
            }
            $file = uniqid('media_', true) . '.' . $allowed[$mime];
            $target = __DIR__ . '/../../storage/uploads/' . $file;
            move_uploaded_file($_FILES['media']['tmp_name'], $target);
            $mediaPath = 'storage/uploads/' . $file;
            $mediaType = str_starts_with($mime, 'image/') ? 'image' : 'video';
        }

        if (!$conversationId || (!$text && !$mediaPath)) {
            json_response(['message' => 'conversation_id + text/media required'], 422);
        }

        (new Message())->create($conversationId, $uid, $text ?: null, $mediaPath, $mediaType);
        json_response(['message' => 'sent']);
    }

    public function typing(): void
    {
        $uid = $this->ensureAuth();
        $conversationId = (int) ($_POST['conversation_id'] ?? 0);
        $isTyping = (bool) ($_POST['is_typing'] ?? false);
        if (!$conversationId) {
            json_response(['message' => 'conversation_id required'], 422);
        }

        (new TypingStatus())->upsert($conversationId, $uid, $isTyping);
        json_response(['ok' => true]);
    }

    public function typingStatus(): void
    {
        $uid = $this->ensureAuth();
        $conversationId = (int) ($_GET['conversation_id'] ?? 0);
        if (!$conversationId) {
            json_response(['message' => 'conversation_id required'], 422);
        }
        $typing = (new TypingStatus())->otherTyping($conversationId, $uid);
        json_response(['typing' => $typing]);
    }
}
