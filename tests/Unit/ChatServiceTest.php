<?php

namespace Tests\Unit;

use App\Models\Message;
use App\Models\User;
use App\Models\UserChat;
use App\Services\ChatService;
use Mockery;
use PHPUnit\Framework\TestCase;

class ChatServiceTest extends TestCase
{
    private $chatModel;
    private $messageModel;
    private $userModel;

    private $chatService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chatModel = Mockery::mock(UserChat::class);
        $this->messageModel = Mockery::mock(Message::class);
        $this->userModel = Mockery::mock(User::class);

        $this->chatService = new ChatService($this->chatModel, $this->messageModel);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testGetMessagesByFriend()
    {
        $idChat = 1;
        $idUserOne = 1;
        $idUserTwo = 2;
        $message = 'test message';
        $this->chatModel->shouldReceive('existsChat')->with($idUserOne, $idUserTwo)->andReturn(true);
        $this->chatModel->shouldReceive('existsChat')->with($idUserTwo, $idUserOne)->andReturn(true);

        $this->chatModel->shouldReceive('getChatId')->with($idUserOne, $idUserTwo)->andReturn();
        $this->chatModel->shouldReceive('getChatId')->with($idUserTwo, $idUserOne)->andReturn(['id'=>$idChat]);

        $this->messageModel->shouldReceive('getAllMessagesByChatId')->andReturn([
            0 => [
                'message' => $message,
                'chat_id' => $idChat,
                'sender_id' => $idUserOne
            ]
        ]);

        $messagesInUserOne = $this->chatService->getMessagesByFriend($idUserOne, $idUserTwo);
        $messagesInUserTwo = $this->chatService->getMessagesByFriend($idUserTwo, $idUserOne);

        $this->assertIsArray($messagesInUserOne);
        $this->assertIsArray($messagesInUserTwo);
        $this->assertEquals('test message', $messagesInUserOne[0]['message']);
        $this->assertEquals('test message', $messagesInUserTwo[0]['message']);
    }

    public function testAddChat()
    {
        $idFriendOne = 1;
        $idFriendTwo = 2;
        $idFriendThree = 3;
        $chat = new UserChat();
        $this->chatModel->shouldReceive('existsChat')->with($idFriendOne, $idFriendTwo)->andReturn(true);
        $this->chatModel->shouldReceive('existsChat')->with($idFriendOne, $idFriendThree)->andReturn(false);
        $this->chatModel->shouldReceive('existsChat')->with($idFriendTwo, $idFriendThree)->andReturn(false);

        $this->chatModel->shouldReceive('createChat')->with($idFriendOne, $idFriendThree)->andReturn($chat);
        $this->chatModel->shouldReceive('createChat')->with($idFriendTwo, $idFriendThree)->andReturn($chat);

        $addChatWithFriendOneAndTwo = $this->chatService->addChat($idFriendOne, $idFriendTwo);
        $addChatWithFriendOneAndThree = $this->chatService->addChat($idFriendOne, $idFriendThree);
        $addChatWithFriendTwoAndThree = $this->chatService->addChat($idFriendTwo, $idFriendThree);

        $this->assertFalse($addChatWithFriendOneAndTwo);
        $this->assertTrue($addChatWithFriendOneAndThree);
        $this->assertTrue($addChatWithFriendTwoAndThree);
    }
}
