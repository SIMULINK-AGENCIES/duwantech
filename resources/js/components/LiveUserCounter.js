/**
 * Live User Counter Alpine.js Component
 * 
 * Features:
 * - Real-time user count updates via WebSocket
 * - Smooth number animations
 * - Responsive design with mobile support
 * - Fallback for WebSocket failures
 * - Loading states and error handling
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('liveUserCounter', () => ({
        // State
        stats: {
            active_users: 0,
            authenticated_users: 0,
            guest_users: 0,
            users_today: 0,
            sessions_today: 0,
            timestamp: null
        },
        
        previousStats: {
            active_users: 0,
            authenticated_users: 0,
            guest_users: 0
        },
        
        loading: true,
        error: null,
        connected: false,
        retryCount: 0,
        maxRetries: 5,
        
        // Animation properties
        animationDuration: 500,
        countUpInterval: null,
        
        // Pusher/WebSocket connection
        pusher: null,
        channel: null,
        
        // Initialize component
        init() {
            console.log('🚀 Initializing Live User Counter...');
            
            // Load initial data
            this.loadInitialData();
            
            // Setup WebSocket connection
            this.setupWebSocket();
            
            // Setup fallback polling
            this.setupFallbackPolling();
            
            // Cleanup on destroy
            this.$watch('$el', (el) => {
                if (!el) {
                    this.cleanup();
                }
            });
        },
        
        // Load initial statistics
        async loadInitialData() {
            try {
                this.loading = true;
                this.error = null;
                
                const response = await fetch('/admin/api/live-stats', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                this.updateStats(data.stats);
                
                console.log('✅ Initial data loaded:', data.stats);
                
            } catch (error) {
                console.error('❌ Failed to load initial data:', error);
                this.error = 'Failed to load user statistics';
            } finally {
                this.loading = false;
            }
        },
        
        // Setup WebSocket connection
        setupWebSocket() {
            try {
                // Initialize Pusher (replace with your actual config)
                this.pusher = new Pusher(window.pusherConfig?.key || 'local', {
                    cluster: window.pusherConfig?.cluster || 'mt1',
                    encrypted: true,
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    }
                });
                
                // Subscribe to admin monitoring channel
                this.channel = this.pusher.subscribe('private-admin-monitoring');
                
                // Connection event handlers
                this.pusher.connection.bind('connected', () => {
                    console.log('🔗 WebSocket connected');
                    this.connected = true;
                    this.retryCount = 0;
                    this.error = null;
                });
                
                this.pusher.connection.bind('disconnected', () => {
                    console.log('🔌 WebSocket disconnected');
                    this.connected = false;
                });
                
                this.pusher.connection.bind('error', (error) => {
                    console.error('🚫 WebSocket error:', error);
                    this.connected = false;
                    this.handleConnectionError();
                });
                
                // Event listeners
                this.channel.bind('user.online', (data) => {
                    console.log('👤 User online:', data);
                    this.handleUserOnline(data);
                });
                
                this.channel.bind('user.offline', (data) => {
                    console.log('👤 User offline:', data);
                    this.handleUserOffline(data);
                });
                
            } catch (error) {
                console.error('❌ Failed to setup WebSocket:', error);
                this.error = 'WebSocket connection failed';
                this.setupFallbackPolling();
            }
        },
        
        // Handle user online event
        handleUserOnline(data) {
            if (data.total_active_users !== undefined) {
                this.animateCountUp('active_users', data.total_active_users);
                
                // Update other stats if provided
                if (data.stats) {
                    this.updateStats(data.stats);
                } else {
                    // Increment authenticated/guest based on user data
                    if (data.user) {
                        this.animateCountUp('authenticated_users', this.stats.authenticated_users + 1);
                    } else {
                        this.animateCountUp('guest_users', this.stats.guest_users + 1);
                    }
                }
                
                this.stats.timestamp = data.timestamp;
            }
        },
        
        // Handle user offline event
        handleUserOffline(data) {
            if (data.total_active_users !== undefined) {
                this.animateCountUp('active_users', data.total_active_users);
                
                // Decrement authenticated/guest based on user data
                if (data.user) {
                    this.animateCountUp('authenticated_users', Math.max(0, this.stats.authenticated_users - 1));
                } else {
                    this.animateCountUp('guest_users', Math.max(0, this.stats.guest_users - 1));
                }
                
                this.stats.timestamp = data.timestamp;
            }
        },
        
        // Update statistics with animation
        updateStats(newStats) {
            this.previousStats = { ...this.stats };
            
            // Animate each counter
            Object.keys(newStats).forEach(key => {
                if (typeof newStats[key] === 'number' && this.stats[key] !== newStats[key]) {
                    this.animateCountUp(key, newStats[key]);
                } else {
                    this.stats[key] = newStats[key];
                }
            });
        },
        
        // Animate count up effect
        animateCountUp(property, targetValue) {
            const startValue = this.stats[property] || 0;
            const difference = targetValue - startValue;
            const duration = this.animationDuration;
            const steps = 30;
            const stepValue = difference / steps;
            const stepDuration = duration / steps;
            
            let currentStep = 0;
            
            if (this.countUpInterval) {
                clearInterval(this.countUpInterval);
            }
            
            this.countUpInterval = setInterval(() => {
                currentStep++;
                
                if (currentStep >= steps) {
                    this.stats[property] = targetValue;
                    clearInterval(this.countUpInterval);
                    this.countUpInterval = null;
                } else {
                    this.stats[property] = Math.round(startValue + (stepValue * currentStep));
                }
            }, stepDuration);
        },
        
        // Setup fallback polling for when WebSocket fails
        setupFallbackPolling() {
            setInterval(() => {
                if (!this.connected && !this.loading) {
                    console.log('🔄 Fallback polling...');
                    this.loadInitialData();
                }
            }, 30000); // Poll every 30 seconds
        },
        
        // Handle connection errors
        handleConnectionError() {
            this.retryCount++;
            
            if (this.retryCount <= this.maxRetries) {
                console.log(`🔄 Retrying connection (${this.retryCount}/${this.maxRetries})...`);
                
                setTimeout(() => {
                    this.setupWebSocket();
                }, Math.pow(2, this.retryCount) * 1000); // Exponential backoff
            } else {
                this.error = 'Connection failed. Using fallback updates.';
            }
        },
        
        // Manual refresh
        async refresh() {
            await this.loadInitialData();
        },
        
        // Cleanup
        cleanup() {
            if (this.countUpInterval) {
                clearInterval(this.countUpInterval);
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
        get totalUsers() {
            return this.stats.authenticated_users + this.stats.guest_users;
        },
        
        get userPercentage() {
            const total = this.totalUsers;
            return total > 0 ? {
                authenticated: Math.round((this.stats.authenticated_users / total) * 100),
                guest: Math.round((this.stats.guest_users / total) * 100)
            } : { authenticated: 0, guest: 0 };
        },
        
        get statusClass() {
            if (this.loading) return 'text-gray-500';
            if (this.error) return 'text-red-500';
            if (this.connected) return 'text-green-500';
            return 'text-yellow-500';
        },
        
        get statusText() {
            if (this.loading) return 'Loading...';
            if (this.error) return this.error;
            if (this.connected) return 'Live';
            return 'Offline';
        },
        
        get pulseClass() {
            return this.connected && !this.loading ? 'animate-pulse' : '';
        }
    }));
});
