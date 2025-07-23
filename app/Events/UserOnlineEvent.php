<?php

namespace App\Events;

use App\Models\User;
use App\Models\ActiveSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $session;
    public $totalActiveUsers;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user = null, ActiveSession $session, int $totalActiveUsers)
    {
        $this->user = $user;
        $this->session = $session;
        $this->totalActiveUsers = $totalActiveUsers;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-monitoring'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.online';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'session' => [
                'id' => $this->session->id,
                'session_id' => $this->session->session_id,
                'ip_address' => $this->session->ip_address,
                'location' => $this->session->location,
                'page_url' => $this->session->page_url,
                'last_activity' => $this->session->last_activity->toISOString(),
                'is_authenticated' => !is_null($this->session->user_id),
            ],
            'total_active_users' => $this->totalActiveUsers,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
