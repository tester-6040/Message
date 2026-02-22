<?php

namespace App\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\TypingStatus;
use App\Models\User;

class ApiController
{
    private function ensureAuth(): int
    {
        $uid = current_user_id();
        if (!$uid) {
            json_response(['message' => 'Unauthenticated'], 401);
        }
        (new User())->heartbeat($uid);
        return $uid;
    }

    private function ensureParticipant(int $conversationId, int $uid): void
    {
        if (!(new Conversation())->isParticipant($conversationId, $uid)) {
            json_response(['message' => 'Forbidden'], 403);
        }
    }

    public function startConversation(): void
    {
        $uid = $this->ensureAuth();
        $partnerId = (int) ($_POST['partner_id'] ?? 0);
        if ($partnerId < 1 || $partnerId === $uid) {
            json_response(['message' => 'Invalid partner'], 422);
        }

        if (!(new User())->find($partnerId)) {
            json_response(['message' => 'Partner not found'], 404);
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
        $this->ensureParticipant($conversationId, $uid);

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
        $text = trim((string) ($_POST['text'] ?? ''));

        $mediaPath = null;
        $mediaType = null;

        if (!empty($_FILES['media']['tmp_name'])) {
            if ((int) $_FILES['media']['size'] > (int) config('uploads.max_size_bytes')) {
                json_response(['message' => 'File too large'], 422);
            }

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

            $file = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
            $uploadDir = __DIR__ . '/../../public/uploads';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                json_response(['message' => 'Upload folder unavailable'], 500);
            }

            $target = $uploadDir . '/' . $file;
            if (!move_uploaded_file($_FILES['media']['tmp_name'], $target)) {
                json_response(['message' => 'Unable to save media'], 500);
            }

            $mediaPath = 'uploads/' . $file;
            $mediaType = str_starts_with($mime, 'image/') ? 'image' : 'video';
        }

        if (!$conversationId || ($text === '' && !$mediaPath)) {
            json_response(['message' => 'conversation_id + text/media required'], 422);
        }

        $this->ensureParticipant($conversationId, $uid);
        (new Message())->create($conversationId, $uid, $text !== '' ? $text : null, $mediaPath, $mediaType);

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

        $this->ensureParticipant($conversationId, $uid);
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

        $this->ensureParticipant($conversationId, $uid);
        $typing = (new TypingStatus())->otherTyping($conversationId, $uid);
        json_response(['typing' => $typing]);
    }

    public function onlineStatus(): void
    {
        $uid = $this->ensureAuth();
        $conversationId = (int) ($_GET['conversation_id'] ?? 0);
        if (!$conversationId) {
            json_response(['message' => 'conversation_id required'], 422);
        }

        $this->ensureParticipant($conversationId, $uid);
        $conversations = (new Conversation())->forUser($uid);

        foreach ($conversations as $conversation) {
            if ((int) $conversation['id'] === $conversationId) {
                json_response(['online' => (bool) $conversation['partner_online']]);
            }
        }

        json_response(['online' => false]);
    }
}
