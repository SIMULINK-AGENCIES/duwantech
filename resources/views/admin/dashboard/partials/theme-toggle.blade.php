{{-- Enhanced Theme Toggle Component - Task 2.4.2 Implementation --}}
<div x-data="themeToggle()" 
     x-init="init()"
     class="relative">
    
    <!-- Theme Toggle Button -->
    <button @click="showDropdown = !showDropdown" 
            class="btn btn-ghost btn-sm p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 relative"
            :title="getThemeTooltip()"
            :aria-label="'Current theme: ' + getThemeLabel(currentTheme) + '. Click to change theme.'">
        
        <!-- Theme Indicator Badge -->
        <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full flex items-center justify-center text-xs font-bold"
             :class="getIndicatorClasses()"
             x-text="getThemeIndicator()">
        </div>
        
        <!-- Light Mode Icon -->
        <svg x-show="getResolvedTheme() === 'light'" 
             x-transition:enter="transition ease-in-out duration-200"
             x-transition:enter-start="opacity-0 scale-50 rotate-180"
             x-transition:enter-end="opacity-100 scale-100 rotate-0"
             x-transition:leave="transition ease-in-out duration-200" 
             x-transition:leave-start="opacity-100 scale-100 rotate-0"
             x-transition:leave-end="opacity-0 scale-50 rotate-180"
             class="w-5 h-5 text-yellow-500" 
             fill="currentColor" 
             viewBox="0 0 24 24">
            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
        </svg>

        <!-- Dark Mode Icon -->
        <svg x-show="getResolvedTheme() === 'dark'" 
             x-transition:enter="transition ease-in-out duration-200"
             x-transition:enter-start="opacity-0 scale-50 rotate-180"
             x-transition:enter-end="opacity-100 scale-100 rotate-0"
             x-transition:leave="transition ease-in-out duration-200"
             x-transition:leave-start="opacity-100 scale-100 rotate-0"
             x-transition:leave-end="opacity-0 scale-50 rotate-180"
             class="w-5 h-5 text-blue-400" 
             fill="currentColor" 
             viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
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
        isTransitioning: false,
        lastChanged: null,

        init() {
            // Get stored theme preference or default to system
            this.currentTheme = localStorage.getItem('theme') || 'system';
            
            // Detect initial system preference
            this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            
            // Listen for system theme changes with enhanced detection
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            darkModeQuery.addEventListener('change', (e) => {
                const newPreference = e.matches ? 'dark' : 'light';
                console.log('System theme changed to:', newPreference);
                
                this.systemPreference = newPreference;
                
                // If using system theme, apply the change immediately
                if (this.currentTheme === 'system') {
                    this.applyTheme();
                    this.notifyThemeChange('System preference changed');
                }
            });

            // Listen for storage changes from other tabs/windows
            window.addEventListener('storage', (e) => {
                if (e.key === 'theme' && e.newValue !== this.currentTheme) {
                    console.log('Theme changed in another tab:', e.newValue);
                    this.currentTheme = e.newValue || 'system';
                    this.applyTheme();
                }
            });

            // Apply initial theme
            this.applyTheme();
            
            // Mark as ready
            this.lastChanged = new Date();
            console.log('Theme toggle initialized with theme:', this.currentTheme);
        },

        toggleTheme() {
            // Quick toggle between light and dark (excludes system for faster switching)
            if (this.currentTheme === 'light') {
                this.setTheme('dark');
            } else if (this.currentTheme === 'dark') {
                this.setTheme('system');
            } else {
                this.setTheme('light');
            }
        },

        setTheme(theme) {
            if (this.isTransitioning) return; // Prevent rapid changes
            
            this.isTransitioning = true;
            this.currentTheme = theme;
            this.showDropdown = false;
            this.lastChanged = new Date();
            
            // Store preference with error handling
            try {
                localStorage.setItem('theme', theme);
                localStorage.setItem('theme-changed-at', this.lastChanged.toISOString());
            } catch (error) {
                console.warn('Failed to save theme preference:', error);
            }
            
            // Apply theme with transition
            this.applyTheme();
            
            // Notify other components
            this.notifyThemeChange('User changed theme');
            
            // Reset transition flag
            setTimeout(() => {
                this.isTransitioning = false;
            }, 300);
        },

        applyTheme() {
            const resolvedTheme = this.getResolvedTheme();
            const htmlElement = document.documentElement;
            
            console.log(`Applying theme: ${this.currentTheme} (resolved: ${resolvedTheme})`);
            
            // Add animation classes
            htmlElement.classList.add('theme-transitioning', 'theme-will-change');
            
            // Add switching animation to body
            document.body.classList.add('theme-switch-animation', 'switching');
            
            // Remove existing theme classes
            htmlElement.classList.remove('light', 'dark');
            htmlElement.removeAttribute('data-theme');
            
            // Apply new theme with transition delay
            setTimeout(() => {
                htmlElement.classList.add(resolvedTheme);
                htmlElement.setAttribute('data-theme', resolvedTheme);
                
                // Update meta theme-color for mobile browsers
                this.updateMetaThemeColor(resolvedTheme);
                
                // Update CSS custom properties for enhanced theming
                this.updateThemeProperties(resolvedTheme);
            }, 50);
            
            // Remove animation classes after transition
            setTimeout(() => {
                htmlElement.classList.remove('theme-transitioning', 'theme-will-change');
                document.body.classList.remove('theme-switch-animation', 'switching');
                htmlElement.classList.add('theme-animation-complete');
                
                // Clean up performance class
                setTimeout(() => {
                    htmlElement.classList.remove('theme-animation-complete');
                }, 100);
            }, 350);
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
            
            // Enhanced theme colors matching our design system
            const themeColors = {
                light: getComputedStyle(document.documentElement).getPropertyValue('--bg-primary') || '#ffffff',
                dark: getComputedStyle(document.documentElement).getPropertyValue('--bg-secondary') || '#1f2937'
            };
            
            themeColorMeta.content = themeColors[theme] || themeColors.light;
        },

        updateThemeProperties(theme) {
            // Update any additional CSS properties based on theme
            const root = document.documentElement;
            
            if (theme === 'dark') {
                root.style.setProperty('--scroll-behavior', 'smooth');
                root.style.setProperty('--selection-bg', 'rgba(59, 130, 246, 0.3)');
            } else {
                root.style.setProperty('--scroll-behavior', 'smooth');
                root.style.setProperty('--selection-bg', 'rgba(59, 130, 246, 0.2)');
            }
        },

        notifyThemeChange(reason = 'Theme changed') {
            // Dispatch enhanced theme change event
            window.dispatchEvent(new CustomEvent('theme-changed', { 
                detail: { 
                    theme: this.currentTheme,
                    resolvedTheme: this.getResolvedTheme(),
                    systemPreference: this.systemPreference,
                    timestamp: this.lastChanged,
                    reason: reason
                } 
            }));
            
            // Also dispatch to any theme-aware components
            document.querySelectorAll('[data-theme-aware]').forEach(element => {
                element.dispatchEvent(new CustomEvent('theme-update', {
                    detail: { theme: this.getResolvedTheme() }
                }));
            });
        },

        // Helper methods for enhanced UI
        getThemeLabel(theme) {
            const labels = {
                light: 'Light Mode',
                dark: 'Dark Mode', 
                system: 'System Default'
            };
            return labels[theme] || 'Unknown';
        },

        getThemeTooltip() {
            const resolved = this.getResolvedTheme();
            if (this.currentTheme === 'system') {
                return `System theme (currently ${resolved}). Click to change theme.`;
            }
            return `${this.getThemeLabel(this.currentTheme)}. Click to change theme.`;
        },

        getThemeIndicator() {
            const indicators = {
                light: 'L',
                dark: 'D',
                system: 'S'
            };
            return indicators[this.currentTheme] || '?';
        },

        getIndicatorClasses() {
            const classes = {
                light: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                dark: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                system: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
            };
            return classes[this.currentTheme] || classes.system;
        },

        // Keyboard support
        handleKeydown(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                this.showDropdown = !this.showDropdown;
            } else if (event.key === 'Escape') {
                this.showDropdown = false;
            }
        },

        // Debug method for development
        getThemeInfo() {
            return {
                current: this.currentTheme,
                resolved: this.getResolvedTheme(),
                system: this.systemPreference,
                lastChanged: this.lastChanged,
                isTransitioning: this.isTransitioning
            };
        }
    }
}

// Global theme utilities
window.themeUtils = {
    getCurrentTheme() {
        return localStorage.getItem('theme') || 'system';
    },
    
    setTheme(theme) {
        const event = new CustomEvent('set-theme', { detail: { theme } });
        window.dispatchEvent(event);
    },
    
    getSystemPreference() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
};
</script>
