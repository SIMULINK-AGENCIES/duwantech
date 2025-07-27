@extends('admin.layouts.master')

@section('title', 'Theme Toggle Test - Task 2.4.2')

@section('content')
<div class="p-6 max-w-6xl mx-auto space-y-8">
    
    {{-- Page Header --}}
    <div class="card">
        <div class="card-header">
            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">
                Theme Toggle Functionality Test - Task 2.4.2
            </h1>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">
                Testing light/dark mode switching, system preference detection, and local storage persistence
            </p>
        </div>
    </div>

    {{-- Theme Status Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Current Theme Info --}}
        <div class="card" x-data="themeStatus()">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Current Theme</h3>
            </div>
            <div class="card-body space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Selected:</span>
                    <span class="badge badge-primary" x-text="currentTheme"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Resolved:</span>
                    <span class="badge badge-secondary" x-text="resolvedTheme"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">System:</span>
                    <span class="badge badge-outline" x-text="systemPreference"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Last Changed:</span>
                    <span class="text-xs" style="color: var(--text-tertiary);" x-text="lastChanged"></span>
                </div>
            </div>
        </div>

        {{-- Theme Toggle Controls --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Quick Actions</h3>
            </div>
            <div class="card-body space-y-3">
                <button @click="setTheme('light')" 
                        class="btn btn-outline w-full">
                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                    </svg>
                    Light Mode
                </button>
                
                <button @click="setTheme('dark')" 
                        class="btn btn-outline w-full">
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
                    </svg>
                    Dark Mode
                </button>
                
                <button @click="setTheme('system')" 
                        class="btn btn-outline w-full">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd"/>
                    </svg>
                    System Default
                </button>
            </div>
        </div>

        {{-- Local Storage Info --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Storage Status</h3>
            </div>
            <div class="card-body space-y-3" x-data="storageStatus()">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Theme Stored:</span>
                    <span class="text-xs font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded" x-text="storedTheme"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: var(--text-secondary);">Advanced Settings:</span>
                    <span class="text-xs" style="color: var(--text-tertiary);" x-text="hasAdvancedSettings ? 'Yes' : 'No'"></span>
                </div>
                <button @click="clearStorage()" 
                        class="btn btn-outline btn-sm w-full">
                    Clear Storage
                </button>
            </div>
        </div>
    </div>

    {{-- Theme Preview Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- UI Components Preview --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">UI Components</h3>
                <p class="text-sm mt-1" style="color: var(--text-secondary);">See how components adapt to themes</p>
            </div>
            <div class="card-body space-y-4">
                
                {{-- Buttons --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-medium" style="color: var(--text-primary);">Buttons</h4>
                    <div class="flex flex-wrap gap-2">
                        <button class="btn btn-primary">Primary</button>
                        <button class="btn btn-secondary">Secondary</button>
                        <button class="btn btn-outline">Outline</button>
                        <button class="btn btn-ghost">Ghost</button>
                    </div>
                </div>

                {{-- Badges --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-medium" style="color: var(--text-primary);">Badges</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge badge-primary">Primary</span>
                        <span class="badge badge-secondary">Secondary</span>
                        <span class="badge badge-success">Success</span>
                        <span class="badge badge-warning">Warning</span>
                        <span class="badge badge-error">Error</span>
                    </div>
                </div>

                {{-- Form Elements --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-medium" style="color: var(--text-primary);">Form Elements</h4>
                    <input type="text" 
                           class="input input-bordered w-full" 
                           placeholder="Sample input field">
                    <select class="select select-bordered w-full">
                        <option>Sample select option</option>
                        <option>Another option</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Theme Event Log --}}
        <div class="card" x-data="eventLog()">
            <div class="card-header">
                <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Theme Events</h3>
                <button @click="clearLog()" class="btn btn-ghost btn-sm">Clear</button>
            </div>
            <div class="card-body">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <div x-show="events.length === 0" 
                         class="text-center text-sm" 
                         style="color: var(--text-tertiary);">
                        No events logged yet. Try changing themes to see events.
                    </div>
                    <template x-for="event in events" :key="event.id">
                        <div class="text-xs mb-2 p-2 bg-white dark:bg-gray-700 rounded border-l-2"
                             :class="{
                                'border-blue-500': event.type === 'theme-changed',
                                'border-green-500': event.type === 'system-changed',
                                'border-orange-500': event.type === 'storage-changed'
                             }">
                            <div class="font-mono text-gray-500 dark:text-gray-400" x-text="event.timestamp"></div>
                            <div class="font-medium" style="color: var(--text-primary);" x-text="event.message"></div>
                            <div class="text-gray-600 dark:text-gray-300" x-text="event.details"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Implementation Details --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Task 2.4.2 Implementation Status</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Light/Dark Mode Switching --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold mb-2" style="color: var(--text-primary);">Light/Dark Mode Switching</h4>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        ✅ Manual theme selection<br>
                        ✅ Smooth transitions<br>
                        ✅ Visual feedback<br>
                        ✅ Keyboard accessibility
                    </p>
                </div>

                {{-- System Preference Detection --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold mb-2" style="color: var(--text-primary);">System Preference Detection</h4>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        ✅ Automatic detection<br>
                        ✅ Real-time updates<br>
                        ✅ System mode option<br>
                        ✅ Preference monitoring
                    </p>
                </div>

                {{-- Local Storage Persistence --}}
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold mb-2" style="color: var(--text-primary);">Local Storage Persistence</h4>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        ✅ Settings saved<br>
                        ✅ Cross-tab sync<br>
                        ✅ Error handling<br>
                        ✅ Fallback support
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Theme status component
function themeStatus() {
    return {
        currentTheme: 'system',
        resolvedTheme: 'light',
        systemPreference: 'light',
        lastChanged: 'Never',

        init() {
            this.updateStatus();
            
            // Listen for theme changes
            window.addEventListener('theme-changed', (e) => {
                this.updateStatus();
            });

            // Update every second to keep current
            setInterval(() => {
                this.updateStatus();
            }, 1000);
        },

        updateStatus() {
            this.currentTheme = localStorage.getItem('theme') || 'system';
            this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            this.resolvedTheme = this.currentTheme === 'system' ? this.systemPreference : this.currentTheme;
            
            const changed = localStorage.getItem('theme-changed-at');
            if (changed) {
                this.lastChanged = new Date(changed).toLocaleTimeString();
            }
        },

        setTheme(theme) {
            window.themeUtils.setTheme(theme);
        }
    }
}

// Storage status component
function storageStatus() {
    return {
        storedTheme: 'none',
        hasAdvancedSettings: false,

        init() {
            this.updateStatus();
            
            window.addEventListener('storage', () => {
                this.updateStatus();
            });
        },

        updateStatus() {
            this.storedTheme = localStorage.getItem('theme') || 'none';
            this.hasAdvancedSettings = !!localStorage.getItem('themeAdvancedSettings');
        },

        clearStorage() {
            if (confirm('Clear all theme storage data?')) {
                localStorage.removeItem('theme');
                localStorage.removeItem('theme-changed-at');
                localStorage.removeItem('themeAdvancedSettings');
                this.updateStatus();
                location.reload();
            }
        }
    }
}

// Event log component
function eventLog() {
    return {
        events: [],
        nextId: 1,

        init() {
            // Listen for all theme-related events
            window.addEventListener('theme-changed', (e) => {
                this.addEvent('theme-changed', 'Theme Changed', 
                    `From: ${e.detail.theme} | Resolved: ${e.detail.resolvedTheme} | Reason: ${e.detail.reason}`);
            });

            window.addEventListener('storage', (e) => {
                if (e.key === 'theme') {
                    this.addEvent('storage-changed', 'Storage Updated', 
                        `Key: ${e.key} | Old: ${e.oldValue} | New: ${e.newValue}`);
                }
            });

            // System preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                this.addEvent('system-changed', 'System Preference Changed', 
                    `New preference: ${e.matches ? 'dark' : 'light'}`);
            });
        },

        addEvent(type, message, details) {
            this.events.unshift({
                id: this.nextId++,
                type: type,
                message: message,
                details: details,
                timestamp: new Date().toLocaleTimeString()
            });

            // Keep only last 10 events
            if (this.events.length > 10) {
                this.events = this.events.slice(0, 10);
            }
        },

        clearLog() {
            this.events = [];
        }
    }
}
</script>
@endsection
