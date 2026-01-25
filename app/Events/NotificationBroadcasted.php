<?php

namespace App\Events;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;


User::first()->id;

class NotificationBroadcasted implements ShouldBroadcast
{
    use SerializesModels;

    public string $userId;
    public array $payload;

    public function __construct(?string $userId, array $payload)
    {
        if (! $userId) {
            throw new \InvalidArgumentException('Notification userId cannot be null');
        }

        $this->userId = $userId;
        $this->payload = $payload;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }
}
