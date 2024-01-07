<?php

use App\Http\Controllers\FriendsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Friends
Route::prefix('friends')->middleware(['auth', 'verified'])->group(function () {

    //get
    Route::get('/search-friends', [FriendsController::class, 'search'])->name('search-friends');
    Route::get('/request-friends', [FriendsController::class, 'request'])->name('request-friends');
    Route::get('/index', [FriendsController::class, 'index'])->name('index');

    //post
    Route::post('/add-friend-request', [FriendsController::class, 'addFriendRequest'])->name('add-friend-request');
    Route::post('/cancel-friend-request', [FriendsController::class, 'cancelFriendRequest'])->name('cancel-friend-request');
    Route::post('/add-friend', [FriendsController::class, 'addFriend'])->name('add-friend');
    Route::post('/cancel-friend', [FriendsController::class, 'cancelFriend'])->name('cancel-friend');
});

require __DIR__ . '/auth.php';
