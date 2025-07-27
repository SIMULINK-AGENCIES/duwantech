<?php

namespace App\Mail;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $notification;
    public $adminEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(AdminNotification $notification, string $adminEmail)
    {
        $this->notification = $notification;
        $this->adminEmail = $adminEmail;
        
        // Set queue connection based on notification type
        $this->onQueue('emails');
        
        // Immediate sending for critical notifications
        if (in_array($this->notification->type, ['security_alert', 'system_error', 'critical'])) {
            $this->delay(now());
        } else {
            $this->delay(now()->addMinutes(1));
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $priority = $this->getPriority();
        $subject = "[{$priority}] {$this->notification->title}";
        
        return new Envelope(
            to: [$this->adminEmail],
            subject: $subject,
            tags: ['admin-notification', $this->notification->type],
            metadata: [
                'notification_id' => $this->notification->id,
                'notification_type' => $this->notification->type,
                'priority' => $priority,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.notification',
            with: [
                'notificationTitle' => $this->notification->title,
                'notificationMessage' => $this->notification->message,
                'notificationType' => $this->notification->type,
                'notificationData' => $this->notification->data,
                'priority' => $this->getPriority(),
                'urgencyColor' => $this->getUrgencyColor(),
                'createdAt' => $this->notification->created_at,
                'actionUrl' => $this->notification->action_url,
                'actionText' => $this->getActionText(),
                'storeName' => config('app.name'),
                'adminPanelUrl' => route('admin.dashboard'),
                'notificationSettingsUrl' => route('admin.settings.notifications'),
                'context' => $this->getNotificationContext(),
                'recommendations' => $this->getRecommendations(),
            ],
        );
    }

    /**
     * Get priority level based on notification type.
     */
    protected function getPriority(): string
    {
        return match($this->notification->type) {
            'security_alert', 'system_error' => 'CRITICAL',
            'inventory_out', 'payment_failed' => 'HIGH',
            'order', 'payment_success', 'inventory_low' => 'MEDIUM',
            'user_activity', 'info' => 'LOW',
            default => 'NORMAL',
        };
    }

    /**
     * Get urgency color for styling.
     */
    protected function getUrgencyColor(): string
    {
        return match($this->getPriority()) {
            'CRITICAL' => '#dc2626', // red-600
            'HIGH' => '#ea580c',      // orange-600
            'MEDIUM' => '#ca8a04',    // yellow-600
            'LOW' => '#16a34a',       // green-600
            default => '#2563eb',     // blue-600
        };
    }

    /**
     * Get action text based on notification type.
     */
    protected function getActionText(): string
    {
        return match($this->notification->type) {
            'order' => 'View Order Details',
            'payment_success', 'payment_failed' => 'View Payment',
            'inventory_out', 'inventory_low' => 'Manage Inventory',
            'user_activity' => 'View Activity',
            'security_alert' => 'Review Security',
            'system_error' => 'View System Status',
            default => 'View Details',
        };
    }

    /**
     * Get notification context information.
     */
    protected function getNotificationContext(): array
    {
        $context = [];
        
        if (isset($this->notification->data['order_id'])) {
            $context['Order ID'] = $this->notification->data['order_id'];
        }
        
        if (isset($this->notification->data['customer_name'])) {
            $context['Customer'] = $this->notification->data['customer_name'];
        }
        
        if (isset($this->notification->data['product_name'])) {
            $context['Product'] = $this->notification->data['product_name'];
        }
        
        if (isset($this->notification->data['amount'])) {
            $context['Amount'] = 'KES ' . number_format($this->notification->data['amount']);
        }
        
        return $context;
    }

    /**
     * Get recommendations based on notification type.
     */
    protected function getRecommendations(): array
    {
        return match($this->notification->type) {
            'inventory_out' => [
                'Contact suppliers immediately',
                'Update product availability status',
                'Consider alternative products',
                'Notify customers about restocking',
            ],
            'payment_failed' => [
                'Check payment gateway status',
                'Contact customer for alternative payment',
                'Review payment logs',
                'Monitor for fraud patterns',
            ],
            'security_alert' => [
                'Review security logs immediately',
                'Check for suspicious activities',
                'Consider blocking suspicious IPs',
                'Update security protocols',
            ],
            'system_error' => [
                'Check system logs',
                'Monitor application performance',
                'Verify database connectivity',
                'Contact technical support if needed',
            ],
            default => [
                'Review the notification details',
                'Take appropriate action',
                'Monitor related activities',
            ],
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSend(): bool
    {
        // Check if admin email notifications are enabled
        return config('app.admin_email_notifications', true);
    }
}
