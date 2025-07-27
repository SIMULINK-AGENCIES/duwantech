@extends('admin.layouts.master')

@section('title', 'Theme Settings')

@section('content')
<div class="p-6 max-w-4xl mx-auto" x-data="themeSettings()">
    
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Theme Settings</h1>
                <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                    Customize the appearance and behavior of your admin dashboard
                </p>
            </div>
            
            {{-- Theme Preview Badge --}}
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium" style="color: var(--text-secondary);">Current Theme:</span>
                <div class="badge badge-primary" x-text="getCurrentThemeLabel()"></div>
            </div>
        </div>
    </div>

    {{-- Theme Configuration Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Theme Selection Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Theme Selection</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Choose your preferred color scheme</p>
            </div>
            <div class="card-body space-y-4">
                
                {{-- Light Theme Option --}}
                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors theme-transition hover:bg-gray-50 dark:hover:bg-gray-700"
                       style="border-color: var(--border-primary);"
                       :class="{ 'ring-2 ring-blue-500': themePreference === 'light' }">
                    <input type="radio" 
                           name="theme" 
                           value="light" 
                           x-model="themePreference"
                           @change="updateTheme('light')"
                           class="sr-only">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-12 h-12 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium" style="color: var(--text-primary);">Light Theme</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">Clean and bright interface</p>
                        </div>
                        <div x-show="themePreference === 'light'" class="text-blue-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>

                {{-- Dark Theme Option --}}
                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors theme-transition hover:bg-gray-50 dark:hover:bg-gray-700"
                       style="border-color: var(--border-primary);"
                       :class="{ 'ring-2 ring-blue-500': themePreference === 'dark' }">
                    <input type="radio" 
                           name="theme" 
                           value="dark" 
                           x-model="themePreference"
                           @change="updateTheme('dark')"
                           class="sr-only">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-12 h-12 rounded-lg bg-gray-900 border-2 border-gray-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium" style="color: var(--text-primary);">Dark Theme</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">Easy on the eyes, perfect for low light</p>
                        </div>
                        <div x-show="themePreference === 'dark'" class="text-blue-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>

                {{-- System Theme Option --}}
                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors theme-transition hover:bg-gray-50 dark:hover:bg-gray-700"
                       style="border-color: var(--border-primary);"
                       :class="{ 'ring-2 ring-blue-500': themePreference === 'system' }">
                    <input type="radio" 
                           name="theme" 
                           value="system" 
                           x-model="themePreference"
                           @change="updateTheme('system')"
                           class="sr-only">
                    <div class="flex items-center space-x-4 flex-1">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-gray-100 to-gray-800 border-2 border-gray-300 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium" style="color: var(--text-primary);">System Theme</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">Automatically matches your system preferences</p>
                        </div>
                        <div x-show="themePreference === 'system'" class="text-blue-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Theme Preview Card --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Theme Preview</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">See how your dashboard will look</p>
            </div>
            <div class="card-body">
                <div class="border rounded-lg overflow-hidden" style="border-color: var(--border-primary);">
                    {{-- Mini Dashboard Preview --}}
                    <div class="p-4 border-b" style="background-color: var(--bg-tertiary); border-color: var(--border-primary);">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded bg-blue-500 flex items-center justify-center">
                                    <div class="w-3 h-3 bg-white rounded-sm"></div>
                                </div>
                                <span class="text-sm font-medium" style="color: var(--text-primary);">Admin Panel</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 rounded-full" style="background-color: var(--color-success-500);"></div>
                                <div class="w-4 h-4 rounded-full" style="background-color: var(--color-warning-500);"></div>
                                <div class="w-4 h-4 rounded-full" style="background-color: var(--color-error-500);"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex">
                        {{-- Mini Sidebar --}}
                        <div class="w-16 p-2 space-y-2" style="background-color: var(--bg-secondary); border-right: 1px solid var(--border-primary);">
                            <div class="w-8 h-8 rounded-lg" style="background-color: var(--color-primary-100);"></div>
                            <div class="w-8 h-8 rounded-lg" style="background-color: var(--bg-tertiary);"></div>
                            <div class="w-8 h-8 rounded-lg" style="background-color: var(--bg-tertiary);"></div>
                        </div>
                        
                        {{-- Mini Content --}}
                        <div class="flex-1 p-4 space-y-3" style="background-color: var(--bg-primary);">
                            <div class="flex items-center justify-between">
                                <div class="h-4 w-24 rounded" style="background-color: var(--text-primary); opacity: 0.8;"></div>
                                <div class="h-4 w-16 rounded" style="background-color: var(--color-primary-200);"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 w-full rounded" style="background-color: var(--text-secondary); opacity: 0.4;"></div>
                                <div class="h-3 w-3/4 rounded" style="background-color: var(--text-secondary); opacity: 0.4;"></div>
                                <div class="h-3 w-1/2 rounded" style="background-color: var(--text-secondary); opacity: 0.4;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Advanced Settings Card --}}
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Advanced Theme Settings</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">Fine-tune your theme experience</p>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Animation Settings --}}
                    <div class="space-y-4">
                        <h4 class="font-medium" style="color: var(--text-primary);">Animation Preferences</h4>
                        
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="settings.reducedMotion"
                                   @change="updateSettings()"
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm" style="color: var(--text-secondary);">Reduce motion effects</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="settings.smoothScrolling"
                                   @change="updateSettings()"
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm" style="color: var(--text-secondary);">Enable smooth scrolling</span>
                        </label>
                    </div>

                    {{-- Display Settings --}}
                    <div class="space-y-4">
                        <h4 class="font-medium" style="color: var(--text-primary);">Display Options</h4>
                        
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="settings.highContrast"
                                   @change="updateSettings()"
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm" style="color: var(--text-secondary);">High contrast mode</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="settings.largeText"
                                   @change="updateSettings()"
                                   class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm" style="color: var(--text-secondary);">Large text size</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reset Settings Button --}}
    <div class="mt-8 flex justify-center">
        <button @click="resetToDefaults()" 
                class="btn btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reset to Defaults
        </button>
    </div>
</div>

<script>
function themeSettings() {
    return {
        themePreference: 'system',
        settings: {
            reducedMotion: false,
            smoothScrolling: true,
            highContrast: false,
            largeText: false
        },

        init() {
            // Load current theme preference
            this.themePreference = localStorage.getItem('theme') || 'system';
            
            // Load advanced settings
            const savedSettings = localStorage.getItem('themeAdvancedSettings');
            if (savedSettings) {
                this.settings = { ...this.settings, ...JSON.parse(savedSettings) };
            }
            
            // Apply settings
            this.applyAdvancedSettings();
        },

        updateTheme(theme) {
            this.themePreference = theme;
            localStorage.setItem('theme', theme);
            
            // Trigger theme change
            window.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { theme: theme } 
            }));
            
            // Apply theme immediately
            this.applyTheme(theme);
        },

        applyTheme(theme) {
            const systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const resolvedTheme = theme === 'system' ? systemPreference : theme;
            
            document.documentElement.classList.remove('light', 'dark');
            document.documentElement.classList.add(resolvedTheme);
            document.documentElement.setAttribute('data-theme', resolvedTheme);
        },

        updateSettings() {
            localStorage.setItem('themeAdvancedSettings', JSON.stringify(this.settings));
            this.applyAdvancedSettings();
        },

        applyAdvancedSettings() {
            const html = document.documentElement;
            
            // Reduced motion
            if (this.settings.reducedMotion) {
                html.style.setProperty('--duration-150', '0ms');
                html.style.setProperty('--duration-200', '0ms');
                html.style.setProperty('--duration-300', '0ms');
            } else {
                html.style.removeProperty('--duration-150');
                html.style.removeProperty('--duration-200');
                html.style.removeProperty('--duration-300');
            }
            
            // Smooth scrolling
            html.style.scrollBehavior = this.settings.smoothScrolling ? 'smooth' : 'auto';
            
            // High contrast
            html.classList.toggle('high-contrast', this.settings.highContrast);
            
            // Large text
            html.classList.toggle('large-text', this.settings.largeText);
        },

        getCurrentThemeLabel() {
            const labels = {
                light: 'Light',
                dark: 'Dark',
                system: 'System'
            };
            return labels[this.themePreference] || 'System';
        },

        resetToDefaults() {
            if (confirm('Are you sure you want to reset all theme settings to defaults?')) {
                this.themePreference = 'system';
                this.settings = {
                    reducedMotion: false,
                    smoothScrolling: true,
                    highContrast: false,
                    largeText: false
                };
                
                localStorage.removeItem('theme');
                localStorage.removeItem('themeAdvancedSettings');
                
                this.updateTheme('system');
                this.applyAdvancedSettings();
            }
        }
    }
}
</script>
@endsection
