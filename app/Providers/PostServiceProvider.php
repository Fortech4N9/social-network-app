<?php

namespace App\Providers;

use App\Models\Friend;
use App\Models\UserPost;
use App\Services\PostService;
use Illuminate\Support\ServiceProvider;

class PostServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PostService::class, function ($app) {
            $userPostModel = new UserPost();
            $friendModel = new Friend();
            return new PostService($friendModel, $userPostModel);
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
