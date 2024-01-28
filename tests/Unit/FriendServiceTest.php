<?php

namespace Tests\Unit;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use App\Services\ChatService;
use App\Services\FriendService;
use Mockery;
use PHPUnit\Framework\TestCase;

class FriendServiceTest extends TestCase
{
    private Friend $friendModel;
    private User $userModel;
    private ChatService $chatService;
    private FriendRequest $friendRequestModel;

    private FriendService $friendService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->friendModel = Mockery::mock(Friend::class);
        $this->friendRequestModel = Mockery::mock(FriendRequest::class);
        $this->userModel = Mockery::mock(User::class);
        $this->chatService = Mockery::mock(ChatService::class);

        $this->friendService = new FriendService(
            $this->friendModel,
            $this->userModel,
            $this->friendRequestModel,
            $this->chatService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testGetAllUsersIfUserHasNoFriendsAndNoRequests()
    {
        $authenticatedUser = [
            'id' => 1,
            'email' => 'test@example.com',
        ];
        $otherUsers = [
            0 => [
                'id' => 2,
                'email' => 'other@example.com',
            ],
            1 => [
                'id' => 3,
                'email' => 'other2@example.com',
            ]
        ];

        $this->friendRequestModel->shouldReceive('friendRequestExists')->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->andReturn(false);

        $this->friendService = Mockery::mock(
            FriendService::class,
            [$this->friendModel, $this->userModel, $this->friendRequestModel, $this->chatService]
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->friendService->shouldReceive('getAuthUser')->andReturn($authenticatedUser);
        $this->friendService->shouldReceive('getAllUsersListWithoutAuthUser')->andReturn($otherUsers);

        $otherUsersResult = [
            0 => [
                'id' => 2,
                'email' => 'other@example.com',
                'requestSent' => false
            ],
            1 => [
                'id' => 3,
                'email' => 'other2@example.com',
                'requestSent' => false
            ]
        ];

        $result = $this->friendService->getAllUsers();
        $this->assertIsArray($result);
        $this->assertEquals($otherUsersResult, $result);
    }

    public function testGetAllUsersIfUserIsAllFriends()
    {
        $authenticatedUser = [
            'id' => 1,
            'email' => 'test@example.com',
        ];
        $otherUsers = [
            0 => [
                'id' => 2,
                'email' => 'other@example.com',
            ],
            1 => [
                'id' => 3,
                'email' => 'other2@example.com',
            ]
        ];

        $this->friendRequestModel->shouldReceive('friendRequestExists')->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->andReturn(true);

        $this->friendService = Mockery::mock(
            FriendService::class,
            [$this->friendModel, $this->userModel, $this->friendRequestModel, $this->chatService]
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->friendService->shouldReceive('getAuthUser')->andReturn($authenticatedUser);
        $this->friendService->shouldReceive('getAllUsersListWithoutAuthUser')->andReturn($otherUsers);

        $result = $this->friendService->getAllUsers();
        $this->assertIsArray($result);
        $this->assertEquals([], $result);
    }

    public function testGetAllUsersIfUserIsOneFriendAndNoRequest()
    {
        $authenticatedUser = [
            'id' => 1,
            'email' => 'test@example.com',
        ];
        $otherUsers = [
            0 => [
                'id' => 2,
                'email' => 'other@example.com',
            ],
            1 => [
                'id' => 3,
                'email' => 'other2@example.com',
            ]
        ];

        $this->friendRequestModel->shouldReceive('friendRequestExists')->andReturn(true);
        $this->friendModel->shouldReceive('friendExists')->with(
            $authenticatedUser['id'],
            $otherUsers[0]['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(true);
        $this->friendModel->shouldReceive('friendExists')->with(
            $authenticatedUser['id'],
            $otherUsers[1]['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->with(
            $otherUsers[1]['id'],
            $authenticatedUser['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);

        $this->friendService = Mockery::mock(
            FriendService::class,
            [$this->friendModel, $this->userModel, $this->friendRequestModel, $this->chatService]
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->friendService->shouldReceive('getAuthUser')->andReturn($authenticatedUser);
        $this->friendService->shouldReceive('getAllUsersListWithoutAuthUser')->andReturn($otherUsers);

        $otherUsersResult = [
            0 => [
                'id' => 3,
                'email' => 'other2@example.com',
                'requestSent' => true
            ]
        ];

        $result = $this->friendService->getAllUsers();
        $this->assertIsArray($result);
        $this->assertEquals($otherUsersResult, $result);
    }

    public function testGetAllUsersIfUserIsOneFriendAndOneRequest()
    {
        $authenticatedUser = [
            'id' => 1,
            'email' => 'test@example.com',
        ];
        $otherUsers = [
            0 => [
                'id' => 2,
                'email' => 'other@example.com',
            ],
            1 => [
                'id' => 3,
                'email' => 'other2@example.com',
            ],
            2 => [
                'id' => 4,
                'email' => 'other3@example.com',
            ]
        ];

        $this->friendRequestModel->shouldReceive('friendRequestExists')->with(
            $authenticatedUser['id'],
            $otherUsers[1]['id'],
            $this->friendService::EXPECTATION_STATUS)->andReturn(true);
        $this->friendRequestModel->shouldReceive('friendRequestExists')->with(
            $authenticatedUser['id'],
            $otherUsers[2]['id'],
            $this->friendService::EXPECTATION_STATUS)->andReturn(false);
        $this->friendRequestModel->shouldReceive('friendRequestExists')->with(
            $authenticatedUser['id'],
            $otherUsers[0]['id'],
            $this->friendService::EXPECTATION_STATUS)->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->with(
            $authenticatedUser['id'],
            $otherUsers[0]['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(true);
        $this->friendModel->shouldReceive('friendExists')->with(
            $authenticatedUser['id'],
            $otherUsers[1]['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->with(
            $otherUsers[1]['id'],
            $authenticatedUser['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->with(
            $authenticatedUser['id'],
            $otherUsers[2]['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);
        $this->friendModel->shouldReceive('friendExists')->with(
            $otherUsers[2]['id'],
            $authenticatedUser['id'],
            $this->friendService::CONFIRMATION_STATUS
        )->andReturn(false);

        $this->friendService = Mockery::mock(
            FriendService::class,
            [$this->friendModel, $this->userModel, $this->friendRequestModel, $this->chatService]
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->friendService->shouldReceive('getAuthUser')->andReturn($authenticatedUser);
        $this->friendService->shouldReceive('getAllUsersListWithoutAuthUser')->andReturn($otherUsers);

        $otherUsersResult = [
            0 => [
                'id' => 3,
                'email' => 'other2@example.com',
                'requestSent' => true
            ],
            1 => [
                'id' => 4,
                'email' => 'other3@example.com',
                'requestSent' => false
            ],
        ];

        $result = $this->friendService->getAllUsers();
        $this->assertIsArray($result);
        $this->assertEquals($otherUsersResult, $result);
    }
}
