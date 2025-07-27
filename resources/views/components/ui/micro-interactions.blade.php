<!-- Micro-Interactions Component -->
<div class="micro-interactions">
    <!-- Interactive Buttons -->
    <div class="interactive-buttons space-y-4">
        <!-- Primary Button with Ripple Effect -->
        <button class="btn-ripple relative overflow-hidden bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:bg-blue-700 hover:shadow-lg hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                @click="createRipple($event)">
            <span class="relative z-10">Click Me</span>
        </button>

        <!-- Floating Action Button -->
        <button class="fab group relative w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2"
                title="Add New Item">
            <svg class="w-6 h-6 text-white transition-transform duration-300 group-hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>

        <!-- Toggle Switch -->
        <div class="toggle-switch">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer" @change="toggleSwitch">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-transform duration-300 peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-gray-700">Enable Notifications</span>
            </label>
        </div>
    </div>

    <!-- Interactive Cards -->
    <div class="interactive-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="card-hover group bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-blue-200 cursor-pointer">
            <div class="relative overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 transition-transform duration-500 group-hover:scale-105"></div>
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">Interactive Card</h3>
                <p class="text-gray-600 mt-2">Hover to see the micro-interactions in action</p>
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button class="text-blue-600 font-medium hover:text-blue-700">Learn More →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Form Elements -->
    <div class="interactive-forms space-y-6">
        <!-- Floating Label Input -->
        <div class="floating-input relative">
            <input type="text" id="floating-email" class="peer w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-transparent" placeholder="Enter your email" />
            <label for="floating-email" class="absolute left-4 -top-2.5 bg-white px-2 text-sm text-gray-600 transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-blue-600">
                Email Address
            </label>
        </div>

        <!-- Progress Input -->
        <div class="progress-input">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password Strength</label>
            <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" 
                   @input="updatePasswordStrength($event.target.value)">
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full transition-all duration-500 rounded-full" 
                     :class="passwordStrength.color" 
                     :style="`width: ${passwordStrength.width}%`"></div>
            </div>
            <p class="text-xs text-gray-500 mt-1" x-text="passwordStrength.text"></p>
        </div>

        <!-- Multi-step Progress -->
        <div class="multi-step-progress">
            <div class="flex items-center justify-between mb-4">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center" :class="{'flex-1': index < steps.length - 1}">
                        <div class="relative">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300"
                                 :class="step.completed ? 'bg-green-500 text-white' : step.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600'">
                                <span x-show="!step.completed" x-text="index + 1"></span>
                                <svg x-show="step.completed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div x-show="index < steps.length - 1" class="flex-1 h-1 mx-4 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 transition-transform duration-500 origin-left"
                                 :class="step.completed ? 'scale-x-100' : 'scale-x-0'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Interactive List Items -->
    <div class="interactive-lists">
        <div class="space-y-2">
            <template x-for="(item, index) in listItems" :key="index">
                <div class="list-item group flex items-center p-4 bg-white border border-gray-200 rounded-lg transition-all duration-200 hover:bg-gray-50 hover:border-blue-200 hover:shadow-md cursor-pointer"
                     @click="toggleListItem(index)">
                    <div class="flex-shrink-0 w-5 h-5 mr-3">
                        <div class="w-full h-full rounded border-2 transition-all duration-200 flex items-center justify-center"
                             :class="item.selected ? 'bg-blue-500 border-blue-500' : 'border-gray-300 group-hover:border-blue-400'">
                            <svg x-show="item.selected" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-200" x-text="item.title"></h4>
                        <p class="text-sm text-gray-500" x-text="item.description"></p>
                    </div>
                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Notification Toast -->
    <div class="notification-container fixed top-4 right-4 z-50 space-y-2">
        <template x-for="notification in notifications" :key="notification.id">
            <div class="notification max-w-sm bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden transform transition-all duration-300"
                 x-show="notification.visible"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center"
                                 :class="notification.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                <svg x-show="notification.type === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <svg x-show="notification.type === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                            <p class="text-sm text-gray-500 mt-1" x-text="notification.message"></p>
                        </div>
                        <button @click="dismissNotification(notification.id)" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="h-1 bg-gray-100">
                    <div class="h-full transition-all duration-300 origin-left"
                         :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
                         :style="`width: ${notification.progress}%`"></div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
// Micro-interactions JavaScript
function createRipple(event) {
    const button = event.currentTarget;
    const rect = button.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;
    
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Micro-interactions Alpine.js data
document.addEventListener('alpine:init', () => {
    Alpine.data('microInteractions', () => ({
        passwordStrength: {
            width: 0,
            color: 'bg-gray-300',
            text: 'Enter a password'
        },
        steps: [
            { active: true, completed: false },
            { active: false, completed: false },
            { active: false, completed: false },
            { active: false, completed: false }
        ],
        listItems: [
            { title: 'Dashboard Analytics', description: 'View comprehensive analytics data', selected: false },
            { title: 'User Management', description: 'Manage user accounts and permissions', selected: true },
            { title: 'Sales Reports', description: 'Generate detailed sales reports', selected: false },
            { title: 'System Settings', description: 'Configure system preferences', selected: false }
        ],
        notifications: [],
        notificationId: 0,

        updatePasswordStrength(password) {
            let strength = 0;
            let color = 'bg-red-400';
            let text = 'Weak password';

            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;

            if (strength >= 75) {
                color = 'bg-green-400';
                text = 'Strong password';
            } else if (strength >= 50) {
                color = 'bg-yellow-400';
                text = 'Medium password';
            } else if (strength >= 25) {
                color = 'bg-orange-400';
                text = 'Fair password';
            }

            this.passwordStrength = { width: strength, color, text };
        },

        toggleListItem(index) {
            this.listItems[index].selected = !this.listItems[index].selected;
        },

        showNotification(type, title, message) {
            const id = ++this.notificationId;
            const notification = {
                id,
                type,
                title,
                message,
                visible: true,
                progress: 100
            };

            this.notifications.push(notification);

            // Auto-dismiss after 5 seconds
            const interval = setInterval(() => {
                notification.progress -= 2;
                if (notification.progress <= 0) {
                    this.dismissNotification(id);
                    clearInterval(interval);
                }
            }, 100);
        },

        dismissNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications[index].visible = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300);
            }
        }
    }));
});
</script>

<style>
/* Ripple effect */
.btn-ripple .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    pointer-events: none;
    width: 40px;
    height: 40px;
    margin-left: -20px;
    margin-top: -20px;
    animation: ripple-animation 0.6s ease-out;
}

@keyframes ripple-animation {
    from {
        opacity: 1;
        transform: scale(0);
    }
    to {
        opacity: 0;
        transform: scale(4);
    }
}

/* Floating Action Button */
.fab {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 40;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .fab {
        bottom: 1rem;
        right: 1rem;
        width: 48px;
        height: 48px;
    }
    
    .notification-container {
        top: 1rem;
        right: 1rem;
        left: 1rem;
    }
    
    .notification {
        max-width: none;
    }
}
</style>
