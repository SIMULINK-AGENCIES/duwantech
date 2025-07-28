@extends('admin.layouts.master')

@section('title', 'Theme Configuration - Task 2.4.3')

@section('content')
<div class="p-6 max-w-6xl mx-auto" x-data="themeConfiguration()">
    
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Theme Configuration</h1>
                <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                    Customize your dashboard appearance with multiple themes, color schemes, and font preferences
                </p>
            </div>
            
            {{-- Theme Preview Badge --}}
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium" style="color: var(--text-secondary);">Active Theme:</span>
                <div class="badge badge-primary" x-text="getThemeLabel()"></div>
            </div>
        </div>
    </div>

    {{-- Configuration Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Theme Selection Panel --}}
        <div class="xl:col-span-2 space-y-6">
            
            {{-- Basic Theme Selection --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Theme Mode</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Choose your preferred color scheme</p>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Light Theme --}}
                        <label class="theme-option" 
                               :class="{ 'selected': themeMode === 'light' }">
                            <input type="radio" 
                                   name="themeMode" 
                                   value="light" 
                                   x-model="themeMode"
                                   @change="updateThemeMode('light')"
                                   class="sr-only">
                            <div class="theme-preview light-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-main">
                                        <div class="preview-item"></div>
                                        <div class="preview-item"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info mt-3">
                                <h4 class="font-medium" style="color: var(--text-primary);">Light Mode</h4>
                                <p class="text-sm" style="color: var(--text-secondary);">Clean and bright interface</p>
                            </div>
                        </label>

                        {{-- Dark Theme --}}
                        <label class="theme-option" 
                               :class="{ 'selected': themeMode === 'dark' }">
                            <input type="radio" 
                                   name="themeMode" 
                                   value="dark" 
                                   x-model="themeMode"
                                   @change="updateThemeMode('dark')"
                                   class="sr-only">
                            <div class="theme-preview dark-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-sidebar"></div>
                                    <div class="preview-main">
                                        <div class="preview-item"></div>
                                        <div class="preview-item"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info mt-3">
                                <h4 class="font-medium" style="color: var(--text-primary);">Dark Mode</h4>
                                <p class="text-sm" style="color: var(--text-secondary);">Easy on the eyes</p>
                            </div>
                        </label>

                        {{-- System Theme --}}
                        <label class="theme-option" 
                               :class="{ 'selected': themeMode === 'system' }">
                            <input type="radio" 
                                   name="themeMode" 
                                   value="system" 
                                   x-model="themeMode"
                                   @change="updateThemeMode('system')"
                                   class="sr-only">
                            <div class="theme-preview system-preview">
                                <div class="preview-header"></div>
                                <div class="preview-content">
                                    <div class="preview-sidebar gradient-sidebar"></div>
                                    <div class="preview-main">
                                        <div class="preview-item"></div>
                                        <div class="preview-item"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-info mt-3">
                                <h4 class="font-medium" style="color: var(--text-primary);">System Default</h4>
                                <p class="text-sm" style="color: var(--text-secondary);">Follows device setting</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Color Scheme Selection --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Color Scheme</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Choose your preferred color palette</p>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        
                        {{-- Default Blue --}}
                        <label class="color-scheme-option" 
                               :class="{ 'selected': colorScheme === 'default' }">
                            <input type="radio" 
                                   name="colorScheme" 
                                   value="default" 
                                   x-model="colorScheme"
                                   @change="updateColorScheme('default')"
                                   class="sr-only">
                            <div class="color-preview">
                                <div class="color-circle bg-blue-500"></div>
                                <div class="color-circle bg-blue-400"></div>
                                <div class="color-circle bg-blue-300"></div>
                            </div>
                            <span class="color-name">Default</span>
                        </label>

                        {{-- Blue Variant --}}
                        <label class="color-scheme-option" 
                               :class="{ 'selected': colorScheme === 'blue' }">
                            <input type="radio" 
                                   name="colorScheme" 
                                   value="blue" 
                                   x-model="colorScheme"
                                   @change="updateColorScheme('blue')"
                                   class="sr-only">
                            <div class="color-preview">
                                <div class="color-circle bg-blue-700"></div>
                                <div class="color-circle bg-blue-600"></div>
                                <div class="color-circle bg-blue-500"></div>
                            </div>
                            <span class="color-name">Ocean</span>
                        </label>

                        {{-- Green Variant --}}
                        <label class="color-scheme-option" 
                               :class="{ 'selected': colorScheme === 'green' }">
                            <input type="radio" 
                                   name="colorScheme" 
                                   value="green" 
                                   x-model="colorScheme"
                                   @change="updateColorScheme('green')"
                                   class="sr-only">
                            <div class="color-preview">
                                <div class="color-circle bg-emerald-600"></div>
                                <div class="color-circle bg-emerald-500"></div>
                                <div class="color-circle bg-emerald-400"></div>
                            </div>
                            <span class="color-name">Forest</span>
                        </label>

                        {{-- Purple Variant --}}
                        <label class="color-scheme-option" 
                               :class="{ 'selected': colorScheme === 'purple' }">
                            <input type="radio" 
                                   name="colorScheme" 
                                   value="purple" 
                                   x-model="colorScheme"
                                   @change="updateColorScheme('purple')"
                                   class="sr-only">
                            <div class="color-preview">
                                <div class="color-circle bg-purple-600"></div>
                                <div class="color-circle bg-purple-500"></div>
                                <div class="color-circle bg-purple-400"></div>
                            </div>
                            <span class="color-name">Royal</span>
                        </label>

                        {{-- Orange Variant --}}
                        <label class="color-scheme-option" 
                               :class="{ 'selected': colorScheme === 'orange' }">
                            <input type="radio" 
                                   name="colorScheme" 
                                   value="orange" 
                                   x-model="colorScheme"
                                   @change="updateColorScheme('orange')"
                                   class="sr-only">
                            <div class="color-preview">
                                <div class="color-circle bg-orange-600"></div>
                                <div class="color-circle bg-orange-500"></div>
                                <div class="color-circle bg-orange-400"></div>
                            </div>
                            <span class="color-name">Sunset</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Font Size Preferences --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Typography</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Adjust text size and readability</p>
                </div>
                <div class="card-body space-y-6">
                    
                    {{-- Font Size Selection --}}
                    <div>
                        <label class="block text-sm font-medium mb-3" style="color: var(--text-primary);">
                            Font Size: <span x-text="getFontSizeLabel()" class="font-normal" style="color: var(--text-secondary);"></span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            
                            <label class="font-size-option" 
                                   :class="{ 'selected': fontSize === 'compact' }">
                                <input type="radio" 
                                       name="fontSize" 
                                       value="compact" 
                                       x-model="fontSize"
                                       @change="updateFontSize('compact')"
                                       class="sr-only">
                                <div class="font-preview text-sm">
                                    <span>Aa</span>
                                    <span class="preview-text">Sample text</span>
                                </div>
                                <span class="font-label">Compact</span>
                            </label>

                            <label class="font-size-option" 
                                   :class="{ 'selected': fontSize === 'normal' }">
                                <input type="radio" 
                                       name="fontSize" 
                                       value="normal" 
                                       x-model="fontSize"
                                       @change="updateFontSize('normal')"
                                       class="sr-only">
                                <div class="font-preview text-base">
                                    <span>Aa</span>
                                    <span class="preview-text">Sample text</span>
                                </div>
                                <span class="font-label">Normal</span>
                            </label>

                            <label class="font-size-option" 
                                   :class="{ 'selected': fontSize === 'comfortable' }">
                                <input type="radio" 
                                       name="fontSize" 
                                       value="comfortable" 
                                       x-model="fontSize"
                                       @change="updateFontSize('comfortable')"
                                       class="sr-only">
                                <div class="font-preview text-lg">
                                    <span>Aa</span>
                                    <span class="preview-text">Sample text</span>
                                </div>
                                <span class="font-label">Comfortable</span>
                            </label>

                            <label class="font-size-option" 
                                   :class="{ 'selected': fontSize === 'large' }">
                                <input type="radio" 
                                       name="fontSize" 
                                       value="large" 
                                       x-model="fontSize"
                                       @change="updateFontSize('large')"
                                       class="sr-only">
                                <div class="font-preview text-xl">
                                    <span>Aa</span>
                                    <span class="preview-text">Sample text</span>
                                </div>
                                <span class="font-label">Large</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Live Preview Panel --}}
        <div class="space-y-6">
            
            {{-- Current Settings Summary --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Current Settings</h3>
                </div>
                <div class="card-body space-y-3">
                    <div class="setting-item">
                        <span class="setting-label">Theme Mode:</span>
                        <span class="setting-value" x-text="themeMode"></span>
                    </div>
                    <div class="setting-item">
                        <span class="setting-label">Color Scheme:</span>
                        <span class="setting-value" x-text="colorScheme"></span>
                    </div>
                    <div class="setting-item">
                        <span class="setting-label">Font Size:</span>
                        <span class="setting-value" x-text="fontSize"></span>
                    </div>
                    <div class="setting-item">
                        <span class="setting-label">System Preference:</span>
                        <span class="setting-value" x-text="systemPreference"></span>
                    </div>
                </div>
            </div>

            {{-- Component Preview --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Live Preview</h3>
                </div>
                <div class="card-body space-y-4">
                    
                    {{-- Button Examples --}}
                    <div class="preview-section">
                        <h4 class="preview-title">Buttons</h4>
                        <div class="flex flex-wrap gap-2">
                            <button class="btn btn-primary">Primary</button>
                            <button class="btn btn-secondary">Secondary</button>
                            <button class="btn btn-outline">Outline</button>
                        </div>
                    </div>

                    {{-- Text Examples --}}
                    <div class="preview-section">
                        <h4 class="preview-title">Typography</h4>
                        <p class="mb-2" style="color: var(--text-primary);">Primary text content</p>
                        <p class="text-sm" style="color: var(--text-secondary);">Secondary text content</p>
                    </div>

                    {{-- Form Examples --}}
                    <div class="preview-section">
                        <h4 class="preview-title">Forms</h4>
                        <input type="text" 
                               class="input input-bordered w-full mb-2" 
                               placeholder="Sample input">
                        <select class="select select-bordered w-full">
                            <option>Sample option</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body space-y-3">
                    <button @click="resetToDefaults()" 
                            class="btn btn-outline w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset to Defaults
                    </button>
                    
                    <button @click="exportTheme()" 
                            class="btn btn-ghost w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Theme
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
        
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
function themeConfiguration() {
    return {
        themeMode: 'system',
        colorScheme: 'default',
        fontSize: 'normal',
        systemPreference: 'light',
        settings: {
            reducedMotion: false,
            smoothScrolling: true,
            highContrast: false
        },

        init() {
            // Load current settings
            this.themeMode = localStorage.getItem('theme-mode') || 'system';
            this.colorScheme = localStorage.getItem('theme-color-scheme') || 'default';
            this.fontSize = localStorage.getItem('theme-font-size') || 'normal';
            
            // Detect system preference
            this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            
            // Load advanced settings
            const savedSettings = localStorage.getItem('themeAdvancedSettings');
            if (savedSettings) {
                this.settings = { ...this.settings, ...JSON.parse(savedSettings) };
            }
            
            // Listen for system changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                this.systemPreference = e.matches ? 'dark' : 'light';
                if (this.themeMode === 'system') {
                    this.applyTheme();
                }
            });

            // Apply initial theme
            this.applyTheme();
            
            // Ensure loading screen is hidden
            this.$nextTick(() => {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 500);
                }
            });
        },

        updateThemeMode(mode) {
            this.themeMode = mode;
            localStorage.setItem('theme-mode', mode);
            localStorage.setItem('theme', mode); // For compatibility
            this.applyTheme();
            this.notifyThemeChange('Theme mode changed');
        },

        updateColorScheme(scheme) {
            this.colorScheme = scheme;
            localStorage.setItem('theme-color-scheme', scheme);
            this.applyTheme();
            this.notifyThemeChange('Color scheme changed');
        },

        updateFontSize(size) {
            this.fontSize = size;
            localStorage.setItem('theme-font-size', size);
            this.applyFontSize();
            this.notifyThemeChange('Font size changed');
        },

        applyTheme() {
            const resolvedTheme = this.getResolvedTheme();
            const htmlElement = document.documentElement;
            
            // Remove existing theme classes
            htmlElement.classList.remove('light', 'dark', 'theme-light', 'theme-dark');
            htmlElement.removeAttribute('data-theme');
            
            // Apply theme mode
            htmlElement.classList.add(resolvedTheme);
            htmlElement.setAttribute('data-theme', resolvedTheme);
            
            // Apply color scheme
            if (this.colorScheme !== 'default') {
                htmlElement.setAttribute('data-color-scheme', this.colorScheme);
                htmlElement.classList.add(`theme-${this.colorScheme}`);
            } else {
                htmlElement.removeAttribute('data-color-scheme');
            }
            
            // Apply font size
            this.applyFontSize();
            
            // Update meta theme color
            this.updateMetaThemeColor(resolvedTheme);
            
            console.log(`Applied theme: ${resolvedTheme}, color: ${this.colorScheme}, font: ${this.fontSize}`);
        },

        applyFontSize() {
            const htmlElement = document.documentElement;
            
            // Remove existing font size classes
            htmlElement.classList.remove('font-size-compact', 'font-size-normal', 'font-size-comfortable', 'font-size-large');
            
            // Apply new font size
            htmlElement.classList.add(`font-size-${this.fontSize}`);
            
            // Update CSS custom property
            const fontSizes = {
                compact: '14px',
                normal: '16px',
                comfortable: '18px',
                large: '20px'
            };
            
            htmlElement.style.setProperty('--base-font-size', fontSizes[this.fontSize]);
        },

        getResolvedTheme() {
            if (this.themeMode === 'system') {
                return this.systemPreference;
            }
            return this.themeMode;
        },

        updateMetaThemeColor(theme) {
            let themeColorMeta = document.querySelector('meta[name="theme-color"]');
            if (!themeColorMeta) {
                themeColorMeta = document.createElement('meta');
                themeColorMeta.name = 'theme-color';
                document.head.appendChild(themeColorMeta);
            }
            
            const themeColors = {
                light: '#ffffff',
                dark: '#0f172a'
            };
            
            themeColorMeta.content = themeColors[theme] || themeColors.light;
        },

        notifyThemeChange(reason = 'Theme changed') {
            window.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { 
                    themeMode: this.themeMode,
                    colorScheme: this.colorScheme,
                    fontSize: this.fontSize,
                    resolvedTheme: this.getResolvedTheme(),
                    systemPreference: this.systemPreference,
                    timestamp: new Date(),
                    reason: reason
                } 
            }));
        },

        // Helper methods
        getThemeLabel() {
            const resolved = this.getResolvedTheme();
            const colorLabel = this.colorScheme === 'default' ? '' : ` (${this.colorScheme})`;
            return `${resolved}${colorLabel}`;
        },

        getFontSizeLabel() {
            const labels = {
                compact: 'Compact (14px)',
                normal: 'Normal (16px)',
                comfortable: 'Comfortable (18px)',
                large: 'Large (20px)'
            };
            return labels[this.fontSize] || 'Normal (16px)';
        },

        resetToDefaults() {
            if (confirm('Are you sure you want to reset all theme settings to defaults?')) {
                this.themeMode = 'system';
                this.colorScheme = 'default';
                this.fontSize = 'normal';
                this.settings = {
                    reducedMotion: false,
                    smoothScrolling: true,
                    highContrast: false
                };
                
                // Clear localStorage
                localStorage.removeItem('theme-mode');
                localStorage.removeItem('theme-color-scheme');
                localStorage.removeItem('theme-font-size');
                localStorage.removeItem('theme');
                localStorage.removeItem('themeAdvancedSettings');
                
                this.applyTheme();
                this.notifyThemeChange('Reset to defaults');
            }
        },

        exportTheme() {
            const themeConfig = {
                themeMode: this.themeMode,
                colorScheme: this.colorScheme,
                fontSize: this.fontSize,
                settings: this.settings,
                timestamp: new Date().toISOString(),
                version: '1.0'
            };
            
            const dataStr = JSON.stringify(themeConfig, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = `theme-config-${new Date().toISOString().split('T')[0]}.json`;
            link.click();
        }
    }
}
</script>

{{-- Enhanced Theme Configuration Styles --}}
<style>
/* Theme Option Cards */
.theme-option {
    @apply block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200;
    border-color: var(--border-primary);
}

.theme-option:hover {
    @apply transform scale-105;
    border-color: var(--color-primary-300);
}

.theme-option.selected {
    border-color: var(--color-primary-500);
    @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Theme Preview */
.theme-preview {
    @apply w-full h-20 rounded-lg overflow-hidden border;
    border-color: var(--border-secondary);
}

.light-preview {
    @apply bg-white;
}

.dark-preview {
    @apply bg-gray-900;
}

.system-preview {
    @apply bg-gradient-to-r from-white via-gray-100 to-gray-900;
}

.preview-header {
    @apply h-3 border-b;
    background: linear-gradient(90deg, var(--color-primary-500) 0%, var(--color-primary-400) 100%);
    border-color: var(--border-primary);
}

.preview-content {
    @apply flex h-full;
}

.preview-sidebar {
    @apply w-6 border-r;
    background-color: var(--bg-secondary);
    border-color: var(--border-primary);
}

.gradient-sidebar {
    background: linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
}

.preview-main {
    @apply flex-1 p-2 space-y-1;
    background-color: var(--bg-primary);
}

.preview-item {
    @apply h-2 rounded;
    background-color: var(--bg-tertiary);
}

.preview-item:first-child {
    @apply w-3/4;
}

.preview-item:last-child {
    @apply w-1/2;
}

/* Color Scheme Options */
.color-scheme-option {
    @apply block text-center p-3 border-2 rounded-lg cursor-pointer transition-all duration-200;
    border-color: var(--border-primary);
}

.color-scheme-option:hover {
    @apply transform scale-105;
    border-color: var(--color-primary-300);
}

.color-scheme-option.selected {
    border-color: var(--color-primary-500);
    @apply ring-2 ring-blue-500 ring-opacity-50;
}

.color-preview {
    @apply flex justify-center space-x-1 mb-2;
}

.color-circle {
    @apply w-4 h-4 rounded-full border;
    border-color: var(--border-secondary);
}

.color-name {
    @apply text-sm font-medium;
    color: var(--text-primary);
}

/* Font Size Options */
.font-size-option {
    @apply block text-center p-4 border-2 rounded-lg cursor-pointer transition-all duration-200;
    border-color: var(--border-primary);
}

.font-size-option:hover {
    @apply transform scale-105;
    border-color: var(--color-primary-300);
}

.font-size-option.selected {
    border-color: var(--color-primary-500);
    @apply ring-2 ring-blue-500 ring-opacity-50;
}

.font-preview {
    @apply mb-2 flex flex-col items-center space-y-1;
}

.font-preview span:first-child {
    @apply font-bold;
    color: var(--color-primary-500);
}

.preview-text {
    @apply text-xs;
    color: var(--text-secondary);
}

.font-label {
    @apply text-sm font-medium;
    color: var(--text-primary);
}

/* Settings Display */
.setting-item {
    @apply flex justify-between items-center py-2 border-b;
    border-color: var(--border-primary);
}

.setting-item:last-child {
    @apply border-b-0;
}

.setting-label {
    @apply text-sm font-medium;
    color: var(--text-secondary);
}

.setting-value {
    @apply text-sm font-mono px-2 py-1 rounded;
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
}

/* Preview Sections */
.preview-section {
    @apply space-y-2;
}

.preview-title {
    @apply text-sm font-semibold;
    color: var(--text-primary);
}

/* Responsive Design */
@media (max-width: 768px) {
    .theme-option,
    .color-scheme-option,
    .font-size-option {
        @apply transform-none;
    }
    
    .theme-option:hover,
    .color-scheme-option:hover,
    .font-size-option:hover {
        @apply transform-none;
    }
}
</style>
@endsection
