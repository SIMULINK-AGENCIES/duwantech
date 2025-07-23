<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Display the activity feed page
     */
    public function index(Request $request)
    {
        $activities = $this->getActivityQuery($request)
            ->with(['user'])
            ->latest()
            ->paginate(20);

        $stats = $this->getActivityStats();
        $activityTypes = $this->getActivityTypes();
        
        if ($request->ajax()) {
            return response()->json([
                'activities' => $activities,
                'stats' => $stats,
                'html' => view('admin.components.activity-feed-items', compact('activities'))->render()
            ]);
        }

        return view('admin.activity.index', compact('activities', 'stats', 'activityTypes'));
    }
    
    /**
     * Get live activities for real-time feed
     */
    public function getLiveActivities(Request $request)
    {
        try {
            $lastActivityId = $request->get('last_id', 0);
            
            // Get new activities since last check
            $activities = ActivityLog::with(['user'])
                ->where('id', '>', $lastActivityId)
                ->latest()
                ->take(10)
                ->get();

            // Get updated stats
            $stats = $this->getActivityStats();
            
            return response()->json([
                'success' => true,
                'activities' => $activities->map(function ($activity) {
                    return $this->formatActivity($activity);
                }),
                'stats' => $stats,
                'last_id' => $activities->first()?->id ?? $lastActivityId,
                'count' => $activities->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to get live activities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch live activities'
            ], 500);
        }
    }
    
    /**
     * Get activity statistics for the dashboard
     */
    public function getActivityStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today' => [
                'total' => ActivityLog::whereDate('created_at', $today)->count(),
                'logins' => ActivityLog::whereDate('created_at', $today)->where('action', 'login')->count(),
                'orders' => ActivityLog::whereDate('created_at', $today)->where('action', 'order_created')->count(),
                'payments' => ActivityLog::whereDate('created_at', $today)->where('action', 'payment_completed')->count(),
            ],
            'this_week' => [
                'total' => ActivityLog::where('created_at', '>=', $thisWeek)->count(),
                'avg_per_day' => round(ActivityLog::where('created_at', '>=', $thisWeek)->count() / 7, 1),
            ],
            'this_month' => [
                'total' => ActivityLog::where('created_at', '>=', $thisMonth)->count(),
                'avg_per_day' => round(ActivityLog::where('created_at', '>=', $thisMonth)->count() / $thisMonth->diffInDays(now()), 1),
            ],
            'top_actions' => ActivityLog::select('action', DB::raw('count(*) as count'))
                ->whereDate('created_at', '>=', $today->subDays(7))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->take(5)
                ->get(),
            'hourly_activity' => ActivityLog::select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('count(*) as count')
                )
                ->whereDate('created_at', $today)
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->pluck('count', 'hour')
                ->toArray()
        ];
    }
    
    /**
     * Get filtered activity query based on request parameters
     */
    private function getActivityQuery(Request $request)
    {
        $query = ActivityLog::query();
        
        // Filter by action type
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by priority if specified
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('metadata->priority', $request->priority);
        }
        
        return $query;
    }
    
    /**
     * Get available activity types for filtering
     */
    private function getActivityTypes()
    {
        return [
            'all' => 'All Activities',
            'login' => 'User Logins',
            'logout' => 'User Logouts',
            'registration' => 'New Registrations',
            'order_created' => 'Orders Created',
            'order_updated' => 'Orders Updated',
            'order_cancelled' => 'Orders Cancelled',
            'payment_initiated' => 'Payments Initiated',
            'payment_completed' => 'Payments Completed',
            'payment_failed' => 'Payment Failures',
            'product_viewed' => 'Products Viewed',
            'cart_updated' => 'Cart Updates',
            'wishlist_updated' => 'Wishlist Updates',
            'profile_updated' => 'Profile Updates',
            'password_changed' => 'Password Changes',
            'email_verified' => 'Email Verifications',
            'system_error' => 'System Errors',
            'admin_action' => 'Admin Actions'
        ];
    }
    
    /**
     * Format activity for API response
     */
    private function formatActivity(ActivityLog $activity)
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
     * Get icon for activity type
     */
    private function getActivityIcon($action)
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
    private function getActivityColor($action)
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
    
    /**
     * Log a new activity
     */
    public static function logActivity($action, $description, $userId = null, $metadata = [])
    {
        try {
            $activity = ActivityLog::create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'description' => $description,
                'metadata' => array_merge($metadata, [
                    'timestamp' => now()->toISOString(),
                    'session_id' => session()->getId()
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            
            // Broadcast the new activity to admin users
            broadcast(new \App\Events\NewActivityEvent($activity))->toOthers();
            
            return $activity;
            
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage(), [
                'action' => $action,
                'description' => $description,
                'user_id' => $userId,
                'metadata' => $metadata
            ]);
        }
    }
}
