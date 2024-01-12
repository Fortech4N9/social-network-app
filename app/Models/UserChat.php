<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class UserChat extends Model
{
    use HasFactory;

    protected $table = 'users_chats';

    protected $fillable = [
        'id_user_one',
        'id_user_two',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_one');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_two');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function existsChat($idUserOne, $idUserTwo): bool
    {
        return $this
            ->where('id_user_one', '=', $idUserOne)
            ->where('id_user_two', '=', $idUserTwo)
            ->exists();
    }

    public function getChatId($idUserOne, $idUserTwo): array
    {

        return $this->where('id_user_one', '=', $idUserOne)
            ->where('id_user_two', '=', $idUserTwo)
            ->orderBy('updated_at', 'desc')
            ->select('id')
            ->first()
            ->toArray();
    }

    public function createChat(int $friendSenderId, int $friendRecipientId): Model|UserChat
    {
        return $this->create(['id_user_one' => $friendSenderId, 'id_user_two' => $friendRecipientId]);
    }

    public function getChatsIdsByUser($id): array
    {
        return $this
            ->where('id_user_one', '=', $id)
            ->orWhere('id_user_two', '=', $id)
            ->get()
            ->toArray();
    }
}
