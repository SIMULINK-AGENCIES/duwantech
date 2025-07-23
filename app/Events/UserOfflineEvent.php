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

class UserOfflineEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $sessionId;
    public $totalActiveUsers;
    public $sessionDuration;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user = null, string $sessionId, int $totalActiveUsers, int $sessionDuration = 0)
    {
        $this->user = $user;
        $this->sessionId = $sessionId;
        $this->totalActiveUsers = $totalActiveUsers;
        $this->sessionDuration = $sessionDuration;
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
        return 'user.offline';
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
            'session_id' => $this->sessionId,
            'total_active_users' => $this->totalActiveUsers,
            'session_duration' => $this->sessionDuration, // in seconds
            'session_duration_human' => $this->formatDuration($this->sessionDuration),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Format duration in human readable format.
     */
    private function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' minutes';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . ' hours' . ($remainingMinutes > 0 ? ', ' . $remainingMinutes . ' minutes' : '');
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
