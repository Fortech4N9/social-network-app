<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Friends
Route::prefix('friends')->middleware(['auth', 'verified'])->group(function () {
    //get
    Route::get('/search-friends', [FriendsController::class, 'search'])->name('search-friends');
    Route::get('/request-friends', [FriendsController::class, 'request'])->name('request-friends');
    Route::get('/index', [FriendsController::class, 'index'])->name('index');

    //post
    Route::post('/add-friend-request', [FriendsController::class, 'addFriendRequest'])->name('add-friend-request');
    Route::post('/cancel-friend-request', [FriendsController::class, 'cancelFriendRequest'])->name(
        'cancel-friend-request'
    );
    Route::post('/add-friend', [FriendsController::class, 'addFriend'])->name('add-friend');
    Route::post('/cancel-friend', [FriendsController::class, 'cancelFriend'])->name('cancel-friend');

    // Маршрут
    Route::get('/messages/{friendId}', [ChatController::class, 'messages'])->name('messages');
    Route::post('/send', [ChatController::class, 'send'])->name('send');
});

Route::get('/dashboard', [PostController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/add-post', [PostController::class, 'addPost'])->middleware(['auth', 'verified'])->name('add-post');
Route::post('/delete-post', [PostController::class, 'deletePost'])->middleware(['auth', 'verified'])->name(
    'delete-post'
);
Route::post('/update-post', [PostController::class, 'updatePost'])->middleware(['auth', 'verified'])->name(
    'update-post'
);

Route::prefix('news')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/friends-posts', [PostController::class, 'friendsPosts'])->name('friends-posts');
});

require __DIR__ . '/auth.php';
