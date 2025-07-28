<x-admin.layouts.master title="Frontend Settings">
    <x-slot name="breadcrumbs">
        @php
            $breadcrumbs = [
                ['title' => 'Settings', 'url' => route('admin.settings.index')],
                ['title' => 'Frontend Settings', 'url' => route('admin.settings.general')]
            ];
        @endphp
    </x-slot>

    <!-- Frontend Settings Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Frontend Settings</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Configure your site's frontend appearance and settings.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.frontend.public') }}" target="_blank" 
               class="btn btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M13 7h8m0 0V2m0 5l-8-8"></path>
                </svg>
                View Public Settings API
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.frontend.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Site Information Tab -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button type="button" class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="site">
                        Site Information
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="appearance">
                        Appearance
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="contact">
                        Contact
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="social">
                        Social Media
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="features">
                        Features
                    </button>
                    <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" data-tab="analytics">
                        Analytics
                    </button>
                </nav>
            </div>

            <!-- Site Information Tab Content -->
            <div id="site-tab" class="tab-content p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name *</label>
                        <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                        <textarea name="site_description" id="site_description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                        @error('site_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="site_keywords" class="block text-sm font-medium text-gray-700">SEO Keywords</label>
                        <input type="text" name="site_keywords" id="site_keywords" value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}"
                               placeholder="ecommerce, shopping, products"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Comma-separated keywords for SEO</p>
                        @error('site_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700">Currency *</label>
                        <input type="text" name="currency" id="currency" value="{{ old('currency', $settings['currency'] ?? 'KES') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700">Currency Symbol *</label>
                        <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'KSh') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('currency_symbol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Appearance Tab Content -->
            <div id="appearance-tab" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="logo_url" class="block text-sm font-medium text-gray-700">Logo URL</label>
                        <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url', $settings['logo_url'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://example.com/logo.png">
                        @error('logo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Primary Color</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#3B82F6') }}"
                                   class="h-10 w-20 border border-gray-300 rounded-md">
                            <input type="text" value="{{ old('primary_color', $settings['primary_color'] ?? '#3B82F6') }}"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   readonly>
                        </div>
                        @error('primary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700">Secondary Color</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#64748B') }}"
                                   class="h-10 w-20 border border-gray-300 rounded-md">
                            <input type="text" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#64748B') }}"
                                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   readonly>
                        </div>
                        @error('secondary_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Tab Content -->
            <div id="contact-tab" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                        <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="contact_address" class="block text-sm font-medium text-gray-700">Contact Address</label>
                        <textarea name="contact_address" id="contact_address" rows="2"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                        @error('contact_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="footer_text" class="block text-sm font-medium text-gray-700">Footer Text</label>
                        <textarea name="footer_text" id="footer_text" rows="2" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('footer_text', $settings['footer_text'] ?? '') }}</textarea>
                        @error('footer_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Social Media Tab Content -->
            <div id="social-tab" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                        <input type="url" name="social_facebook" id="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('social_facebook')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="social_twitter" class="block text-sm font-medium text-gray-700">Twitter URL</label>
                        <input type="url" name="social_twitter" id="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('social_twitter')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                        <input type="url" name="social_instagram" id="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('social_instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn URL</label>
                        <input type="url" name="social_linkedin" id="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('social_linkedin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Features Tab Content -->
            <div id="features-tab" class="tab-content p-6 hidden">
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" 
                               {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="ml-2 block text-sm text-gray-900">
                            Enable Maintenance Mode
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="allow_registration" id="allow_registration" value="1" 
                               {{ old('allow_registration', $settings['allow_registration'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="allow_registration" class="ml-2 block text-sm text-gray-900">
                            Allow New User Registration
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enable_reviews" id="enable_reviews" value="1" 
                               {{ old('enable_reviews', $settings['enable_reviews'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="enable_reviews" class="ml-2 block text-sm text-gray-900">
                            Enable Product Reviews
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enable_wishlist" id="enable_wishlist" value="1" 
                               {{ old('enable_wishlist', $settings['enable_wishlist'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="enable_wishlist" class="ml-2 block text-sm text-gray-900">
                            Enable Customer Wishlist
                        </label>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab Content -->
            <div id="analytics-tab" class="tab-content p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="google_analytics_id" class="block text-sm font-medium text-gray-700">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                               placeholder="GA-XXXXXXXXX-X"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('google_analytics_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="facebook_pixel_id" class="block text-sm font-medium text-gray-700">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" id="facebook_pixel_id" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('facebook_pixel_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="tawk_to_widget_id" class="block text-sm font-medium text-gray-700">Tawk.to Widget ID</label>
                        <input type="text" name="tawk_to_widget_id" id="tawk_to_widget_id" value="{{ old('tawk_to_widget_id', $settings['tawk_to_widget_id'] ?? '') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('tawk_to_widget_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-blue-500', 'text-blue-600');
                
                // Update content visibility
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(tabName + '-tab').classList.remove('hidden');
            });
        });

        // Color picker sync
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            colorInput.addEventListener('change', function() {
                const textInput = this.parentElement.querySelector('input[type="text"]');
                textInput.value = this.value;
            });
        });
    });
    </script>
    @endpush
</x-admin.layouts.master>