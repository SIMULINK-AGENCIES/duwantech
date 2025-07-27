/**
 * Professional Admin Dashboard - Master Layout JavaScript
 * Enhanced interactivity, accessibility, and responsive behavior
 */

// ================================================================
// Core Layout Management
// ================================================================

class DashboardLayout {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.setupAccessibility();
        this.setupPerformanceOptimizations();
    }

    init() {
        this.sidebar = document.getElementById('sidebar');
        this.sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        this.mainContent = document.querySelector('.main-content');
        this.sidebarOverlay = document.querySelector('.sidebar-overlay');
        
        // State management
        this.state = {
            sidebarOpen: false,
            sidebarCollapsed: this.getSavedState('sidebar-collapsed', false),
            isMobile: window.innerWidth < 1024,
            isTablet: window.innerWidth >= 768 && window.innerWidth < 1024,
            isDesktop: window.innerWidth >= 1024,
            reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            highContrast: window.matchMedia('(prefers-contrast: high)').matches,
            darkMode: this.getSavedState('dark-mode', false)
        };

        this.updateLayout();
        this.initializeTheme();
    }

    setupEventListeners() {
        // Resize handler with debouncing
        this.debounceResize = this.debounce(() => {
            this.handleResize();
        }, 250);
        
        window.addEventListener('resize', this.debounceResize);

        // Sidebar toggle
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Overlay click to close sidebar
        if (this.sidebarOverlay) {
            this.sidebarOverlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardNavigation(e);
        });

        // Media query listeners
        const mobileQuery = window.matchMedia('(max-width: 1023px)');
        const tabletQuery = window.matchMedia('(min-width: 768px) and (max-width: 1023px)');
        const desktopQuery = window.matchMedia('(min-width: 1024px)');
        const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        const highContrastQuery = window.matchMedia('(prefers-contrast: high)');
        const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

        mobileQuery.addEventListener('change', () => this.handleMediaChange());
        tabletQuery.addEventListener('change', () => this.handleMediaChange());
        desktopQuery.addEventListener('change', () => this.handleMediaChange());
        reducedMotionQuery.addEventListener('change', () => this.handleReducedMotion());
        highContrastQuery.addEventListener('change', () => this.handleHighContrast());
        darkModeQuery.addEventListener('change', () => this.handleSystemThemeChange());

        // Custom events
        document.addEventListener('theme-changed', (e) => {
            this.handleThemeChange(e.detail);
        });
    }

    setupAccessibility() {
        // Skip link functionality
        const skipLink = document.querySelector('.skip-link');
        if (skipLink) {
            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector('#main-content');
                if (target) {
                    target.focus();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }

        // ARIA live regions for dynamic content
        this.createLiveRegion();

        // Focus management
        this.setupFocusManagement();

        // Announce page changes to screen readers
        this.announcePageChanges();
    }

    setupPerformanceOptimizations() {
        // Intersection Observer for lazy loading
        this.setupIntersectionObserver();

        // Preload critical resources
        this.preloadCriticalResources();

        // Service Worker registration (if available)
        this.registerServiceWorker();
    }

    // ================================================================
    // Sidebar Management
    // ================================================================

    toggleSidebar() {
        if (this.state.isMobile) {
            this.state.sidebarOpen = !this.state.sidebarOpen;
            this.updateMobileSidebar();
        } else {
            this.state.sidebarCollapsed = !this.state.sidebarCollapsed;
            this.updateDesktopSidebar();
            this.saveState('sidebar-collapsed', this.state.sidebarCollapsed);
        }

        this.announceToScreenReader(
            this.state.sidebarOpen || !this.state.sidebarCollapsed 
                ? 'Sidebar opened' 
                : 'Sidebar closed'
        );
    }

    closeSidebar() {
        if (this.state.isMobile) {
            this.state.sidebarOpen = false;
            this.updateMobileSidebar();
        }
    }

    updateMobileSidebar() {
        if (!this.sidebar) return;

        this.sidebar.classList.toggle('open', this.state.sidebarOpen);
        this.sidebarOverlay?.classList.toggle('active', this.state.sidebarOpen);

        // Prevent body scroll when sidebar is open
        document.body.style.overflow = this.state.sidebarOpen ? 'hidden' : '';

        // Update ARIA attributes
        this.sidebar.setAttribute('aria-hidden', !this.state.sidebarOpen);
        
        // Focus management
        if (this.state.sidebarOpen) {
            this.trapFocusInSidebar();
        } else {
            this.releaseFocusTrap();
        }
    }

    updateDesktopSidebar() {
        if (!this.sidebar || !this.mainContent) return;

        this.sidebar.classList.toggle('collapsed', this.state.sidebarCollapsed);
        this.mainContent.classList.toggle('sidebar-collapsed', this.state.sidebarCollapsed);

        // Update CSS custom property for animations
        document.documentElement.style.setProperty(
            '--sidebar-current-width',
            this.state.sidebarCollapsed ? 'var(--sidebar-collapsed-width)' : 'var(--sidebar-width)'
        );

        // Dispatch resize event for charts and other components
        setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
        }, 300);
    }

    // ================================================================
    // Responsive Behavior
    // ================================================================

    handleResize() {
        const newState = {
            isMobile: window.innerWidth < 1024,
            isTablet: window.innerWidth >= 768 && window.innerWidth < 1024,
            isDesktop: window.innerWidth >= 1024
        };

        // Check if device type changed
        if (newState.isMobile !== this.state.isMobile) {
            this.handleDeviceTypeChange(newState);
        }

        Object.assign(this.state, newState);
        this.updateLayout();
    }

    handleDeviceTypeChange(newState) {
        if (newState.isMobile && !this.state.isMobile) {
            // Switched to mobile
            this.state.sidebarOpen = false;
            this.closeSidebar();
        } else if (!newState.isMobile && this.state.isMobile) {
            // Switched to desktop
            this.state.sidebarOpen = false;
            this.sidebarOverlay?.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    handleMediaChange() {
        this.state.isMobile = window.innerWidth < 1024;
        this.state.isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
        this.state.isDesktop = window.innerWidth >= 1024;
        this.updateLayout();
    }

    updateLayout() {
        if (this.state.isMobile) {
            this.updateMobileSidebar();
        } else {
            this.updateDesktopSidebar();
        }

        // Update viewport meta tag for better mobile rendering
        this.updateViewportMeta();
    }

    updateViewportMeta() {
        let viewportMeta = document.querySelector('meta[name="viewport"]');
        if (!viewportMeta) {
            viewportMeta = document.createElement('meta');
            viewportMeta.name = 'viewport';
            document.head.appendChild(viewportMeta);
        }

        const content = this.state.isMobile
            ? 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'
            : 'width=device-width, initial-scale=1.0';
        
        viewportMeta.content = content;
    }

    // ================================================================
    // Theme Management
    // ================================================================

    initializeTheme() {
        const savedTheme = this.getSavedState('theme', null);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            this.enableDarkMode();
        } else {
            this.enableLightMode();
        }
    }

    enableDarkMode() {
        document.documentElement.classList.add('dark');
        this.state.darkMode = true;
        this.saveState('theme', 'dark');
        this.updateThemeColor('#1f2937');
    }

    enableLightMode() {
        document.documentElement.classList.remove('dark');
        this.state.darkMode = false;
        this.saveState('theme', 'light');
        this.updateThemeColor('#ffffff');
    }

    toggleTheme() {
        if (this.state.darkMode) {
            this.enableLightMode();
        } else {
            this.enableDarkMode();
        }

        this.announceToScreenReader(`Switched to ${this.state.darkMode ? 'dark' : 'light'} mode`);
        
        // Dispatch theme change event
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: this.state.darkMode ? 'dark' : 'light' }
        }));
    }

    handleSystemThemeChange() {
        // Only apply system theme if user hasn't set a preference
        if (!this.getSavedState('theme', null)) {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (systemPrefersDark) {
                this.enableDarkMode();
            } else {
                this.enableLightMode();
            }
        }
    }

    updateThemeColor(color) {
        let themeColorMeta = document.querySelector('meta[name="theme-color"]');
        if (!themeColorMeta) {
            themeColorMeta = document.createElement('meta');
            themeColorMeta.name = 'theme-color';
            document.head.appendChild(themeColorMeta);
        }
        themeColorMeta.content = color;
    }

    // ================================================================
    // Accessibility Features
    // ================================================================

    setupFocusManagement() {
        // Focus visible polyfill for better keyboard navigation
        this.addFocusVisiblePolyfill();

        // Tab trap for modals and sidebars
        this.setupTabTrap();

        // Focus restoration
        this.setupFocusRestoration();
    }

    addFocusVisiblePolyfill() {
        // Add focus-visible class for keyboard navigation
        const focusVisibleElements = document.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        focusVisibleElements.forEach(element => {
            element.addEventListener('focus', () => {
                if (this.isKeyboardFocus()) {
                    element.classList.add('focus-visible');
                }
            });

            element.addEventListener('blur', () => {
                element.classList.remove('focus-visible');
            });
        });
    }

    isKeyboardFocus() {
        // Simple heuristic to detect keyboard focus
        return !this.mouseUsed;
    }

    trapFocusInSidebar() {
        if (!this.sidebar) return;

        const focusableElements = this.sidebar.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        this.focusTrapHandler = (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                }
            }
        };

        document.addEventListener('keydown', this.focusTrapHandler);
        firstFocusable.focus();
    }

    releaseFocusTrap() {
        if (this.focusTrapHandler) {
            document.removeEventListener('keydown', this.focusTrapHandler);
            this.focusTrapHandler = null;
        }
    }

    createLiveRegion() {
        // Create ARIA live region for announcements
        this.liveRegion = document.createElement('div');
        this.liveRegion.setAttribute('aria-live', 'polite');
        this.liveRegion.setAttribute('aria-atomic', 'true');
        this.liveRegion.style.position = 'absolute';
        this.liveRegion.style.left = '-10000px';
        this.liveRegion.style.width = '1px';
        this.liveRegion.style.height = '1px';
        this.liveRegion.style.overflow = 'hidden';
        document.body.appendChild(this.liveRegion);
    }

    announceToScreenReader(message) {
        if (this.liveRegion) {
            this.liveRegion.textContent = '';
            setTimeout(() => {
                this.liveRegion.textContent = message;
            }, 10);
        }
    }

    announcePageChanges() {
        // Listen for navigation changes and announce them
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.target.matches('title')) {
                    const title = document.title;
                    this.announceToScreenReader(`Page loaded: ${title}`);
                }
            });
        });

        observer.observe(document.head, {
            childList: true,
            subtree: true
        });
    }

    // ================================================================
    // Keyboard Navigation
    // ================================================================

    handleKeyboardNavigation(e) {
        switch (e.key) {
            case 'Escape':
                this.handleEscapeKey(e);
                break;
            case 'Tab':
                this.handleTabKey(e);
                break;
            case 'F1':
                this.handleHelpKey(e);
                break;
            case '/':
                this.handleSearchKey(e);
                break;
        }

        // Custom keyboard shortcuts
        if (e.ctrlKey || e.metaKey) {
            this.handleKeyboardShortcuts(e);
        }
    }

    handleEscapeKey(e) {
        // Close any open dropdowns or modals
        if (this.state.sidebarOpen && this.state.isMobile) {
            e.preventDefault();
            this.closeSidebar();
        }

        // Close dropdowns
        const openDropdowns = document.querySelectorAll('[data-dropdown][aria-expanded="true"]');
        openDropdowns.forEach(dropdown => {
            dropdown.click();
        });
    }

    handleTabKey(e) {
        // Skip to main content functionality
        if (e.shiftKey && document.activeElement === document.body) {
            const skipLink = document.querySelector('.skip-link');
            if (skipLink) {
                skipLink.focus();
            }
        }
    }

    handleHelpKey(e) {
        e.preventDefault();
        // Open help dialog or navigate to help page
        window.dispatchEvent(new CustomEvent('open-help'));
    }

    handleSearchKey(e) {
        // Don't trigger if focus is in an input
        if (e.target.matches('input, textarea, [contenteditable]')) return;
        
        e.preventDefault();
        // Focus search input
        const searchInput = document.querySelector('[data-search-input]');
        if (searchInput) {
            searchInput.focus();
        }
    }

    handleKeyboardShortcuts(e) {
        const shortcuts = {
            'b': () => this.toggleSidebar(), // Ctrl/Cmd + B
            'd': () => this.toggleTheme(),   // Ctrl/Cmd + D
            'k': () => window.dispatchEvent(new CustomEvent('open-command-palette')) // Ctrl/Cmd + K
        };

        const shortcut = shortcuts[e.key.toLowerCase()];
        if (shortcut) {
            e.preventDefault();
            shortcut();
        }
    }

    // ================================================================
    // Performance Optimizations
    // ================================================================

    setupIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadLazyContent(entry.target);
                }
            });
        }, {
            rootMargin: '50px'
        });

        // Observe lazy-loading elements
        document.querySelectorAll('[data-lazy]').forEach(el => {
            this.observer.observe(el);
        });
    }

    loadLazyContent(element) {
        const src = element.dataset.lazy;
        if (src && element.tagName === 'IMG') {
            element.src = src;
            element.removeAttribute('data-lazy');
        }
        
        this.observer.unobserve(element);
    }

    preloadCriticalResources() {
        // Preload critical CSS and fonts
        const criticalResources = [
            { href: '/admin/css/critical.css', as: 'style' },
            { href: '/fonts/inter-var.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' }
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            Object.assign(link, resource);
            document.head.appendChild(link);
        });
    }

    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('ServiceWorker registered:', registration);
                })
                .catch(error => {
                    console.log('ServiceWorker registration failed:', error);
                });
        }
    }

    // ================================================================
    // Utility Methods
    // ================================================================

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    getSavedState(key, defaultValue) {
        try {
            const saved = localStorage.getItem(`dashboard-${key}`);
            return saved !== null ? JSON.parse(saved) : defaultValue;
        } catch {
            return defaultValue;
        }
    }

    saveState(key, value) {
        try {
            localStorage.setItem(`dashboard-${key}`, JSON.stringify(value));
        } catch (error) {
            console.warn('Could not save state to localStorage:', error);
        }
    }

    handleReducedMotion() {
        this.state.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        document.documentElement.classList.toggle('reduce-motion', this.state.reducedMotion);
    }

    handleHighContrast() {
        this.state.highContrast = window.matchMedia('(prefers-contrast: high)').matches;
        document.documentElement.classList.toggle('high-contrast', this.state.highContrast);
    }
}

// ================================================================
// Initialize Dashboard Layout
// ================================================================

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.dashboardLayout = new DashboardLayout();
    });
} else {
    window.dashboardLayout = new DashboardLayout();
}

// ================================================================
// Global Utilities & Helpers
// ================================================================

// Global event dispatcher for component communication
window.dashboardEvents = {
    emit(eventName, data = {}) {
        window.dispatchEvent(new CustomEvent(eventName, { detail: data }));
    },

    on(eventName, callback) {
        window.addEventListener(eventName, callback);
    },

    off(eventName, callback) {
        window.removeEventListener(eventName, callback);
    }
};

// Notification system
window.dashboardNotifications = {
    show(message, type = 'info', duration = 5000) {
        const notification = this.create(message, type);
        document.body.appendChild(notification);
        
        // Auto-remove after duration
        setTimeout(() => {
            this.remove(notification);
        }, duration);
        
        return notification;
    },

    create(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" aria-label="Close notification">×</button>
            </div>
        `;

        // Add close functionality
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.remove(notification);
        });

        return notification;
    },

    remove(notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
};

// Loading state management
window.dashboardLoading = {
    show(target = document.body) {
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Loading...</p>
            </div>
        `;
        target.appendChild(loader);
        return loader;
    },

    hide(loader) {
        if (loader && loader.parentNode) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.parentNode.removeChild(loader);
            }, 300);
        }
    }
};

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { DashboardLayout };
}
