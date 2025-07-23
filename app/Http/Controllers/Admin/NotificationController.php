<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notification center
     */
    public function index()
    {
        $notifications = AdminNotification::with('user')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => AdminNotification::count(),
            'unread' => AdminNotification::where('is_read', false)->count(),
            'today' => AdminNotification::whereDate('created_at', today())->count(),
            'urgent' => AdminNotification::where('priority', 'high')->where('is_read', false)->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get notifications for the bell dropdown
     */
    public function dropdown(): JsonResponse
    {
        $notifications = AdminNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'icon' => $notification->getIcon(),
                    'color' => $notification->getColorClass(),
                ];
            });

        $unreadCount = AdminNotification::where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'has_urgent' => AdminNotification::where('priority', 'high')
                ->where('is_read', false)
                ->exists(),
        ]);
    }

    /**
     * Get unread notification count
     */
    public function count(): JsonResponse
    {
        $count = AdminNotification::where('is_read', false)->count();
        $hasUrgent = AdminNotification::where('priority', 'high')
            ->where('is_read', false)
            ->exists();

        return response()->json([
            'count' => $count,
            'has_urgent' => $hasUrgent,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(AdminNotification $notification): JsonResponse
    {
        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(AdminNotification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:admin_notifications,id',
        ]);

        $notificationIds = $request->input('notification_ids');
        AdminNotification::whereIn('id', $notificationIds)->delete();

        return response()->json([
            'success' => true,
            'message' => count($notificationIds) . ' notifications deleted successfully',
        ]);
    }

    /**
     * Create a test notification (for development)
     */
    public function createTest(): JsonResponse
    {
        $types = ['info', 'warning', 'success', 'error'];
        $priorities = ['low', 'medium', 'high'];
        
        $testMessages = [
            'info' => [
                'title' => 'System Information',
                'message' => 'Database backup completed successfully.',
            ],
            'warning' => [
                'title' => 'Low Stock Alert',
                'message' => 'Product "iPhone 13" has only 2 items remaining in stock.',
            ],
            'success' => [
                'title' => 'Payment Received',
                'message' => 'New payment of KSH 25,000 has been received.',
            ],
            'error' => [
                'title' => 'System Error',
                'message' => 'Failed to process payment. Please check payment gateway configuration.',
            ],
        ];

        $type = $types[array_rand($types)];
        $priority = $priorities[array_rand($priorities)];
        $data = $testMessages[$type];

        $notification = $this->notificationService->createNotification(
            $data['title'],
            $data['message'],
            $type,
            $priority,
            null,
            ['test' => true, 'created_by' => 'system']
        );

        return response()->json([
            'success' => true,
            'message' => 'Test notification created',
            'notification' => $notification,
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'email_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'sound_enabled' => 'boolean',
            'desktop_enabled' => 'boolean',
            'frequency' => 'in:real_time,hourly,daily',
            'types' => 'array',
            'types.*' => 'in:info,warning,success,error',
        ]);

        $preferences = auth()->user()->notificationPreferences()->firstOrCreate([]);
        
        $preferences->update([
            'email_enabled' => $request->boolean('email_enabled'),
            'push_enabled' => $request->boolean('push_enabled'),
            'sound_enabled' => $request->boolean('sound_enabled'),
            'desktop_enabled' => $request->boolean('desktop_enabled'),
            'frequency' => $request->input('frequency', 'real_time'),
            'notification_types' => $request->input('types', ['info', 'warning', 'success', 'error']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
        ]);
    }
}
