<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
class FriendRequest extends Model {
    protected $table = 'friends_requests';
    protected $fillable = ['id_user_one', 'id_user_two', 'status'];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'id_user_one');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'id_user_two');
    }

    public function createFriend(string $id_user_one, string $id_user_two, string $status)
    {
        return $this->create([
            'id_user_one' => $id_user_one,
            'id_user_two' => $id_user_two,
            'status' => $status
        ]);
    }

    public function removeFriend(string $id_user_one,string $id_user_two)
    {
        return $this->where('id_user_one', $id_user_one)
            ->where('id_user_two', $id_user_two)
            ->delete();
    }

    public function updateFriend(string $id_user_one, string $id_user_two, string $status): bool
    {
        $friend = $this->where('id_user_one', $id_user_one)
            ->where('id_user_two', $id_user_two)
            ->orderBy('updated_at', 'desc')
            ->first();
        $friend->id_user_one = $id_user_one;
        $friend->id_user_two = $id_user_two;
        $friend->status = $status;
        return $friend->save();
    }

    public function friendRequestExists(string $id_user_one,string $id_user_two,string $status): bool
    {
        if ($this->where('id_user_one', '=', $id_user_one)
            ->where('id_user_two', '=', $id_user_two)
            ->where('status', '=', $status)
            ->exists()) {
            return true;
        }
        return false;
    }

    public function getAllUsersIdsFriendRequest(string $idUser,string $status): array
    {
        return $this
            ->where('id_user_two', '=', $idUser)
            ->where('status', '=', $status)
            ->select('id_user_one')
            ->get()
            ->toArray();
    }
}
