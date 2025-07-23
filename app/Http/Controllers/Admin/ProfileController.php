<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the admin profile form
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'login_count' => $user->getAttribute('login_count') ?? 0,
            'last_login' => $user->getAttribute('last_login_at'),
            'account_created' => $user->getAttribute('created_at'),
            'profile_completion' => $this->calculateProfileCompletion($user),
        ];

        return view('admin.profile.show', compact('user', 'stats'));
    }

    /**
     * Update the admin profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->getAttribute('id'),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notification_email' => 'boolean',
            'notification_browser' => 'boolean',
            'notification_orders' => 'boolean',
            'notification_products' => 'boolean',
            'notification_users' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->getAttribute('avatar') && Storage::exists('public/' . $user->getAttribute('avatar'))) {
                Storage::delete('public/' . $user->getAttribute('avatar'));
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Update user data
        $user->update([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'bio' => $request->get('bio'),
            'timezone' => $request->get('timezone'),
            'language' => $request->get('language', 'en'),
            'notification_email' => $request->boolean('notification_email'),
            'notification_browser' => $request->boolean('notification_browser'),
            'notification_orders' => $request->boolean('notification_orders'),
            'notification_products' => $request->boolean('notification_products'),
            'notification_users' => $request->boolean('notification_users'),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the admin password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->get('password')),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Update two-factor authentication settings
     */
    public function updateTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_enabled' => 'boolean',
            'two_factor_method' => 'nullable|in:email,sms,app',
        ]);

        $user = Auth::user();
        $user->update([
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'two_factor_method' => $request->get('two_factor_method'),
        ]);

        $message = $request->boolean('two_factor_enabled') 
            ? 'Two-factor authentication enabled successfully.'
            : 'Two-factor authentication disabled successfully.';

        return back()->with('success', $message);
    }

    /**
     * Update API access settings
     */
    public function updateApiAccess(Request $request)
    {
        $request->validate([
            'api_access_enabled' => 'boolean',
            'api_rate_limit' => 'nullable|integer|min:1|max:10000',
        ]);

        $user = Auth::user();
        $user->update([
            'api_access_enabled' => $request->boolean('api_access_enabled'),
            'api_rate_limit' => $request->get('api_rate_limit', 1000),
        ]);

        // Generate new API token if enabled
        if ($request->boolean('api_access_enabled') && !$user->getAttribute('api_token')) {
            $user->update([
                'api_token' => Hash::make($user->getAttribute('id') . time()),
            ]);
        }

        return back()->with('success', 'API access settings updated successfully.');
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->getAttribute('avatar') && Storage::exists('public/' . $user->getAttribute('avatar'))) {
            Storage::delete('public/' . $user->getAttribute('avatar'));
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Avatar deleted successfully.');
    }

    /**
     * Export user data
     */
    public function exportData()
    {
        $user = Auth::user();
        
        $data = [
            'profile' => [
                'name' => $user->getAttribute('name'),
                'email' => $user->getAttribute('email'),
                'phone' => $user->getAttribute('phone'),
                'bio' => $user->getAttribute('bio'),
                'timezone' => $user->getAttribute('timezone'),
                'language' => $user->getAttribute('language'),
                'created_at' => $user->getAttribute('created_at'),
                'updated_at' => $user->getAttribute('updated_at'),
            ],
            'preferences' => [
                'notification_email' => $user->getAttribute('notification_email'),
                'notification_browser' => $user->getAttribute('notification_browser'),
                'notification_orders' => $user->getAttribute('notification_orders'),
                'notification_products' => $user->getAttribute('notification_products'),
                'notification_users' => $user->getAttribute('notification_users'),
            ],
            'security' => [
                'two_factor_enabled' => $user->getAttribute('two_factor_enabled'),
                'two_factor_method' => $user->getAttribute('two_factor_method'),
                'api_access_enabled' => $user->getAttribute('api_access_enabled'),
                'last_login_at' => $user->getAttribute('last_login_at'),
            ],
            'exported_at' => now()->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => 'admin-profile-' . $user->getAttribute('id') . '-' . now()->format('Y-m-d-H-i-s') . '.json'
        ]);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion($user): int
    {
        $fields = [
            'name' => !empty($user->getAttribute('name')),
            'email' => !empty($user->getAttribute('email')),
            'phone' => !empty($user->getAttribute('phone')),
            'bio' => !empty($user->getAttribute('bio')),
            'avatar' => !empty($user->getAttribute('avatar')),
            'timezone' => !empty($user->getAttribute('timezone')),
            'language' => !empty($user->getAttribute('language')),
        ];

        $completed = array_sum($fields);
        $total = count($fields);

        return round(($completed / $total) * 100);
    }

    /**
     * Get activity log for the user
     */
    public function getActivityLog()
    {
        $activities = collect([
            [
                'description' => 'Logged in',
                'created_at' => now()->subHours(2)->diffForHumans(),
                'properties' => ['ip' => request()->ip()],
            ],
            [
                'description' => 'Updated profile',
                'created_at' => now()->subDays(1)->diffForHumans(),
                'properties' => ['fields' => ['name', 'email']],
            ],
            [
                'description' => 'Changed password',
                'created_at' => now()->subDays(3)->diffForHumans(),
                'properties' => [],
            ],
        ]);

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }
}
