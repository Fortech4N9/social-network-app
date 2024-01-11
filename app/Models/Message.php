<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class Message extends Model
{
    use HasFactory;

    protected $table = 'users_messages';

    protected $fillable = [
        'chat_id',
        'message',
    ];

    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_one');
    }

    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_two');
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(UserChat::class, 'chat_id');
    }

    public function getAllMessagesByChatId($chatId): array
    {
        return $this->where('chat_id', '=', $chatId)->get()->toArray();
    }
}
