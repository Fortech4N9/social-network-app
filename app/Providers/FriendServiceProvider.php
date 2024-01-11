<?php

namespace App\Providers;


use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\User;
use App\Models\UserChat;
use App\Services\ChatService;
use App\Services\FriendService;
use Illuminate\Support\ServiceProvider;

class FriendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FriendService::class, function ($app) {
            $userModel = new User();
            $friendModel = new Friend();
            $friendRequestModel = new FriendRequest();
            $chatModel = new UserChat();
            $messagesModel = new Message();
            $chatService = new ChatService($chatModel, $messagesModel);
            return new FriendService($friendModel, $userModel, $friendRequestModel, $chatService);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
