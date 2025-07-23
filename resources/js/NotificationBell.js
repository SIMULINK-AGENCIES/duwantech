/**
 * Notification Bell Component
 * Handles real-time notifications with bell icon, badge, and dropdown
 */
class NotificationBell {
    constructor() {
        this.bellElement = document.getElementById('notification-bell');
        this.badgeElement = document.getElementById('notification-badge');
        this.dropdownElement = document.getElementById('notification-dropdown');
        this.listElement = document.getElementById('notification-list');
        this.countElement = document.getElementById('notification-count');
        this.markAllReadBtn = document.getElementById('mark-all-read');
        
        this.isOpen = false;
        this.notifications = [];
        this.unreadCount = 0;
        this.hasUrgent = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadInitialNotifications();
        this.setupRealTimeUpdates();
        this.startPolling();
    }

    setupEventListeners() {
        // Bell click to toggle dropdown
        if (this.bellElement) {
            this.bellElement.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
        }

        // Mark all as read button
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', () => {
                this.markAllAsRead();
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.bellElement?.contains(e.target) && !this.dropdownElement?.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeDropdown();
            }
        });
    }

    async loadInitialNotifications() {
        try {
            const response = await fetch('/admin/notifications/dropdown');
            const data = await response.json();
            
            this.notifications = data.notifications;
            this.unreadCount = data.unread_count;
            this.hasUrgent = data.has_urgent;
            
            this.updateBellUI();
            this.renderNotifications();
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    setupRealTimeUpdates() {
        // Subscribe to notification channel
        if (window.Echo) {
            window.Echo.private('admin-notifications')
                .listen('.new-notification', (data) => {
                    this.handleNewNotification(data);
                })
                .listen('.notification-read', (data) => {
                    this.handleNotificationRead(data);
                })
                .listen('.notification-deleted', (data) => {
                    this.handleNotificationDeleted(data);
                });
        }
    }

    handleNewNotification(data) {
        // Add new notification to the beginning of the list
        this.notifications.unshift(data);
        
        // Keep only latest 10 notifications in dropdown
        if (this.notifications.length > 10) {
            this.notifications = this.notifications.slice(0, 10);
        }
        
        // Update counts
        if (!data.is_read) {
            this.unreadCount++;
        }
        
        if (data.priority === 'high') {
            this.hasUrgent = true;
        }
        
        this.updateBellUI();
        this.renderNotifications();
        this.showNotificationToast(data);
        this.playNotificationSound(data);
    }

    handleNotificationRead(data) {
        // Update notification in list
        const notification = this.notifications.find(n => n.id === data.id);
        if (notification && !notification.is_read) {
            notification.is_read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
            
            this.updateBellUI();
            this.renderNotifications();
        }
    }

    handleNotificationDeleted(data) {
        // Remove notification from list
        const index = this.notifications.findIndex(n => n.id === data.id);
        if (index !== -1) {
            const notification = this.notifications[index];
            if (!notification.is_read) {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
            
            this.notifications.splice(index, 1);
            this.updateBellUI();
            this.renderNotifications();
        }
    }

    toggleDropdown() {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    openDropdown() {
        if (!this.dropdownElement) return;
        
        this.isOpen = true;
        this.dropdownElement.classList.remove('hidden');
        this.dropdownElement.classList.add('opacity-100', 'scale-100');
        
        // Focus first notification for keyboard navigation
        const firstNotification = this.dropdownElement.querySelector('[role="menuitem"]');
        if (firstNotification) {
            firstNotification.focus();
        }
        
        // Load fresh notifications when opening
        this.loadInitialNotifications();
    }

    closeDropdown() {
        if (!this.dropdownElement) return;
        
        this.isOpen = false;
        this.dropdownElement.classList.add('hidden');
        this.dropdownElement.classList.remove('opacity-100', 'scale-100');
    }

    updateBellUI() {
        // Update badge
        if (this.badgeElement) {
            if (this.unreadCount > 0) {
                this.badgeElement.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                this.badgeElement.classList.remove('hidden');
                
                // Add urgent styling for high priority notifications
                if (this.hasUrgent) {
                    this.badgeElement.classList.add('bg-red-500', 'animate-pulse');
                    this.badgeElement.classList.remove('bg-blue-500');
                } else {
                    this.badgeElement.classList.add('bg-blue-500');
                    this.badgeElement.classList.remove('bg-red-500', 'animate-pulse');
                }
            } else {
                this.badgeElement.classList.add('hidden');
                this.badgeElement.classList.remove('animate-pulse');
            }
        }

        // Update bell icon animation
        if (this.bellElement && this.unreadCount > 0) {
            this.bellElement.classList.add('text-blue-600');
            
            if (this.hasUrgent) {
                this.bellElement.classList.add('animate-bounce');
            }
        } else if (this.bellElement) {
            this.bellElement.classList.remove('text-blue-600', 'animate-bounce');
        }

        // Update count in dropdown header
        if (this.countElement) {
            this.countElement.textContent = this.unreadCount;
        }
    }

    renderNotifications() {
        if (!this.listElement) return;

        if (this.notifications.length === 0) {
            this.listElement.innerHTML = `
                <div class="p-6 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm">No notifications</p>
                </div>
            `;
            return;
        }

        const html = this.notifications.map(notification => this.renderNotificationItem(notification)).join('');
        this.listElement.innerHTML = html;

        // Add click listeners to notification items
        this.listElement.querySelectorAll('.notification-item').forEach(item => {
            const notificationId = item.dataset.notificationId;
            
            item.addEventListener('click', () => {
                this.markAsRead(notificationId);
            });

            // Delete button
            const deleteBtn = item.querySelector('.delete-notification');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.deleteNotification(notificationId);
                });
            }
        });
    }

    renderNotificationItem(notification) {
        const timeAgo = this.formatTimeAgo(notification.created_at);
        const isUnread = !notification.is_read;
        
        return `
            <div class="notification-item p-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer transition-colors duration-200 ${isUnread ? 'bg-blue-50' : ''}" 
                 data-notification-id="${notification.id}" 
                 role="menuitem">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${notification.color}">
                            ${notification.icon}
                        </div>
                        ${isUnread ? '<span class="absolute top-0 right-0 w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate ${isUnread ? 'font-semibold' : ''}">${notification.title}</p>
                            <button class="delete-notification text-gray-400 hover:text-red-500 transition-colors duration-200 opacity-0 group-hover:opacity-100" 
                                    title="Delete notification">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">${notification.message}</p>
                        
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500">${timeAgo}</span>
                            ${notification.priority === 'high' ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Urgent</span>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                // Update local state
                const notification = this.notifications.find(n => n.id == notificationId);
                if (notification && !notification.is_read) {
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateBellUI();
                    this.renderNotifications();
                }
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                // Update local state
                this.notifications.forEach(notification => {
                    notification.is_read = true;
                });
                this.unreadCount = 0;
                this.hasUrgent = false;
                this.updateBellUI();
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    async deleteNotification(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });

            if (response.ok) {
                this.handleNotificationDeleted({ id: parseInt(notificationId) });
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    }

    showNotificationToast(notification) {
        // Create toast notification for new notifications
        if (document.getElementById('notification-toast')) return; // Prevent duplicates

        const toast = document.createElement('div');
        toast.id = 'notification-toast';
        toast.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border border-gray-200 transform translate-x-full transition-transform duration-300 ease-in-out`;
        
        toast.innerHTML = `
            <div class="p-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center ${notification.color}">
                            ${notification.icon}
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                    </div>
                    <button class="close-toast text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        const removeToast = () => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        };

        setTimeout(removeToast, 5000);

        // Close button
        toast.querySelector('.close-toast').addEventListener('click', removeToast);
    }

    playNotificationSound(notification) {
        // Play notification sound for urgent notifications
        if (notification.priority === 'high' && 'AudioContext' in window || 'webkitAudioContext' in window) {
            try {
                // Create a short beep sound using Web Audio API
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // 800Hz tone
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime); // 30% volume
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2); // Fade out
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2); // 200ms duration
            } catch (error) {
                // Sound not available, ignore
                console.log('Audio notification not available');
            }
        }
    }

    startPolling() {
        // Fallback polling every 30 seconds in case websockets fail
        setInterval(() => {
            this.loadInitialNotifications();
        }, 30000);
    }

    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMinutes = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMinutes < 1) return 'Just now';
        if (diffMinutes < 60) return `${diffMinutes}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        
        return date.toLocaleDateString();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.notificationBell = new NotificationBell();
});

export default NotificationBell;
