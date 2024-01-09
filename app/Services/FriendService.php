<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FriendService
{

    private Friend $friendModel;
    private User $userModel;

    private FriendRequest $friendRequestModel;

    const EXPECTATION_STATUS = 'expectation';
    const DEVIATION_STATUS = 'deviation';
    const CONFIRMATION_STATUS = 'confirmation';

    public function __construct(Friend $friendModel, User $userModel, FriendRequest $friendRequestModel)
    {
        $this->friendModel = $friendModel;
        $this->userModel = $userModel;
        $this->friendRequestModel = $friendRequestModel;
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

    private function getAllUsersListWithoutAuthUser(string $userEmail): array
    {
        return $this->userModel
            ->where('email', '!=', $userEmail)
            ->select(['name', 'id'])
            ->get()
            ->toArray();
    }

    public function getAllUsers(): array
    {
        $authUser = auth()->user();
        $allUsers = $this->getAllUsersListWithoutAuthUser($authUser->email);

        $allUsersWithoutFriends = [];
        foreach ($allUsers as $index => $user) {
            $allUsers[$index]['requestSent'] = false;
            if ($this->friendRequestModel->friendRequestExists($authUser->id, $user['id'], self::EXPECTATION_STATUS)) {
                $allUsers[$index]['requestSent'] = true;
            }
            if (!$this->friendModel->friendExists($authUser->id, $user['id'], self::CONFIRMATION_STATUS) &&
                !$this->friendModel->friendExists($user['id'], $authUser->id, self::CONFIRMATION_STATUS)) {
                $allUsersWithoutFriends[] = $allUsers[$index];
            }

        }
        return $allUsersWithoutFriends;
    }

    public function getUsersRequests(): array
    {
        return $this->userModel
            ->getUsersByIds($this->friendRequestModel->getAllUsersIdsFriendRequest(
                auth()->user()->id,
                self::EXPECTATION_STATUS)
            );
    }

    public function getUsersFriends(): array
    {
        $friendsIds = $this->friendModel->getAllUserFriendsIds(auth()->user()->id, self::CONFIRMATION_STATUS);
        return $this->userModel->getUsersByIds($friendsIds);
    }

    public function addFriend(int $friendSenderId, int $friendRecipientId): Friend|Model
    {
        $this->friendRequestModel->updateFriend($friendRecipientId, $friendSenderId, self::CONFIRMATION_STATUS);
        return $this->friendModel
            ->createFriend($friendSenderId, $friendRecipientId, self::CONFIRMATION_STATUS);
    }

    public function cancelFriend(int $friendSenderId, int $friendRecipientId): bool
    {
        if ($this->friendModel->friendExists($friendSenderId, $friendRecipientId, self::CONFIRMATION_STATUS)) {
            return $this->friendModel->updateFriend($friendSenderId, $friendRecipientId, self::DEVIATION_STATUS);
        }
        return $this->friendModel->updateFriend($friendRecipientId, $friendSenderId, self::DEVIATION_STATUS);
    }
}
