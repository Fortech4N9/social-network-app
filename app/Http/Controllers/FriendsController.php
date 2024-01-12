<?php

namespace App\Http\Controllers;

use App\Services\FriendService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FriendsController extends Controller
{
    protected $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    public function search(): Response
    {
        $friendsList = $this->friendService->getAllUsers();
        return Inertia::render('Friends/Search', ['friendsList' => $friendsList]);
    }

    public function index(): Response
    {
        $friendsList = $this->friendService->getUsersFriends();
        return Inertia::render('Friends/Index', ['friendsList' => $friendsList, 'user' => auth()->user()]);
    }

    public function request(): Response
    {
        $requestsList = $this->friendService->getUsersRequests();
        return Inertia::render('Friends/Request', ['requestsList' => $requestsList]);
    }

    /**
     * Добавить запрос на добавление в друзья.
     */
    public function addFriendRequest(Request $request): RedirectResponse
    {
        $ids = $this->getIds($request);
        $this->friendService->addFriendRequest($ids['senderId'], $ids['recipientId']);
        return back()->with('success', 'Запрос в друзья отправлен.');
    }

    /**
     * Отменить запрос на отмену запроса в друзья.
     */
    public function cancelFriendRequest(Request $request): RedirectResponse
    {
        $ids = $this->getIds($request);
        $this->friendService->cancelRequest($ids['senderId'], $ids['recipientId']);
        return back()->with('success', 'Запрос в друзья отменён.');
    }

    /**
     * Отменить запрос на отмену запроса в друзья.
     */
    public function cancelFriend(Request $request): RedirectResponse
    {
        $ids = $this->getIds($request);
        $this->friendService->cancelFriend($ids['senderId'], $ids['recipientId']);
        return back()->with('success', 'Дружба прекращена');
    }

    public function addFriend(Request $request): RedirectResponse
    {
        $ids = $this->getIds($request);
        $this->friendService->addFriend($ids['senderId'], $ids['recipientId']);
        return back()->with('success', 'Пользователь добавлен в друзья');
    }
    private function getIds(Request $request): array
    {
        return [
            'senderId'=>Auth::user()->id,
            'recipientId'=>$request->input('friendId'),
        ];
    }
}
