@extends('admin.layouts.master')

@section('title', 'Theme Configuration & Animations Test')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-8" x-data="themeTestSuite()">
    
    {{-- Page Header --}}
    <div class="card stagger-animation">
        <div class="card-header">
            <h1 class="text-3xl font-bold" style="color: var(--text-primary);">
                Theme Configuration & Animations Test Suite
            </h1>
            <p class="text-sm mt-1" style="color: var(--text-secondary);">
                Testing Tasks 2.4.3 (Configuration) and 2.4.4 (Animations)
            </p>
        </div>
    </div>

    {{-- Quick Actions Bar --}}
    <div class="card stagger-animation">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                
                {{-- Theme Mode Tests --}}
                <button @click="testThemeSwitch('light')" 
                        class="btn btn-outline theme-scale theme-ripple">
                    <div class="theme-spinner" x-show="isLoading" x-cloak></div>
                    <span x-show="!isLoading">☀️ Light</span>
                </button>
                
                <button @click="testThemeSwitch('dark')" 
                        class="btn btn-outline theme-scale theme-ripple">
                    <div class="theme-spinner" x-show="isLoading" x-cloak></div>
                    <span x-show="!isLoading">🌙 Dark</span>
                </button>
                
                <button @click="testThemeSwitch('system')" 
                        class="btn btn-outline theme-scale theme-ripple">
                    <div class="theme-spinner" x-show="isLoading" x-cloak></div>
                    <span x-show="!isLoading">💻 System</span>
                </button>

                {{-- Color Scheme Tests --}}
                <button @click="testColorScheme('blue')" 
                        class="btn btn-primary theme-scale">
                    🌊 Ocean
                </button>
                
                <button @click="testColorScheme('green')" 
                        class="btn btn-success theme-scale">
                    🌲 Forest
                </button>
                
                <button @click="testColorScheme('purple')" 
                        class="btn theme-scale"
                        style="background: #7c3aed; color: white;">
                    👑 Royal
                </button>
            </div>
        </div>
    </div>

    {{-- Animation Test Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Animation Tests --}}
        <div class="xl:col-span-2 space-y-6">
            
            {{-- Transition Animations --}}
            <div class="card theme-fade stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Transition Animations</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Test smooth color transitions</p>
                </div>
                <div class="card-body space-y-4">
                    
                    {{-- Animated Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg theme-scale smooth-transition"
                             style="background-color: var(--bg-secondary); border: 1px solid var(--border-primary);">
                            <h4 class="font-semibold mb-2" style="color: var(--text-primary);">Smooth Card</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">
                                This card demonstrates smooth color transitions when themes change.
                            </p>
                        </div>
                        
                        <div class="p-4 rounded-lg theme-scale smooth-transition gradient-flow">
                            <h4 class="font-semibold mb-2 text-white">Gradient Flow</h4>
                            <p class="text-sm text-white opacity-90">
                                Animated gradient background that flows continuously.
                            </p>
                        </div>
                    </div>

                    {{-- Animation Control Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <button @click="triggerAnimation('bounce')" 
                                class="btn btn-outline theme-bounce" 
                                :class="{ 'theme-bounce': animations.bounce }">
                            Bounce Effect
                        </button>
                        
                        <button @click="triggerAnimation('shake')" 
                                class="btn btn-outline theme-shake" 
                                :class="{ 'theme-shake': animations.shake }">
                            Shake Effect
                        </button>
                        
                        <button @click="triggerAnimation('pulse')" 
                                class="btn btn-outline theme-pulse" 
                                :class="{ 'theme-pulse': animations.pulse }">
                            Pulse Effect
                        </button>
                        
                        <button @click="triggerAnimation('glow')" 
                                class="btn btn-primary theme-glow" 
                                :class="{ 'theme-glow': animations.glow }">
                            Glow Effect
                        </button>
                    </div>
                </div>
            </div>

            {{-- Theme Preview Animations --}}
            <div class="card stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Theme Preview Animations</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Interactive theme previews with animations</p>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Flip Animation Preview --}}
                        <div class="theme-preview-container">
                            <div class="theme-preview-flip p-6 rounded-lg cursor-pointer"
                                 @click="flipPreview()" 
                                 :class="{ 'flipping': previewFlipped }"
                                 style="background-color: var(--bg-secondary); border: 2px solid var(--border-primary);">
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-2 rounded-full"
                                         style="background-color: var(--color-primary-500);"></div>
                                    <h4 class="font-semibold" style="color: var(--text-primary);">Flip Preview</h4>
                                    <p class="text-sm" style="color: var(--text-secondary);">Click to flip</p>
                                </div>
                            </div>
                        </div>

                        {{-- Slide Animation Preview --}}
                        <div class="overflow-hidden rounded-lg" style="border: 2px solid var(--border-primary);">
                            <div class="theme-preview-slide p-6"
                                 :class="{ 'slide-out': slideOut }"
                                 style="background-color: var(--bg-secondary);">
                                <div class="text-center">
                                    <div class="w-8 h-8 mx-auto mb-2 rounded-full"
                                         style="background-color: var(--color-accent-500);"></div>
                                    <h4 class="font-semibold" style="color: var(--text-primary);">Slide Preview</h4>
                                    <button @click="slidePreview()" 
                                            class="text-sm btn btn-sm btn-ghost">
                                        Click to slide
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Color Morph Preview --}}
                        <div class="p-6 rounded-lg"
                             style="border: 2px solid var(--border-primary);">
                            <div class="text-center">
                                <div class="w-8 h-8 mx-auto mb-2 rounded-full color-morph"></div>
                                <h4 class="font-semibold" style="color: var(--text-primary);">Color Morph</h4>
                                <p class="text-sm" style="color: var(--text-secondary);">Auto-animating</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Font Size Testing --}}
            <div class="card stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Font Size Configuration</h3>
                    <p class="text-sm mt-1" style="color: var(--text-secondary);">Test different font size preferences</p>
                </div>
                <div class="card-body space-y-4">
                    
                    {{-- Font Size Controls --}}
                    <div class="flex flex-wrap gap-2">
                        <button @click="setFontSize('compact')" 
                                class="btn btn-sm theme-scale"
                                :class="{ 'btn-primary': currentFontSize === 'compact', 'btn-outline': currentFontSize !== 'compact' }">
                            Compact
                        </button>
                        <button @click="setFontSize('normal')" 
                                class="btn btn-sm theme-scale"
                                :class="{ 'btn-primary': currentFontSize === 'normal', 'btn-outline': currentFontSize !== 'normal' }">
                            Normal
                        </button>
                        <button @click="setFontSize('comfortable')" 
                                class="btn btn-sm theme-scale"
                                :class="{ 'btn-primary': currentFontSize === 'comfortable', 'btn-outline': currentFontSize !== 'comfortable' }">
                            Comfortable
                        </button>
                        <button @click="setFontSize('large')" 
                                class="btn btn-sm theme-scale"
                                :class="{ 'btn-primary': currentFontSize === 'large', 'btn-outline': currentFontSize !== 'large' }">
                            Large
                        </button>
                    </div>

                    {{-- Font Size Examples --}}
                    <div class="space-y-3 p-4 rounded-lg smooth-transition"
                         style="background-color: var(--bg-tertiary);">
                        <p class="text-size-xs" style="color: var(--text-secondary);">Extra Small Text (12px) - Secondary content</p>
                        <p class="text-size-sm" style="color: var(--text-secondary);">Small Text (14px) - Captions and labels</p>
                        <p class="text-size-base" style="color: var(--text-primary);">Base Text (16px) - Main content text</p>
                        <p class="text-size-lg" style="color: var(--text-primary);">Large Text (18px) - Emphasized content</p>
                        <p class="text-size-xl font-semibold" style="color: var(--text-primary);">Extra Large Text (20px) - Headings</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status & Monitoring Panel --}}
        <div class="space-y-6">
            
            {{-- Current Configuration --}}
            <div class="card theme-fade stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Current Config</h3>
                </div>
                <div class="card-body space-y-3">
                    <div class="config-item">
                        <span class="config-label">Theme Mode:</span>
                        <span class="config-value" x-text="currentTheme"></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Color Scheme:</span>
                        <span class="config-value" x-text="currentColorScheme"></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Font Size:</span>
                        <span class="config-value" x-text="currentFontSize"></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">System Pref:</span>
                        <span class="config-value" x-text="systemPreference"></span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Animations:</span>
                        <span class="config-value" x-text="animationsEnabled ? 'Enabled' : 'Disabled'"></span>
                    </div>
                </div>
            </div>

            {{-- Animation Status --}}
            <div class="card theme-fade stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Animation Status</h3>
                </div>
                <div class="card-body space-y-2">
                    <template x-for="(active, name) in animations" :key="name">
                        <div class="flex justify-between items-center">
                            <span class="text-sm capitalize" style="color: var(--text-secondary);" x-text="name"></span>
                            <div class="w-3 h-3 rounded-full transition-colors"
                                 :class="active ? 'bg-green-500' : 'bg-gray-300'"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Performance Monitor --}}
            <div class="card theme-fade stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Performance</h3>
                </div>
                <div class="card-body space-y-3">
                    <div class="performance-item">
                        <span class="text-sm" style="color: var(--text-secondary);">Theme Switches:</span>
                        <span class="text-sm font-mono" x-text="performanceStats.themeChanges"></span>
                    </div>
                    <div class="performance-item">
                        <span class="text-sm" style="color: var(--text-secondary);">Avg Switch Time:</span>
                        <span class="text-sm font-mono" x-text="performanceStats.avgSwitchTime + 'ms'"></span>
                    </div>
                    <div class="performance-item">
                        <span class="text-sm" style="color: var(--text-secondary);">Last Switch:</span>
                        <span class="text-sm font-mono" x-text="performanceStats.lastSwitch"></span>
                    </div>
                </div>
            </div>

            {{-- Task Completion Status --}}
            <div class="card theme-fade stagger-animation">
                <div class="card-header">
                    <h3 class="text-lg font-semibold" style="color: var(--text-primary);">Task Status</h3>
                </div>
                <div class="card-body space-y-4">
                    
                    {{-- Task 2.4.3 Status --}}
                    <div class="task-status">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-sm" style="color: var(--text-primary);">Task 2.4.3</h4>
                            <span class="badge badge-success">✅ Complete</span>
                        </div>
                        <ul class="text-xs space-y-1" style="color: var(--text-secondary);">
                            <li>✅ Multiple theme options</li>
                            <li>✅ Custom color schemes</li>
                            <li>✅ Font size preferences</li>
                        </ul>
                    </div>

                    {{-- Task 2.4.4 Status --}}
                    <div class="task-status">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-sm" style="color: var(--text-primary);">Task 2.4.4</h4>
                            <span class="badge badge-success">✅ Complete</span>
                        </div>
                        <ul class="text-xs space-y-1" style="color: var(--text-secondary);">
                            <li>✅ Smooth color transitions</li>
                            <li>✅ Loading states</li>
                            <li>✅ Theme preview animations</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function themeTestSuite() {
    return {
        // Current state
        currentTheme: 'system',
        currentColorScheme: 'default',
        currentFontSize: 'normal',
        systemPreference: 'light',
        isLoading: false,
        
        // Animation states
        animations: {
            bounce: false,
            shake: false,
            pulse: false,
            glow: false
        },
        previewFlipped: false,
        slideOut: false,
        animationsEnabled: true,

        // Performance tracking
        performanceStats: {
            themeChanges: 0,
            avgSwitchTime: 0,
            lastSwitch: 'Never',
            switchTimes: []
        },

        init() {
            // Load current settings
            this.currentTheme = localStorage.getItem('theme-mode') || 'system';
            this.currentColorScheme = localStorage.getItem('theme-color-scheme') || 'default';
            this.currentFontSize = localStorage.getItem('theme-font-size') || 'normal';
            
            // Detect system preference
            this.systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            
            // Check if animations are enabled
            this.animationsEnabled = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            // Listen for theme changes
            window.addEventListener('theme-changed', (e) => {
                this.updateFromEvent(e.detail);
            });

            // Listen for system changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                this.systemPreference = e.matches ? 'dark' : 'light';
            });

            console.log('Theme test suite initialized');
        },

        // Theme switching with performance tracking
        async testThemeSwitch(theme) {
            if (this.isLoading) return;
            
            this.isLoading = true;
            const startTime = performance.now();
            
            try {
                // Apply theme
                this.currentTheme = theme;
                localStorage.setItem('theme-mode', theme);
                localStorage.setItem('theme', theme); // For compatibility
                
                // Trigger theme application
                window.dispatchEvent(new CustomEvent('theme-changed', {
                    detail: {
                        themeMode: theme,
                        colorScheme: this.currentColorScheme,
                        fontSize: this.currentFontSize,
                        reason: 'Test suite switch'
                    }
                }));
                
                // Apply theme manually if needed
                this.applyTheme(theme);
                
                await this.delay(300); // Wait for animations
                
                // Track performance
                const endTime = performance.now();
                const switchTime = Math.round(endTime - startTime);
                this.trackPerformance(switchTime);
                
            } finally {
                this.isLoading = false;
            }
        },

        // Color scheme testing
        testColorScheme(scheme) {
            this.currentColorScheme = scheme;
            localStorage.setItem('theme-color-scheme', scheme);
            
            const htmlElement = document.documentElement;
            
            // Remove existing color scheme classes
            htmlElement.classList.remove('theme-blue', 'theme-green', 'theme-purple', 'theme-orange');
            htmlElement.removeAttribute('data-color-scheme');
            
            // Apply new color scheme
            if (scheme !== 'default') {
                htmlElement.setAttribute('data-color-scheme', scheme);
                htmlElement.classList.add(`theme-${scheme}`);
            }
        },

        // Font size testing
        setFontSize(size) {
            this.currentFontSize = size;
            localStorage.setItem('theme-font-size', size);
            
            const htmlElement = document.documentElement;
            
            // Remove existing font size classes
            htmlElement.classList.remove('font-size-compact', 'font-size-normal', 'font-size-comfortable', 'font-size-large');
            
            // Apply new font size
            htmlElement.classList.add(`font-size-${size}`);
            
            // Update CSS custom property
            const fontSizes = {
                compact: '14px',
                normal: '16px',
                comfortable: '18px',
                large: '20px'
            };
            htmlElement.style.setProperty('--base-font-size', fontSizes[size]);
        },

        // Animation testing
        triggerAnimation(animationType) {
            if (!this.animationsEnabled) return;
            
            // Reset animation
            this.animations[animationType] = false;
            
            // Trigger animation with delay
            this.$nextTick(() => {
                this.animations[animationType] = true;
                
                // Reset after animation duration
                setTimeout(() => {
                    this.animations[animationType] = false;
                }, 1000);
            });
        },

        // Preview animations
        flipPreview() {
            this.previewFlipped = !this.previewFlipped;
            
            setTimeout(() => {
                this.previewFlipped = false;
            }, 1200);
        },

        slidePreview() {
            this.slideOut = true;
            
            setTimeout(() => {
                this.slideOut = false;
            }, 800);
        },

        // Theme application
        applyTheme(theme) {
            const systemPreference = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const resolvedTheme = theme === 'system' ? systemPreference : theme;
            const htmlElement = document.documentElement;
            
            // Add animation classes
            htmlElement.classList.add('theme-transitioning', 'theme-will-change');
            document.body.classList.add('theme-switch-animation', 'switching');
            
            // Remove existing theme classes
            htmlElement.classList.remove('light', 'dark');
            htmlElement.removeAttribute('data-theme');
            
            // Apply new theme
            setTimeout(() => {
                htmlElement.classList.add(resolvedTheme);
                htmlElement.setAttribute('data-theme', resolvedTheme);
            }, 50);
            
            // Clean up animation classes
            setTimeout(() => {
                htmlElement.classList.remove('theme-transitioning', 'theme-will-change');
                document.body.classList.remove('theme-switch-animation', 'switching');
            }, 350);
        },

        // Performance tracking
        trackPerformance(switchTime) {
            this.performanceStats.themeChanges++;
            this.performanceStats.switchTimes.push(switchTime);
            this.performanceStats.lastSwitch = new Date().toLocaleTimeString();
            
            // Calculate average
            const sum = this.performanceStats.switchTimes.reduce((a, b) => a + b, 0);
            this.performanceStats.avgSwitchTime = Math.round(sum / this.performanceStats.switchTimes.length);
            
            // Keep only last 10 measurements
            if (this.performanceStats.switchTimes.length > 10) {
                this.performanceStats.switchTimes = this.performanceStats.switchTimes.slice(-10);
            }
        },

        // Update from theme change event
        updateFromEvent(detail) {
            if (detail.themeMode) this.currentTheme = detail.themeMode;
            if (detail.colorScheme) this.currentColorScheme = detail.colorScheme;
            if (detail.fontSize) this.currentFontSize = detail.fontSize;
        },

        // Utility function
        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    }
}
</script>

{{-- Enhanced Test Suite Styles --}}
<style>
/* Configuration Display */
.config-item {
    @apply flex justify-between items-center py-2 border-b;
    border-color: var(--border-primary);
}

.config-item:last-child {
    @apply border-b-0;
}

.config-label {
    @apply text-sm font-medium;
    color: var(--text-secondary);
}

.config-value {
    @apply text-sm font-mono px-2 py-1 rounded uppercase;
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
}

/* Performance Display */
.performance-item {
    @apply flex justify-between items-center;
}

/* Task Status */
.task-status {
    @apply p-3 rounded-lg;
    background-color: var(--bg-tertiary);
}

/* Loading Overlay */
.theme-loading-overlay {
    @apply absolute inset-0 flex items-center justify-center;
    background-color: var(--bg-overlay);
    z-index: 1000;
}

/* Animation Test Elements */
.animation-test-card {
    @apply p-4 rounded-lg transition-all duration-200;
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-primary);
}

.animation-test-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Stagger animation timing */
.stagger-animation > *:nth-child(1) { animation-delay: 0s; }
.stagger-animation > *:nth-child(2) { animation-delay: 0.1s; }
.stagger-animation > *:nth-child(3) { animation-delay: 0.2s; }
.stagger-animation > *:nth-child(4) { animation-delay: 0.3s; }

/* Status indicators */
.status-indicator {
    @apply w-3 h-3 rounded-full;
}

.status-indicator.active {
    @apply bg-green-500;
    animation: pulse 2s infinite;
}

.status-indicator.inactive {
    background-color: var(--border-tertiary);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .animation-test-card:hover {
        transform: none;
    }
    
    .theme-scale:hover {
        transform: scale(1.02);
    }
}
</style>
@endsection
