@extends('admin.layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-2">Manage your account settings and preferences</p>
        </div>

        <!-- Profile Completion Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Profile Completion</h3>
                    <p class="text-gray-600">{{ $stats['profile_completion'] }}% complete</p>
                </div>
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#e5e7eb"
                            stroke-width="2"
                        />
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#3b82f6"
                            stroke-width="2"
                            stroke-dasharray="{{ $stats['profile_completion'] }}, 100"
                        />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-semibold text-gray-900">{{ $stats['profile_completion'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" aria-label="Tabs">
                    <button onclick="showTab('profile')" id="profile-tab" class="tab-button border-b-2 border-blue-500 text-blue-600 py-4 px-6 text-sm font-medium">
                        Personal Information
                    </button>
                    <button onclick="showTab('security')" id="security-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-6 text-sm font-medium">
                        Security & Privacy
                    </button>
                    <button onclick="showTab('notifications')" id="notifications-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-6 text-sm font-medium">
                        Notifications
                    </button>
                    <button onclick="showTab('api')" id="api-tab" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-6 text-sm font-medium">
                        API Access
                    </button>
                </nav>
            </div>

            <!-- Personal Information Tab -->
            <div id="profile-content" class="tab-content p-6">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Avatar Section -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Profile Photo</label>
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    @if($user->avatar)
                                        <img class="h-20 w-20 object-cover rounded-full border-2 border-gray-300" 
                                             src="{{ Storage::url($user->avatar) }}" 
                                             alt="{{ $user->name }}">
                                    @else
                                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden">
                                    <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Change Photo
                                    </label>
                                    @if($user->avatar)
                                        <button type="button" onclick="deleteAvatar()" class="ml-3 inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                            Remove
                                        </button>
                                    @endif
                                    <p class="mt-2 text-xs text-gray-500">JPG, PNG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                            <select name="timezone" id="timezone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ old('timezone', $user->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ old('timezone', $user->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                <option value="Europe/London" {{ old('timezone', $user->timezone) == 'Europe/London' ? 'selected' : '' }}>London</option>
                                <option value="Africa/Nairobi" {{ old('timezone', $user->timezone) == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi</option>
                            </select>
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                            <select name="language" id="language" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="en" {{ old('language', $user->language) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ old('language', $user->language) == 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ old('language', $user->language) == 'fr' ? 'selected' : '' }}>French</option>
                                <option value="sw" {{ old('language', $user->language) == 'sw' ? 'selected' : '' }}>Swahili</option>
                            </select>
                        </div>

                        <!-- Bio -->
                        <div class="lg:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea name="bio" id="bio" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Tab -->
            <div id="security-content" class="tab-content hidden p-6">
                <div class="space-y-8">
                    <!-- Change Password -->
                    <div class="border-b border-gray-200 pb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                <input type="password" name="current_password" id="current_password" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Update Password
                            </button>
                        </form>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Two-Factor Authentication</h3>
                        <form action="{{ route('admin.profile.two-factor') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" 
                                           value="1" {{ $user->two_factor_enabled ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="two_factor_enabled" class="ml-2 block text-sm text-gray-900">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>

                                <div id="two-factor-method" class="{{ $user->two_factor_enabled ? '' : 'hidden' }}">
                                    <label for="two_factor_method_select" class="block text-sm font-medium text-gray-700">Method</label>
                                    <select name="two_factor_method" id="two_factor_method_select" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="email" {{ $user->two_factor_method == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="sms" {{ $user->two_factor_method == 'sms' ? 'selected' : '' }}>SMS</option>
                                        <option value="app" {{ $user->two_factor_method == 'app' ? 'selected' : '' }}>Authenticator App</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Update Security Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-content" class="tab-content hidden p-6">
                <form action="{{ route('admin.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Email Notifications</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="notification_email" class="text-sm font-medium text-gray-900">Email Notifications</label>
                                        <p class="text-sm text-gray-500">Receive email notifications for important updates</p>
                                    </div>
                                    <input type="checkbox" name="notification_email" id="notification_email" 
                                           value="1" {{ $user->notification_email ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="notification_browser" class="text-sm font-medium text-gray-900">Browser Notifications</label>
                                        <p class="text-sm text-gray-500">Receive push notifications in your browser</p>
                                    </div>
                                    <input type="checkbox" name="notification_browser" id="notification_browser" 
                                           value="1" {{ $user->notification_browser ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Notifications</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="notification_orders" class="text-sm font-medium text-gray-900">Order Updates</label>
                                        <p class="text-sm text-gray-500">Get notified when orders are placed or updated</p>
                                    </div>
                                    <input type="checkbox" name="notification_orders" id="notification_orders" 
                                           value="1" {{ $user->notification_orders ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="notification_products" class="text-sm font-medium text-gray-900">Product Updates</label>
                                        <p class="text-sm text-gray-500">Get notified about product changes and inventory</p>
                                    </div>
                                    <input type="checkbox" name="notification_products" id="notification_products" 
                                           value="1" {{ $user->notification_products ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="notification_users" class="text-sm font-medium text-gray-900">User Activity</label>
                                        <p class="text-sm text-gray-500">Get notified about new user registrations</p>
                                    </div>
                                    <input type="checkbox" name="notification_users" id="notification_users" 
                                           value="1" {{ $user->notification_users ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                            Update Notifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- API Access Tab -->
            <div id="api-content" class="tab-content hidden p-6">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">API Configuration</h3>
                        <form action="{{ route('admin.profile.api') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="api_access_enabled" id="api_access_enabled" 
                                           value="1" {{ $user->api_access_enabled ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="api_access_enabled" class="ml-2 block text-sm text-gray-900">
                                        Enable API Access
                                    </label>
                                </div>

                                <div id="api-settings" class="{{ $user->api_access_enabled ? '' : 'hidden' }}">
                                    <div>
                                        <label for="api_rate_limit" class="block text-sm font-medium text-gray-700">Rate Limit (requests per hour)</label>
                                        <input type="number" name="api_rate_limit" id="api_rate_limit" 
                                               value="{{ old('api_rate_limit', $user->api_rate_limit ?? 1000) }}"
                                               min="1" max="10000"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    @if($user->api_token)
                                        <div class="mt-4 p-4 bg-gray-50 rounded-md">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">API Token</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="text" value="{{ substr($user->api_token, 0, 20) }}..." 
                                                       readonly class="flex-1 border-gray-300 rounded-md shadow-sm bg-gray-100">
                                                <button type="button" onclick="copyToken()" class="px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
                                                    Copy
                                                </button>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500">Keep this token secure. It won't be shown again.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Update API Settings
                            </button>
                        </form>
                    </div>

                    <!-- Export Data -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Export</h3>
                        <p class="text-gray-600 mb-4">Download your profile data and settings</p>
                        <button onclick="exportData()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $stats['login_count'] }}</h3>
                        <p class="text-gray-600">Total Logins</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $stats['last_login'] ? $stats['last_login']->diffForHumans() : 'Never' }}
                        </h3>
                        <p class="text-gray-600">Last Login</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $stats['account_created']->diffForHumans() }}
                        </h3>
                        <p class="text-gray-600">Member Since</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Reset all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Activate selected tab button
    document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(tabName + '-tab').classList.add('border-blue-500', 'text-blue-600');
}

// Two-factor authentication toggle
document.getElementById('two_factor_enabled').addEventListener('change', function() {
    const methodDiv = document.getElementById('two-factor-method');
    if (this.checked) {
        methodDiv.classList.remove('hidden');
    } else {
        methodDiv.classList.add('hidden');
    }
});

// API access toggle
document.getElementById('api_access_enabled').addEventListener('change', function() {
    const settingsDiv = document.getElementById('api-settings');
    if (this.checked) {
        settingsDiv.classList.remove('hidden');
    } else {
        settingsDiv.classList.add('hidden');
    }
});

// Delete avatar
function deleteAvatar() {
    if (confirm('Are you sure you want to remove your profile photo?')) {
        fetch('{{ route("admin.profile.avatar.delete") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Copy API token
function copyToken() {
    const token = '{{ $user->api_token }}';
    navigator.clipboard.writeText(token).then(() => {
        alert('API token copied to clipboard');
    });
}

// Export data
function exportData() {
    fetch('{{ route("admin.profile.export") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = data.filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
    });
}

// Avatar preview
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('img[alt="{{ $user->name }}"]');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
