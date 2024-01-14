<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class UserPost extends Model
{
    use HasFactory;

    // Указываем имя таблицы, если оно не соответствует стандартному наименованию Laravel
    protected $table = 'users_posts';

    // Указываем поля, которые могут быть заполнены через массовое присваивание
    protected $fillable = [
        'id_user',
        'content',
        'title',
    ];

    // Отключаем защиту от массового присваивания для этих полей
    protected $guarded = [];

    // Родительская связь с моделью User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function addPost(int $userId, string $content, string $title): Model|UserPost
    {
        return $this->create(['id_user' => $userId, 'content' => $content, 'title' => $title]);
    }

    public function getPostsByUser(int $userId): array
    {
        return $this
            ->where('id_user', '=', $userId)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getPostsByUsers(array $userIds): array
    {
        return $this
            ->whereIn('id_user', $userIds)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function deletePost(int $postId): bool
    {
        return $this->find($postId)->delete();
    }

    public function updatePost(int $postId, string $content, string $title): Model|Collection|UserPost|array|null
    {
        $postUpdate = $this->find($postId);
        $postUpdate->content = $content;
        $postUpdate->title = $title;
        $postUpdate->save();
        return $postUpdate;
    }
}
