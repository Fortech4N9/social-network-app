<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\User;
use App\Models\UserPost;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PostService
{
    private Friend $friendModel;
    private UserPost $postModel;

    const EXPECTATION_STATUS = 'expectation';
    const DEVIATION_STATUS = 'deviation';
    const CONFIRMATION_STATUS = 'confirmation';

    public function __construct(Friend $friendModel, UserPost $postModel)
    {
        $this->friendModel = $friendModel;
        $this->postModel = $postModel;
    }

    public function addPost(int $userId, string $content, string $title): Model|UserPost
    {
        return $this->postModel->addPost($userId, $content, $title);
    }

    public function getUserPosts(int $userId): array
    {
        return $this->postModel->getPostsByUser($userId);
    }

    public function getFriendsPosts(int $userId): array
    {
        $friendsIds = $this->friendModel->getAllUserFriendsIds($userId, self::CONFIRMATION_STATUS);
        $posts = $this->postModel->getPostsByUsers($friendsIds);
        foreach ($posts as $index => $post) {
            $user = $this->getUserById($post['id_user']);
            $posts[$index]['name'] = $user->name;
        }
        return $posts;
    }

    public function getUserById(int $id): Model|Collection|array|User|null
    {
        return (new User)->find($id);
    }

    public function deletePost(int $postId): bool
    {
        return $this->postModel->deletePost($postId);
    }

    public function updatePost(int $postId, string $content, string $title): Model|Collection|UserPost|array|null
    {
        return $this->postModel->updatePost($postId,$content,$title);
    }

}
