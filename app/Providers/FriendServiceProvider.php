<?php

namespace App\Providers;


use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
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
            return new FriendService($friendModel, $userModel, $friendRequestModel);
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
