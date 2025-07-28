<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $alertType;
    public $alertTitle;
    public $alertMessage;
    public $alertData;
    public $severity;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $alertType,
        string $alertTitle,
        string $alertMessage,
        array $alertData = [],
        string $severity = 'medium',
        ?User $recipient = null
    ) {
        $this->alertType = $alertType;
        $this->alertTitle = $alertTitle;
        $this->alertMessage = $alertMessage;
        $this->alertData = $alertData;
        $this->severity = $severity;
        $this->recipient = $recipient;
        
        // Set queue based on severity
        $this->onQueue($this->getQueueName($severity));
        $this->delay($this->getDelay($severity));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $recipientEmail = $this->recipient?->email ?? config('mail.admin_email', 'admin@example.com');
        $severityPrefix = $this->getSeverityPrefix($this->severity);
        
        return new Envelope(
            to: [$recipientEmail],
            subject: "{$severityPrefix}{$this->alertTitle}",
            tags: ['system-alert', $this->alertType, $this->severity],
            metadata: [
                'alert_type' => $this->alertType,
                'severity' => $this->severity,
                'recipient_id' => $this->recipient?->id,
                'timestamp' => now()->toISOString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.system.alert',
            with: [
                'recipientName' => $this->recipient?->name ?? 'Administrator',
                'alertType' => $this->alertType,
                'alertTitle' => $this->alertTitle,
                'alertMessage' => $this->alertMessage,
                'alertData' => $this->alertData,
                'severity' => $this->severity,
                'severityColor' => $this->getSeverityColor($this->severity),
                'severityIcon' => $this->getSeverityIcon($this->severity),
                'timestamp' => now(),
                'actionUrl' => $this->getActionUrl($this->alertType),
                'actionText' => $this->getActionText($this->alertType),
                'recommendations' => $this->getRecommendations($this->alertType),
                'systemHealth' => $this->getSystemHealth(),
                'dashboardUrl' => route('admin.dashboard'),
                'supportEmail' => config('mail.from.address'),
                'systemName' => config('app.name'),
                'serverInfo' => [
                    'environment' => config('app.env'),
                    'server_time' => now()->format('Y-m-d H:i:s T'),
                    'app_version' => config('app.version', '1.0.0'),
                ],
            ],
        );
    }

    /**
     * Get queue name based on severity.
     */
    protected function getQueueName(string $severity): string
    {
        return match($severity) {
            'critical' => 'critical-alerts',
            'high' => 'high-priority',
            'medium' => 'emails',
            'low' => 'low-priority',
            default => 'emails',
        };
    }

    /**
     * Get delay based on severity.
     */
    protected function getDelay(string $severity): \DateTime
    {
        return match($severity) {
            'critical' => now(), // Send immediately
            'high' => now()->addSeconds(10),
            'medium' => now()->addSeconds(30),
            'low' => now()->addMinutes(5),
            default => now()->addSeconds(30),
        };
    }

    /**
     * Get severity prefix for subject.
     */
    protected function getSeverityPrefix(string $severity): string
    {
        return match($severity) {
            'critical' => '[CRITICAL] ',
            'high' => '[HIGH] ',
            'medium' => '[ALERT] ',
            'low' => '[INFO] ',
            default => '[ALERT] ',
        };
    }

    /**
     * Get severity color for styling.
     */
    protected function getSeverityColor(string $severity): string
    {
        return match($severity) {
            'critical' => '#dc2626', // red-600
            'high' => '#ea580c',      // orange-600
            'medium' => '#d97706',    // amber-600
            'low' => '#059669',       // emerald-600
            default => '#6b7280',     // gray-500
        };
    }

    /**
     * Get severity icon.
     */
    protected function getSeverityIcon(string $severity): string
    {
        return match($severity) {
            'critical' => '🚨',
            'high' => '⚠️',
            'medium' => '🔔',
            'low' => 'ℹ️',
            default => '🔔',
        };
    }

    /**
     * Get action URL based on alert type.
     */
    protected function getActionUrl(string $alertType): ?string
    {
        return match($alertType) {
            'low_stock' => route('admin.inventory.index'),
            'payment_failure' => route('admin.payments.failed'),
            'security_breach' => route('admin.security.logs'),
            'system_error' => route('admin.logs.errors'),
            'user_activity' => route('admin.users.activity'),
            'performance' => route('admin.system.performance'),
            'backup_failure' => route('admin.system.backups'),
            'disk_space' => route('admin.system.storage'),
            default => route('admin.dashboard'),
        };
    }

    /**
     * Get action text based on alert type.
     */
    protected function getActionText(string $alertType): string
    {
        return match($alertType) {
            'low_stock' => 'View Inventory',
            'payment_failure' => 'Check Payments',
            'security_breach' => 'Review Security',
            'system_error' => 'View Error Logs',
            'user_activity' => 'Check User Activity',
            'performance' => 'View Performance',
            'backup_failure' => 'Check Backups',
            'disk_space' => 'View Storage',
            default => 'View Dashboard',
        };
    }

    /**
     * Get recommendations based on alert type.
     */
    protected function getRecommendations(string $alertType): array
    {
        return match($alertType) {
            'low_stock' => [
                'Review inventory levels and reorder products',
                'Set up automatic stock alerts for better management',
                'Consider increasing safety stock for popular items',
            ],
            'payment_failure' => [
                'Check payment gateway status and configurations',
                'Review failed transactions for patterns',
                'Contact payment provider if issues persist',
            ],
            'security_breach' => [
                'Immediately review and secure affected accounts',
                'Change passwords and enable 2FA where possible',
                'Monitor system logs for unusual activity',
            ],
            'system_error' => [
                'Check server resources and application logs',
                'Review recent deployments or configuration changes',
                'Monitor system performance and error rates',
            ],
            'performance' => [
                'Check database query performance',
                'Review server resource utilization',
                'Consider scaling if load is consistently high',
            ],
            'backup_failure' => [
                'Verify backup system configuration',
                'Check available storage space',
                'Test backup restoration process',
            ],
            'disk_space' => [
                'Clean up temporary files and old logs',
                'Archive or delete unnecessary data',
                'Consider increasing storage capacity',
            ],
            default => [
                'Review the alert details carefully',
                'Take appropriate action based on the alert type',
                'Monitor the situation for any changes',
            ],
        };
    }

    /**
     * Get current system health status.
     */
    protected function getSystemHealth(): array
    {
        return [
            'status' => 'operational', // This would normally be calculated
            'uptime' => '99.9%',
            'last_backup' => now()->subHours(6)->format('M d, Y H:i'),
            'active_users' => '127', // This would be dynamically calculated
            'server_load' => 'Normal',
        ];
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach relevant logs for critical alerts
        if ($this->severity === 'critical' && isset($this->alertData['log_file'])) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath(
                storage_path('logs/' . $this->alertData['log_file'])
            )->as('error_log.txt')->withMime('text/plain');
        }

        return $attachments;
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSend(): bool
    {
        // Always send critical alerts
        if ($this->severity === 'critical') {
            return true;
        }

        // Check if recipient has alert notifications enabled
        if ($this->recipient && !($this->recipient->system_alerts ?? true)) {
            return false;
        }

        // Don't spam with duplicate alerts
        $recentAlerts = cache()->get("system_alert_{$this->alertType}", []);
        $duplicateThreshold = now()->subMinutes(30);
        
        foreach ($recentAlerts as $timestamp) {
            if ($timestamp > $duplicateThreshold) {
                return false; // Skip duplicate alert
            }
        }

        // Cache this alert
        $recentAlerts[] = now();
        cache()->put("system_alert_{$this->alertType}", $recentAlerts, now()->addHours(2));

        return true;
    }
}
