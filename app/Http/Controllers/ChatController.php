<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MessageFormRequest;
use App\Models\Message;
use App\Services\ChatService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function messages($friendId): array
    {
        return $this->chatService->getMessagesByFriend(Auth::user()->id, $friendId);
    }

    public function send(MessageFormRequest $request): Model|Message
    {
        $chatId = $request->input('chatId');
        $message = $request->input('message');
        $userId = Auth::user()->id;
        $message = $this->chatService->addMessage($chatId, $message, $userId);
        $user = $this->chatService->getUserById($userId);
        broadcast(new MessageSent($user, $message));

        return $message;
    }
}
