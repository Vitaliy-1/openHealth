<?php

namespace App\Events;

use App\Models\LegalEntity;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EhealthUserLoggedIn
{
    use Dispatchable, SerializesModels;

    public string $token = '';

    /**
     * Create a new event instance.
     */
    public function __construct(
        public LegalEntity $legalEntity,
        public bool $isFirstLogin = false
    ) {
    }
}
