<?php

namespace App\Controllers;

use App\Models\Conversation;
use App\Models\User;

class ChatController
{
    public function index(): void
    {
        require_auth();

        $userId = current_user_id();
        $users = (new User())->allExcept($userId);
        $conversations = (new Conversation())->forUser($userId);

        foreach ($conversations as &$conv) {
            $conv['preview'] = $conv['content_encrypted'] ? decrypt_text($conv['content_encrypted']) : ($conv['media_path'] ? '📎 Media' : '');
        }

        view('chat/index', [
            'title' => 'Private Chat',
            'users' => $users,
            'conversations' => $conversations,
            'me' => (new User())->find($userId),
        ]);
    }
}
