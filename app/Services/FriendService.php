<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class FriendService
{

    private Friend $friendModel;
    private User $userModel;
    private ChatService $chatService;
    private FriendRequest $friendRequestModel;

    const EXPECTATION_STATUS = 'expectation';
    const DEVIATION_STATUS = 'deviation';
    const CONFIRMATION_STATUS = 'confirmation';


    public function __construct(Friend $friendModel, User $userModel, FriendRequest $friendRequestModel,ChatService $chatService)
    {
        $this->friendModel = $friendModel;
        $this->userModel = $userModel;
        $this->friendRequestModel = $friendRequestModel;
        $this->chatService = $chatService;
    }

    public function addFriendRequest(int $friendSenderId, int $friendRecipientId): Model|FriendRequest
    {
        return $this->friendRequestModel
            ->createFriend($friendSenderId, $friendRecipientId, self::EXPECTATION_STATUS);
    }

    public function cancelRequest(int $friendSenderId, int $friendRecipientId): bool
    {
        return $this->friendRequestModel
            ->updateFriend($friendRecipientId, $friendSenderId, self::DEVIATION_STATUS);
    }

    public function getAllUsersListWithoutAuthUser(string $userEmail): array
    {
        return $this->userModel
            ->where('email', '!=', $userEmail)
            ->select(['name', 'id'])
            ->get()
            ->toArray();
    }

    public function getAllUsers(): array
    {
        $authUser = $this->getAuthUser()[0];
        $allUsers = $this->getAllUsersListWithoutAuthUser($authUser['email']);

        $allUsersWithoutFriends = [];
        foreach ($allUsers as $index => $user) {
            $allUsers[$index]['requestSent'] = false;
            if ($this->friendRequestModel->friendRequestExists($authUser['id'], $user['id'], self::EXPECTATION_STATUS)) {
                $allUsers[$index]['requestSent'] = true;
            }
            if (!$this->friendModel->friendExists($authUser['id'], $user['id'], self::CONFIRMATION_STATUS) &&
                !$this->friendModel->friendExists($user['id'], $authUser['id'], self::CONFIRMATION_STATUS)) {
                $allUsersWithoutFriends[] = $allUsers[$index];
            }
        }
        return $allUsersWithoutFriends;
    }

    public function getAuthUser(): array
    {
        return $this->userModel
            ->where('id', '=', auth()->user()->id)
            ->select(['name', 'id','email'])
            ->first()
            ->get()
            ->toArray();
    }

    public function getUsersRequests(): array
    {
        return $this->userModel
            ->getUsersByIds(
                $this->friendRequestModel->getAllUsersIdsFriendRequest(
                    auth()->user()->id,
                    self::EXPECTATION_STATUS
                )
            );
    }

    public function getUsersFriends(): array
    {
        $friendsIds = $this->friendModel->getAllUserFriendsIds(auth()->user()->id, self::CONFIRMATION_STATUS);
        $usersWithModel = $this->userModel->getUsersByIds($friendsIds);
        $usersChats = $this->getFriendsChatsIds();
        foreach ($usersWithModel as $index => $user) {
            foreach ($usersChats as $chats){
                if ($chats['id_user_one']==$user['id']||$chats['id_user_two']==$user['id']){
                    $usersWithModel[$index]['chatId'] = $chats['id'];
                }
            }
        }
        return $usersWithModel;
    }

    public function addFriend(int $friendSenderId, int $friendRecipientId): bool
    {
        $this->chatService->addChat($friendSenderId, $friendRecipientId);
        return
            $this->friendRequestModel->updateFriend($friendRecipientId, $friendSenderId, self::CONFIRMATION_STATUS)
            &&
            $this->friendModel->createFriend($friendSenderId, $friendRecipientId, self::CONFIRMATION_STATUS);
    }

    public function cancelFriend(int $friendSenderId, int $friendRecipientId): bool
    {
        if ($this->friendModel->friendExists($friendSenderId, $friendRecipientId, self::CONFIRMATION_STATUS)) {
            return $this->friendModel->updateFriend($friendSenderId, $friendRecipientId, self::DEVIATION_STATUS);
        }
        return $this->friendModel->updateFriend($friendRecipientId, $friendSenderId, self::DEVIATION_STATUS);
    }

    public function getFriendsChatsIds(): array
    {

        return $this->chatService->getChatsByAuthUser(auth()->user()->id);
    }
}
