/**
 * Activity Feed Alpine.js Component
 * 
 * Features:
 * - Real-time activity updates via WebSocket
 * - Activity filtering and pagination
 * - Professional UI with activity icons and colors
 * - Smooth animations for new activities
 * - Auto-refresh and manual refresh options
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('activityFeed', (options = {}) => ({
        // Configuration
        config: {
            maxActivities: options.maxActivities || 50,
            autoRefresh: options.autoRefresh !== false,
            refreshInterval: options.refreshInterval || 30000,
            showFilters: options.showFilters !== false,
            compact: options.compact || false,
            ...options
        },
        
        // State
        activities: [],
        stats: {
            today: { total: 0, logins: 0, orders: 0, payments: 0 },
            this_week: { total: 0, avg_per_day: 0 },
            top_actions: []
        },
        
        // UI State
        loading: true,
        error: null,
        connected: false,
        lastActivityId: 0,
        
        // Filters
        filters: {
            action: 'all',
            user_id: '',
            date_from: '',
            date_to: '',
            priority: 'all'
        },
        
        activityTypes: {
            'all': 'All Activities',
            'login': 'User Logins',
            'logout': 'User Logouts',
            'registration': 'New Registrations',
            'order_created': 'Orders Created',
            'order_updated': 'Orders Updated',
            'payment_completed': 'Payments Completed',
            'product_viewed': 'Products Viewed'
        },
        
        // WebSocket
        pusher: null,
        channel: null,
        refreshTimer: null,
        
        // Initialize component
        init() {
            console.log('🚀 Initializing Activity Feed...');
            
            // Load initial data
            this.loadActivities();
            
            // Setup WebSocket connection
            this.setupWebSocket();
            
            // Setup auto-refresh
            if (this.config.autoRefresh) {
                this.setupAutoRefresh();
            }
            
            // Watch for filter changes
            this.$watch('filters', () => {
                this.loadActivities();
            }, { deep: true });
            
            // Cleanup on destroy
            this.$watch('$el', (el) => {
                if (!el) {
                    this.cleanup();
                }
            });
        },
        
        // Load activities from API
        async loadActivities(append = false) {
            try {
                if (!append) {
                    this.loading = true;
                    this.error = null;
                }
                
                const params = new URLSearchParams();
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key] && this.filters[key] !== 'all') {
                        params.append(key, this.filters[key]);
                    }
                });
                
                const response = await fetch(`/admin/activity?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (append) {
                    this.activities = [...this.activities, ...data.activities.data];
                } else {
                    this.activities = data.activities.data || [];
                    this.stats = data.stats || this.stats;
                }
                
                // Update last activity ID for real-time updates
                if (this.activities.length > 0) {
                    this.lastActivityId = Math.max(...this.activities.map(a => a.id));
                }
                
                console.log('✅ Activities loaded:', this.activities.length);
                
            } catch (error) {
                console.error('❌ Failed to load activities:', error);
                this.error = 'Failed to load activities';
            } finally {
                this.loading = false;
            }
        },
        
        // Load new activities for real-time updates
        async loadNewActivities() {
            try {
                const response = await fetch(`/admin/api/live-activities?last_id=${this.lastActivityId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.activities.length > 0) {
                    // Add new activities to the top
                    this.activities = [...data.activities, ...this.activities];
                    
                    // Limit total activities to prevent memory issues
                    if (this.activities.length > this.config.maxActivities) {
                        this.activities = this.activities.slice(0, this.config.maxActivities);
                    }
                    
                    // Update stats
                    this.stats = data.stats || this.stats;
                    this.lastActivityId = data.last_id;
                    
                    // Animate new activities
                    this.$nextTick(() => {
                        data.activities.forEach((_, index) => {
                            const element = this.$refs.activityList?.children[index];
                            if (element) {
                                element.classList.add('animate-pulse-once');
                                setTimeout(() => {
                                    element.classList.remove('animate-pulse-once');
                                }, 1000);
                            }
                        });
                    });
                    
                    console.log('🔄 New activities loaded:', data.activities.length);
                }
                
            } catch (error) {
                console.error('❌ Failed to load new activities:', error);
            }
        },
        
        // Setup WebSocket connection
        setupWebSocket() {
            try {
                if (!window.pusherConfig) {
                    console.warn('⚠️ Pusher config not found, skipping WebSocket setup');
                    return;
                }
                
                this.pusher = new Pusher(window.pusherConfig.key, {
                    cluster: window.pusherConfig.cluster,
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    }
                });
                
                this.channel = this.pusher.subscribe('private-admin-monitoring');
                
                // Connection handlers
                this.pusher.connection.bind('connected', () => {
                    console.log('🔗 Activity Feed WebSocket connected');
                    this.connected = true;
                });
                
                this.pusher.connection.bind('disconnected', () => {
                    console.log('🔌 Activity Feed WebSocket disconnected');
                    this.connected = false;
                });
                
                // Listen for new activity events
                this.channel.bind('activity.new', (data) => {
                    console.log('📢 New activity received:', data);
                    this.handleNewActivity(data.activity);
                });
                
            } catch (error) {
                console.error('❌ Failed to setup WebSocket:', error);
            }
        },
        
        // Handle new activity from WebSocket
        handleNewActivity(activity) {
            // Check if activity already exists
            const exists = this.activities.some(a => a.id === activity.id);
            if (exists) return;
            
            // Add to top of list
            this.activities.unshift(activity);
            
            // Limit activities
            if (this.activities.length > this.config.maxActivities) {
                this.activities.pop();
            }
            
            // Update last activity ID
            this.lastActivityId = Math.max(this.lastActivityId, activity.id);
            
            // Animate new activity
            this.$nextTick(() => {
                const element = this.$refs.activityList?.children[0];
                if (element) {
                    element.classList.add('animate-slide-in-top');
                    setTimeout(() => {
                        element.classList.remove('animate-slide-in-top');
                    }, 500);
                }
            });
        },
        
        // Setup auto-refresh
        setupAutoRefresh() {
            this.refreshTimer = setInterval(() => {
                if (!this.connected && !this.loading) {
                    this.loadNewActivities();
                }
            }, this.config.refreshInterval);
        },
        
        // Manual refresh
        async refresh() {
            await this.loadActivities();
        },
        
        // Clear filters
        clearFilters() {
            this.filters = {
                action: 'all',
                user_id: '',
                date_from: '',
                date_to: '',
                priority: 'all'
            };
        },
        
        // Format relative time
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
            if (diff < 86400000) return `${Math.floor(diff / 3600000)}h ago`;
            return date.toLocaleDateString();
        },
        
        // Get activity icon SVG
        getActivityIconSvg(icon) {
            const icons = {
                'login': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
                'logout': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>',
                'user-plus': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>',
                'shopping-bag': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>',
                'credit-card': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
                'check-circle': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                'x-circle': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                'eye': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>',
                'activity': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>'
            };
            
            return icons[icon] || icons['activity'];
        },
        
        // Get activity color classes
        getActivityColorClasses(color) {
            const colors = {
                'green': 'bg-green-100 text-green-600',
                'blue': 'bg-blue-100 text-blue-600',
                'indigo': 'bg-indigo-100 text-indigo-600',
                'purple': 'bg-purple-100 text-purple-600',
                'yellow': 'bg-yellow-100 text-yellow-600',
                'red': 'bg-red-100 text-red-600',
                'gray': 'bg-gray-100 text-gray-600',
                'orange': 'bg-orange-100 text-orange-600',
                'pink': 'bg-pink-100 text-pink-600'
            };
            
            return colors[color] || colors['gray'];
        },
        
        // Cleanup
        cleanup() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
            
            if (this.channel) {
                this.channel.unbind_all();
                this.pusher.unsubscribe('private-admin-monitoring');
            }
            
            if (this.pusher) {
                this.pusher.disconnect();
            }
        },
        
        // Computed properties
        get filteredActivitiesCount() {
            return this.activities.length;
        },
        
        get hasFilters() {
            return Object.values(this.filters).some(value => value && value !== 'all');
        },
        
        get connectionStatus() {
            if (this.loading) return 'Loading...';
            if (this.error) return 'Error';
            if (this.connected) return 'Live';
            return 'Offline';
        },
        
        get connectionStatusClass() {
            if (this.loading) return 'text-gray-500';
            if (this.error) return 'text-red-500';
            if (this.connected) return 'text-green-500';
            return 'text-yellow-500';
        }
    }));
});
