@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Site Settings</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- General Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">General Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ $settings['contact_phone'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                    <textarea name="site_description" id="site_description" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $settings['site_description'] }}</textarea>
                </div>
            </div>
        </div>

        <!-- M-PESA Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">M-PESA Configuration</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="mpesa_consumer_key" class="block text-sm font-medium text-gray-700">Consumer Key</label>
                    <input type="text" name="mpesa_consumer_key" id="mpesa_consumer_key" value="{{ $settings['mpesa_consumer_key'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="mpesa_consumer_secret" class="block text-sm font-medium text-gray-700">Consumer Secret</label>
                    <input type="password" name="mpesa_consumer_secret" id="mpesa_consumer_secret" value="{{ $settings['mpesa_consumer_secret'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="mpesa_passkey" class="block text-sm font-medium text-gray-700">Passkey</label>
                    <input type="password" name="mpesa_passkey" id="mpesa_passkey" value="{{ $settings['mpesa_passkey'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="mpesa_shortcode" class="block text-sm font-medium text-gray-700">Shortcode</label>
                    <input type="text" name="mpesa_shortcode" id="mpesa_shortcode" value="{{ $settings['mpesa_shortcode'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Third Party Integrations -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Third Party Integrations</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tawk_to_widget_id" class="block text-sm font-medium text-gray-700">Tawk.to Widget ID</label>
                    <input type="text" name="tawk_to_widget_id" id="tawk_to_widget_id" value="{{ $settings['tawk_to_widget_id'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">For live chat integration</p>
                </div>
                
                <div>
                    <label for="google_analytics_id" class="block text-sm font-medium text-gray-700">Google Analytics ID</label>
                    <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ $settings['google_analytics_id'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">e.g., G-XXXXXXXXXX</p>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Social Media Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                    <input type="url" name="social_facebook" id="social_facebook" value="{{ $settings['social_facebook'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                    <input type="url" name="social_instagram" id="social_instagram" value="{{ $settings['social_instagram'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter/X URL</label>
                    <input type="url" name="social_twitter" id="social_twitter" value="{{ $settings['social_twitter'] }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection 