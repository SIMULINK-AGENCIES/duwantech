<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewActivityEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activity;
    public $activityData;

    /**
     * Create a new event instance.
     */
    public function __construct(ActivityLog $activity)
    {
        $this->activity = $activity;
        $this->activityData = $this->formatActivityData($activity);
    }

    /**
     * Get the channels the event should broadcast on.
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
        return 'activity.new';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'activity' => $this->activityData,
            'timestamp' => now()->toISOString(),
            'message' => $this->generateMessage(),
        ];
    }

    /**
     * Format activity data for broadcasting
     */
    private function formatActivityData(ActivityLog $activity): array
    {
        return [
            'id' => $activity->id,
            'action' => $activity->action,
            'description' => $activity->description,
            'user' => $activity->user ? [
                'id' => $activity->user->id,
                'name' => $activity->user->name,
                'email' => $activity->user->email,
                'avatar' => $activity->user->profile_photo_path ?? null
            ] : null,
            'metadata' => $activity->metadata,
            'ip_address' => $activity->ip_address,
            'user_agent' => $activity->user_agent,
            'created_at' => $activity->created_at->toISOString(),
            'created_at_human' => $activity->created_at->diffForHumans(),
            'formatted_time' => $activity->created_at->format('M j, Y g:i A'),
            'icon' => $this->getActivityIcon($activity->action),
            'color' => $this->getActivityColor($activity->action),
            'priority' => $activity->metadata['priority'] ?? 'medium'
        ];
    }

    /**
     * Generate a human-readable message for the activity
     */
    private function generateMessage(): string
    {
        $userName = $this->activity->user ? $this->activity->user->name : 'Guest user';
        
        $messages = [
            'login' => "{$userName} logged in",
            'logout' => "{$userName} logged out",
            'registration' => "{$userName} registered a new account",
            'order_created' => "{$userName} created a new order",
            'order_updated' => "{$userName} updated an order",
            'order_cancelled' => "{$userName} cancelled an order",
            'payment_initiated' => "{$userName} initiated a payment",
            'payment_completed' => "{$userName} completed a payment",
            'payment_failed' => "Payment failed for {$userName}",
            'product_viewed' => "{$userName} viewed a product",
            'cart_updated' => "{$userName} updated their cart",
            'wishlist_updated' => "{$userName} updated their wishlist",
            'profile_updated' => "{$userName} updated their profile",
            'password_changed' => "{$userName} changed their password",
            'email_verified' => "{$userName} verified their email",
            'system_error' => "System error occurred",
            'admin_action' => "Admin performed an action"
        ];

        return $messages[$this->activity->action] ?? "{$userName} performed: {$this->activity->action}";
    }

    /**
     * Get icon for activity type
     */
    private function getActivityIcon($action): string
    {
        $icons = [
            'login' => 'login',
            'logout' => 'logout',
            'registration' => 'user-plus',
            'order_created' => 'shopping-bag',
            'order_updated' => 'edit',
            'order_cancelled' => 'x-circle',
            'payment_initiated' => 'credit-card',
            'payment_completed' => 'check-circle',
            'payment_failed' => 'x-circle',
            'product_viewed' => 'eye',
            'cart_updated' => 'shopping-cart',
            'wishlist_updated' => 'heart',
            'profile_updated' => 'user',
            'password_changed' => 'lock',
            'email_verified' => 'mail',
            'system_error' => 'alert-triangle',
            'admin_action' => 'shield'
        ];

        return $icons[$action] ?? 'activity';
    }

    /**
     * Get color for activity type
     */
    private function getActivityColor($action): string
    {
        $colors = [
            'login' => 'green',
            'logout' => 'gray',
            'registration' => 'blue',
            'order_created' => 'indigo',
            'order_updated' => 'yellow',
            'order_cancelled' => 'red',
            'payment_initiated' => 'purple',
            'payment_completed' => 'green',
            'payment_failed' => 'red',
            'product_viewed' => 'blue',
            'cart_updated' => 'orange',
            'wishlist_updated' => 'pink',
            'profile_updated' => 'indigo',
            'password_changed' => 'yellow',
            'email_verified' => 'green',
            'system_error' => 'red',
            'admin_action' => 'purple'
        ];

        return $colors[$action] ?? 'gray';
    }
}
