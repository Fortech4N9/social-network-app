<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\UserChat;
use App\Services\ChatService;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ChatService::class, function ($app) {
            $userChatModel = new UserChat();
            $messageModel = new Message();
            return new ChatService($userChatModel, $messageModel);
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
