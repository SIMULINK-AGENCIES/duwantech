// NotificationBell.js - Alpine.js component for real-time notification management
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', () => ({
        // State
        open: false,
        notifications: [],
        unreadCount: 0,
        loading: false,
        soundEnabled: true,
        lastNotificationId: null,
        
        // Pagination
        hasMore: false,
        currentPage: 1,
        
        // Sound
        notificationSound: null,

        // Initialize component
        init() {
            this.initializeSound();
            this.loadNotifications();
            this.setupWebSocketConnection();
            this.setupKeyboardShortcuts();
        },

        // Initialize notification sound
        initializeSound() {
            // Create notification sound (using Web Audio API)
            this.notificationSound = this.createNotificationTone();
        },

        // Create notification tone using Web Audio API
        createNotificationTone() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                
                return {
                    play: () => {
                        if (!this.soundEnabled) return;
                        
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                        
                        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                        gainNode.gain.linearRampToValueAtTime(0.2, audioContext.currentTime + 0.01);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                    }
                };
            } catch (error) {
                console.warn('Could not initialize notification sound:', error);
                return { play: () => {} };
            }
        },

        // Load notifications from API
        async loadNotifications(append = false) {
            this.loading = true;
            
            try {
                const response = await fetch('/admin/api/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load notifications');
                
                const data = await response.json();
                
                if (append) {
                    this.notifications = [...this.notifications, ...data.notifications];
                } else {
                    this.notifications = data.notifications;
                }
                
                this.unreadCount = data.unread_count;
                this.hasMore = data.has_more;
                
                // Update last notification ID for real-time updates
                if (this.notifications.length > 0) {
                    this.lastNotificationId = this.notifications[0].id;
                }
                
            } catch (error) {
                console.error('Error loading notifications:', error);
                this.showErrorToast('Failed to load notifications');
            } finally {
                this.loading = false;
            }
        },

        // Setup WebSocket connection for real-time updates
        setupWebSocketConnection() {
            if (typeof window.Echo !== 'undefined') {
                // Listen for new notifications
                window.Echo.private('admin-notifications')
                    .listen('NewNotificationEvent', (event) => {
                        this.handleNewNotification(event.notification);
                    });

                // Listen for notification updates
                window.Echo.private('admin-notifications')
                    .listen('NotificationUpdatedEvent', (event) => {
                        this.handleNotificationUpdate(event.notification);
                    });
            }
        },

        // Handle new notification from WebSocket
        handleNewNotification(notification) {
            // Add to beginning of array
            this.notifications.unshift(notification);
            this.unreadCount++;
            
            // Play sound
            this.playNotificationSound();
            
            // Show browser notification if permission granted
            this.showBrowserNotification(notification);
            
            // Animate bell
            this.animateBell();
        },

        // Handle notification update from WebSocket
        handleNotificationUpdate(updatedNotification) {
            const index = this.notifications.findIndex(n => n.id === updatedNotification.id);
            if (index !== -1) {
                this.notifications[index] = updatedNotification;
                
                // Update unread count
                this.updateUnreadCount();
            }
        },

        // Update unread count from current notifications
        updateUnreadCount() {
            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
        },

        // Toggle notification dropdown
        toggleDropdown() {
            this.open = !this.open;
            
            if (this.open) {
                // Load latest notifications when opening
                this.loadNotifications();
            }
        },

        // Mark notification as read
        async markAsRead(notificationId) {
            try {
                const response = await fetch(`/admin/api/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                
                if (!response.ok) throw new Error('Failed to mark as read');
                
                const data = await response.json();
                
                // Update notification in array
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.is_read = true;
                    this.unreadCount = data.unread_count;
                }
                
            } catch (error) {
                console.error('Error marking notification as read:', error);
                this.showErrorToast('Failed to mark notification as read');
            }
        },

        // Mark all notifications as read
        async markAllAsRead() {
            try {
                const response = await fetch('/admin/api/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                
                if (!response.ok) throw new Error('Failed to mark all as read');
                
                // Update all notifications
                this.notifications.forEach(notification => {
                    notification.is_read = true;
                });
                
                this.unreadCount = 0;
                this.showSuccessToast('All notifications marked as read');
                
            } catch (error) {
                console.error('Error marking all as read:', error);
                this.showErrorToast('Failed to mark all notifications as read');
            }
        },

        // Delete notification
        async deleteNotification(notificationId) {
            try {
                const response = await fetch(`/admin/api/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                
                if (!response.ok) throw new Error('Failed to delete notification');
                
                // Remove from array
                this.notifications = this.notifications.filter(n => n.id !== notificationId);
                this.updateUnreadCount();
                
                this.showSuccessToast('Notification deleted');
                
            } catch (error) {
                console.error('Error deleting notification:', error);
                this.showErrorToast('Failed to delete notification');
            }
        },

        // Handle notification click
        handleNotificationClick(notification) {
            // Mark as read if not already read
            if (!notification.is_read) {
                this.markAsRead(notification.id);
            }
            
            // Navigate to action URL if available
            if (notification.action_url) {
                window.location.href = notification.action_url;
            }
            
            // Close dropdown
            this.open = false;
        },

        // Play notification sound
        playNotificationSound() {
            if (this.notificationSound && this.soundEnabled) {
                this.notificationSound.play();
            }
        },

        // Show browser notification
        showBrowserNotification(notification) {
            if (Notification.permission === 'granted') {
                new Notification(notification.title, {
                    body: notification.message,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    tag: `notification-${notification.id}`,
                });
            }
        },

        // Animate notification bell
        animateBell() {
            const bell = this.$refs.bellIcon;
            if (bell) {
                bell.classList.add('animate-bounce');
                setTimeout(() => {
                    bell.classList.remove('animate-bounce');
                }, 1000);
            }
        },

        // Toggle sound preference
        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem('notificationSoundEnabled', this.soundEnabled);
            
            if (this.soundEnabled) {
                this.showSuccessToast('Notification sounds enabled');
            } else {
                this.showSuccessToast('Notification sounds disabled');
            }
        },

        // Setup keyboard shortcuts
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (event) => {
                // Alt + N to toggle notifications
                if (event.altKey && event.key === 'n') {
                    event.preventDefault();
                    this.toggleDropdown();
                }
                
                // Escape to close dropdown
                if (event.key === 'Escape' && this.open) {
                    this.open = false;
                }
            });
        },

        // Request notification permission
        async requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    this.showSuccessToast('Browser notifications enabled');
                }
            }
        },

        // Utility methods
        showSuccessToast(message) {
            // You can integrate with your toast notification system here
            console.log('Success:', message);
        },

        showErrorToast(message) {
            // You can integrate with your toast notification system here
            console.error('Error:', message);
        },

        // Format relative time
        formatTime(dateString) {
            return new Date(dateString).toLocaleString();
        },

        // Get notification icon based on type
        getNotificationIcon(type) {
            const icons = {
                order: '🛒',
                payment: '💳',
                inventory: '📦',
                user: '👤',
                system: '⚙️',
                info: 'ℹ️',
                warning: '⚠️',
                error: '❌',
                success: '✅'
            };
            return icons[type] || '📢';
        },

        // Get priority badge color
        getPriorityColor(priority) {
            const colors = {
                low: 'bg-gray-100 text-gray-800',
                medium: 'bg-blue-100 text-blue-800',
                high: 'bg-orange-100 text-orange-800',
                critical: 'bg-red-100 text-red-800'
            };
            return colors[priority] || colors.medium;
        }
    }));
});
