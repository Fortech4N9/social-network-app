<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Friend;
use App\Models\User;
use App\Models\UserPost;
use App\Services\PostService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

class PostServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    protected $friendModel;
    protected $userPostModel;
    protected $postService;

    protected $userModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->friendModel = Mockery::mock(Friend::class);
        $this->userPostModel = Mockery::mock(UserPost::class);
        $this->userModel = Mockery::mock(User::class);

        $this->postService = new PostService($this->friendModel, $this->userPostModel);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testGetFriendsPosts()
    {
        $userId = 1;
        $friendIds = [2, 3];
        $postsData = [
            ['id_user' => 2, 'content' => 'Content 1', 'title' => 'Title 1'],
            ['id_user' => 3, 'content' => 'Content 2', 'title' => 'Title 2']
        ];
        $friendOne = new User();
        $friendOne->id = 2;
        $friendOne->name = 'friend One';
        $friendTwo = new User();
        $friendTwo->id = 3;
        $friendTwo->name = 'friend Two';

        $this->friendModel->shouldReceive('getAllUserFriendsIds')
            ->with($userId, PostService::CONFIRMATION_STATUS)
            ->andReturn($friendIds);

        $this->userPostModel->shouldReceive('getPostsByUsers')->with($friendIds)->andReturn($postsData);

        $this->postService = Mockery::mock(PostService::class, [$this->friendModel, $this->userPostModel])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->postService->shouldReceive('getUserById')->with($friendOne->id)->andReturn($friendOne);
        $this->postService->shouldReceive('getUserById')->with($friendTwo->id)->andReturn($friendTwo);

        $posts = $this->postService->getFriendsPosts($userId);

        $this->assertIsArray($posts);
        foreach ($posts as $post) {
            if ($post['id_user']==2){
                $this->assertEquals('friend One', $post['name']);
            }
            if ($post['id_user']==3){
                $this->assertEquals('friend Two', $post['name']);
            }
        }
    }
}
