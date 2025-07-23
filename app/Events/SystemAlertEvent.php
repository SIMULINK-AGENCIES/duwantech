<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemAlertEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $alertType;
    public $severity;
    public $title;
    public $message;
    public $data;
    public $actionUrl;

    const TYPE_SYSTEM_ERROR = 'system_error';
    const TYPE_SECURITY_ALERT = 'security_alert';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_PERFORMANCE = 'performance';
    const TYPE_BACKUP = 'backup';
    const TYPE_UPDATE = 'update';

    const SEVERITY_LOW = 'low';
    const SEVERITY_MEDIUM = 'medium';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $alertType,
        string $severity,
        string $title,
        string $message,
        array $data = [],
        string $actionUrl = null
    ) {
        $this->alertType = $alertType;
        $this->severity = $severity;
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->actionUrl = $actionUrl;
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
            new PrivateChannel('admin-system'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'system.alert';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'alert' => [
                'type' => $this->alertType,
                'severity' => $this->severity,
                'title' => $this->title,
                'message' => $this->message,
                'icon' => $this->getAlertIcon(),
                'color' => $this->getAlertColor(),
                'action_url' => $this->actionUrl,
            ],
            'system_data' => array_merge($this->data, [
                'server_time' => now()->toISOString(),
                'memory_usage' => $this->getMemoryUsage(),
                'disk_usage' => $this->getDiskUsage(),
            ]),
            'notification' => [
                'title' => $this->title,
                'message' => $this->message,
                'type' => 'system',
                'priority' => $this->severity === self::SEVERITY_CRITICAL ? 'critical' : 
                           ($this->severity === self::SEVERITY_HIGH ? 'high' : 'medium'),
                'action_url' => $this->actionUrl,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get alert icon based on type.
     */
    private function getAlertIcon(): string
    {
        return match($this->alertType) {
            self::TYPE_SYSTEM_ERROR => 'fas fa-exclamation-triangle',
            self::TYPE_SECURITY_ALERT => 'fas fa-shield-alt',
            self::TYPE_MAINTENANCE => 'fas fa-tools',
            self::TYPE_PERFORMANCE => 'fas fa-tachometer-alt',
            self::TYPE_BACKUP => 'fas fa-database',
            self::TYPE_UPDATE => 'fas fa-download',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get alert color based on severity.
     */
    private function getAlertColor(): string
    {
        return match($this->severity) {
            self::SEVERITY_LOW => 'text-blue-500',
            self::SEVERITY_MEDIUM => 'text-yellow-500',
            self::SEVERITY_HIGH => 'text-orange-500',
            self::SEVERITY_CRITICAL => 'text-red-500',
            default => 'text-gray-500',
        };
    }

    /**
     * Get current memory usage.
     */
    private function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        return [
            'current' => $this->formatBytes($memoryUsage),
            'peak' => $this->formatBytes($memoryPeak),
            'current_bytes' => $memoryUsage,
            'peak_bytes' => $memoryPeak,
        ];
    }

    /**
     * Get disk usage information.
     */
    private function getDiskUsage(): array
    {
        $bytes = disk_free_space('/');
        $totalBytes = disk_total_space('/');
        
        return [
            'free' => $this->formatBytes($bytes),
            'total' => $this->formatBytes($totalBytes),
            'used_percentage' => $totalBytes > 0 ? round((($totalBytes - $bytes) / $totalBytes) * 100, 2) : 0,
        ];
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Create a system error alert.
     */
    public static function systemError(string $title, string $message, array $data = []): self
    {
        return new self(
            self::TYPE_SYSTEM_ERROR,
            self::SEVERITY_HIGH,
            $title,
            $message,
            $data
        );
    }

    /**
     * Create a security alert.
     */
    public static function securityAlert(string $title, string $message, array $data = []): self
    {
        return new self(
            self::TYPE_SECURITY_ALERT,
            self::SEVERITY_CRITICAL,
            $title,
            $message,
            $data
        );
    }

    /**
     * Create a maintenance alert.
     */
    public static function maintenanceAlert(string $title, string $message, array $data = []): self
    {
        return new self(
            self::TYPE_MAINTENANCE,
            self::SEVERITY_MEDIUM,
            $title,
            $message,
            $data
        );
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        return true;
    }
}
