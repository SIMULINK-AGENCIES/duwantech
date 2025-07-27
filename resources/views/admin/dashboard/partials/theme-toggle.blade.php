{{-- Theme Toggle Component --}}
<div x-data="themeToggle()" 
     x-init="init()"
     class="relative">
    
    <!-- Theme Toggle Button -->
    <button @click="toggleTheme()" 
            class="btn btn-ghost btn-sm p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200"
            :title="currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'">
        
        <!-- Light Mode Icon -->
        <svg x-show="currentTheme === 'light'" 
             x-transition:enter="transition ease-in-out duration-200"
             x-transition:enter-start="opacity-0 scale-50"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in-out duration-200" 
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-50"
             class="w-5 h-5 text-yellow-500" 
             fill="currentColor" 
             viewBox="0 0 24 24">
            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
        </svg>

        <!-- Dark Mode Icon -->
        <svg x-show="currentTheme === 'dark'" 
             x-transition:enter="transition ease-in-out duration-200"
             x-transition:enter-start="opacity-0 scale-50"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in-out duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-50"
             class="w-5 h-5 text-blue-400" 
             fill="currentColor" 
             viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
        </svg>

        <!-- System Mode Icon -->
        <svg x-show="currentTheme === 'system'" 
             x-transition:enter="transition ease-in-out duration-200"
             x-transition:enter-start="opacity-0 scale-50"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in-out duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-50"
             class="w-5 h-5 text-gray-500" 
             fill="currentColor" 
             viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd"/>
        </svg>
    </button>

    <!-- Theme Dropdown Menu -->
    <div x-show="showDropdown" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         @click.outside="showDropdown = false"
         class="absolute right-0 top-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg min-w-[160px] z-50">
        
        <!-- Light Theme Option -->
        <button @click="setTheme('light')" 
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 first:rounded-t-lg transition-colors duration-150"
                :class="{ 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': currentTheme === 'light' }">
            <svg class="w-4 h-4 mr-3 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
            </svg>
            <span>Light</span>
            <svg x-show="currentTheme === 'light'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Dark Theme Option -->
        <button @click="setTheme('dark')" 
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                :class="{ 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': currentTheme === 'dark' }">
            <svg class="w-4 h-4 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
            </svg>
            <span>Dark</span>
            <svg x-show="currentTheme === 'dark'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- System Theme Option -->
        <button @click="setTheme('system')" 
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 last:rounded-b-lg transition-colors duration-150"
                :class="{ 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': currentTheme === 'system' }">
            <svg class="w-4 h-4 mr-3 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z" clip-rule="evenodd"/>
            </svg>
            <span>System</span>
            <svg x-show="currentTheme === 'system'" class="w-4 h-4 ml-auto text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
</div>

<script>
function themeToggle() {
    return {
        currentTheme: 'system',
        showDropdown: false,
        systemPreference: 'light',

        init() {
            // Get stored theme preference or default to system
            this.currentTheme = localStorage.getItem('theme') || 'system';
            
            // Detect system preference
            this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                this.systemPreference = e.matches ? 'dark' : 'light';
                if (this.currentTheme === 'system') {
                    this.applyTheme();
                }
            });

            // Apply initial theme
            this.applyTheme();
        },

        toggleTheme() {
            // Cycle through themes: light -> dark -> system -> light...
            const themes = ['light', 'dark', 'system'];
            const currentIndex = themes.indexOf(this.currentTheme);
            const nextIndex = (currentIndex + 1) % themes.length;
            this.setTheme(themes[nextIndex]);
        },

        setTheme(theme) {
            this.currentTheme = theme;
            this.showDropdown = false;
            
            // Store preference
            localStorage.setItem('theme', theme);
            
            // Apply theme
            this.applyTheme();
            
            // Dispatch theme change event for other components
            window.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { 
                    theme: theme,
                    resolvedTheme: this.getResolvedTheme()
                } 
            }));
        },

        applyTheme() {
            const resolvedTheme = this.getResolvedTheme();
            const htmlElement = document.documentElement;
            
            // Remove existing theme classes
            htmlElement.classList.remove('light', 'dark');
            htmlElement.removeAttribute('data-theme');
            
            // Apply new theme
            htmlElement.classList.add(resolvedTheme);
            htmlElement.setAttribute('data-theme', resolvedTheme);
            
            // Update meta theme-color for mobile browsers
            this.updateMetaThemeColor(resolvedTheme);
        },

        getResolvedTheme() {
            if (this.currentTheme === 'system') {
                return this.systemPreference;
            }
            return this.currentTheme;
        },

        updateMetaThemeColor(theme) {
            let themeColorMeta = document.querySelector('meta[name="theme-color"]');
            if (!themeColorMeta) {
                themeColorMeta = document.createElement('meta');
                themeColorMeta.name = 'theme-color';
                document.head.appendChild(themeColorMeta);
            }
            
            // Set appropriate theme color based on theme
            const themeColors = {
                light: '#ffffff',
                dark: '#1f2937'
            };
            
            themeColorMeta.content = themeColors[theme] || themeColors.light;
        }
    }
}
</script>
