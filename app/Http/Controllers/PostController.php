<?php

namespace App\Http\Controllers;

use App\Events\PostSent;
use App\Http\Requests\PostFormRequest;
use App\Models\UserPost;
use App\Services\PostService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{

    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function friendsPosts(): Response
    {
        $friendsPosts = $this->postService->getFriendsPosts(auth()->user()->id);
        return Inertia::render('Posts/FriendsPosts', ['posts' => $friendsPosts]);
    }

    public function dashboard(): Response
    {
        $userPosts = $this->postService->getUserPosts(auth()->user()->id);
        return Inertia::render('Dashboard', ['posts' => $userPosts]);
    }

    public function addPost(PostFormRequest $request): Model|UserPost
    {
        $content = $request->input('content');
        $title = $request->input('title');
        $userId = auth()->user()->id;
        $userPost = $this->postService->addPost($userId, $content, $title);
        $user = $this->postService->getUserById($userId);
        broadcast(new PostSent($user, $userPost));

        return $userPost;
    }

    public function deletePost(Request $request): RedirectResponse
    {
        $postId = $request->input('postId');
        $this->postService->deletePost($postId);
        return back()->with('success', 'Пост удален');
    }

    public function updatePost(PostFormRequest $request): Model|Collection|UserPost|array|null
    {
        $postId = $request->input('postId');
        $content = $request->input('content');
        $title = $request->input('title');
        $userId = auth()->user()->id;
        $userPost = $this->postService->updatePost($postId, $content, $title);
        $user = $this->postService->getUserById($userId);
        broadcast(new PostSent($user, $userPost));
        return $userPost;
    }

}
