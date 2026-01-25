<?php

namespace App\Jobs;

use App\Events\NotificationBroadcasted;
use App\Mail\SystemNotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $userId;
    public array $payload;

    public int $tries = 5;
    public int $backoff = 10;

    public function __construct(string $userId, array $payload)
    {
        $this->userId = $userId;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        // 1️⃣ Store notification
        $notification = Notification::create([
            'user_id' => $this->userId,
            'type'    => $this->payload['type'],
            'data'    => $this->payload,
        ]);

        // 2️⃣ Realtime broadcast
        broadcast(new NotificationBroadcasted(
            $this->userId,
            array_merge($this->payload, [
                'id' => $notification->id,
                'created_at' => $notification->created_at,
            ])
        ));

        // 3️⃣ Email (if enabled)
        $user = User::find($this->userId);

        if ($user && $user->email_notifications) {
            Mail::to($user->email)->send(
                new SystemNotificationMail([
                    'subject' => 'New activity on your board',
                    'title'   => 'You have a new notification',
                    'message' => $this->payload['message'] ?? '',
                    'meta'    => $this->payload['meta'] ?? [],
                ])
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        logger()->error('Notification job failed', [
            'user_id' => $this->userId,
            'payload' => $this->payload,
            'error'   => $exception->getMessage(),
        ]);
    }
}
