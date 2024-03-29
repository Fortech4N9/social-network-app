<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\UserChat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ChatService
{
    private UserChat $chatModel;
    private Message $messageModel;

    public function __construct(UserChat $chatModel, Message $messageModel)
    {
        $this->chatModel = $chatModel;
        $this->messageModel = $messageModel;
    }

    public function getMessagesByFriend($idAuthUser, $idFriendUser): array
    {
        if ($this->chatModel->existsChat($idAuthUser, $idFriendUser)) {
            $chatId = $this->chatModel->getChatId($idAuthUser, $idFriendUser);
        } else {
            $chatId = $this->chatModel->getChatId($idFriendUser, $idAuthUser);
        }
        return $this->messageModel->getAllMessagesByChatId($chatId);
    }

    public function addChat(int $friendSenderId, int $friendRecipientId): bool
    {
        if (!$this->chatModel->existsChat($friendSenderId, $friendRecipientId)) {
            $this->chatModel->createChat($friendSenderId, $friendRecipientId);
            return true;
        }
        return false;
    }

    public function getChatsByAuthUser($id): array
    {
        return $this->chatModel->getChatsIdsByUser($id);
    }

    public function addMessage(int $chatId, string $message, int $senderMessage): Model|Message
    {
        return $this->messageModel->createMessage($chatId, $message, $senderMessage);
    }

    public function getUserById(int $id): Model|Collection|array|User|null
    {
        return (new User)->find($id);
    }
}
