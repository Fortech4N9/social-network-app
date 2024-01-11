<?php

namespace App\Observers;

use App\Models\Message;
use App\Events\MessageSent;

class MessageObserver
{
    public function created(Message $message)
    {
        broadcast(new MessageSent($message))->toOthers();
    }
}
