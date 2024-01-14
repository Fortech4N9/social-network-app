<?php

use App\Broadcasting\ChatChannel;
use App\Broadcasting\PostChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat', ChatChannel::class);
Broadcast::channel('post', PostChannel::class);
