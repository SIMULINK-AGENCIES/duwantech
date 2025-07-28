<!-- Sidebar Footer Content -->
<div class="space-y-3">
    <!-- System Status Indicator -->
    <div class="flex items-center justify-between" :class="{ 'justify-center': sidebarCollapsed }">
        <div class="flex items-center space-x-2" x-show="!sidebarCollapsed" x-transition>
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-xs text-gray-500">System Online</span>
        </div>
        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" 
             x-show="sidebarCollapsed" 
             x-transition
             title="System Online"></div>
    </div>

    <!-- Quick Theme Toggle -->
    <div class="flex items-center justify-between" :class="{ 'justify-center': sidebarCollapsed }">
        <span class="text-xs text-gray-500" x-show="!sidebarCollapsed" x-transition>Dark Mode</span>
        <button type="button" 
                class="relative inline-flex items-center h-5 w-9 rounded-full bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                :class="{ 'bg-blue-600': darkMode }"
                @click="toggleDarkMode()"
                x-data="{ darkMode: false }"
                aria-label="Toggle dark mode">
            <span class="sr-only">Toggle dark mode</span>
            <span class="inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200"
                  :class="{ 'translate-x-4': darkMode, 'translate-x-0': !darkMode }"></span>
        </button>
    </div>

    <!-- Help & Support -->
    <div class="pt-2 border-t border-gray-200">
        <div class="flex items-center space-x-2" :class="{ 'justify-center': sidebarCollapsed }">
            <button type="button" 
                    class="flex items-center space-x-2 text-xs text-gray-500 hover:text-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded"
                    @click="$dispatch('open-help')"
                    :title="sidebarCollapsed ? 'Help & Support' : ''"
                    aria-label="Help and support">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition>Help</span>
            </button>
        </div>
    </div>

    <!-- Version Info -->
    <div class="text-center" x-show="!sidebarCollapsed" x-transition>
        <p class="text-xs text-gray-400">
            v{{ config('app.version', '1.0.0') }}
        </p>
    </div>
</div>

<script>
function toggleDarkMode() {
    // This will be expanded when we implement full dark mode support
    const isDark = document.documentElement.classList.contains('dark');
    
    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
    
    // Dispatch event for other components to react to theme change
    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: { theme: isDark ? 'light' : 'dark' }
    }));
}

// Initialize dark mode based on saved preference or system preference
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.classList.add('dark');
    }
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
});
</script>
