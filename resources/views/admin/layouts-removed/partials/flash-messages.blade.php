<!-- Flash Messages Component -->
<div x-data="flashMessages()" class="space-y-2">
    <!-- Success Message -->
    @if(session('success'))
        <div class="flash-message bg-green-50 border border-green-200 rounded-lg p-4 flex items-start space-x-3"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             role="alert"
             aria-live="polite">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-green-800">Success</h3>
                <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
            </div>
            <button type="button" 
                    class="flex-shrink-0 ml-3 text-green-400 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-green-50 rounded-md p-1"
                    @click="show = false"
                    aria-label="Dismiss success message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="flash-message bg-red-50 border border-red-200 rounded-lg p-4 flex items-start space-x-3"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             role="alert"
             aria-live="assertive">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
            </div>
            <button type="button" 
                    class="flex-shrink-0 ml-3 text-red-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-50 rounded-md p-1"
                    @click="show = false"
                    aria-label="Dismiss error message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Warning Message -->
    @if(session('warning'))
        <div class="flash-message bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start space-x-3"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             role="alert"
             aria-live="polite">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-yellow-800">Warning</h3>
                <p class="mt-1 text-sm text-yellow-700">{{ session('warning') }}</p>
            </div>
            <button type="button" 
                    class="flex-shrink-0 ml-3 text-yellow-400 hover:text-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-yellow-50 rounded-md p-1"
                    @click="show = false"
                    aria-label="Dismiss warning message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Info Message -->
    @if(session('info'))
        <div class="flash-message bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start space-x-3"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             role="alert"
             aria-live="polite">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-medium text-blue-800">Information</h3>
                <p class="mt-1 text-sm text-blue-700">{{ session('info') }}</p>
            </div>
            <button type="button" 
                    class="flex-shrink-0 ml-3 text-blue-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-blue-50 rounded-md p-1"
                    @click="show = false"
                    aria-label="Dismiss info message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="flash-message bg-red-50 border border-red-200 rounded-lg p-4"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             role="alert"
             aria-live="assertive">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-red-800">
                        {{ $errors->count() === 1 ? 'There was a validation error:' : 'There were validation errors:' }}
                    </h3>
                    <div class="mt-2">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" 
                        class="flex-shrink-0 ml-3 text-red-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-red-50 rounded-md p-1"
                        @click="show = false"
                        aria-label="Dismiss validation errors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function flashMessages() {
    return {
        init() {
            // Auto-dismiss flash messages after 5 seconds
            setTimeout(() => {
                const flashMessages = document.querySelectorAll('.flash-message[x-data*="show: true"]');
                flashMessages.forEach(message => {
                    const alpineData = Alpine.$data(message);
                    if (alpineData && alpineData.show) {
                        alpineData.show = false;
                    }
                });
            }, 5000);
        }
    }
}

// Enhanced accessibility for flash messages
document.addEventListener('DOMContentLoaded', function() {
    // Focus management for error messages
    const errorMessages = document.querySelectorAll('[role="alert"][aria-live="assertive"]');
    if (errorMessages.length > 0) {
        // Focus the first error message for screen readers
        errorMessages[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Keyboard navigation for dismissing messages
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const visibleFlashMessages = document.querySelectorAll('.flash-message[x-show="show"]');
            visibleFlashMessages.forEach(message => {
                const dismissButton = message.querySelector('button[aria-label*="Dismiss"]');
                if (dismissButton) {
                    dismissButton.click();
                }
            });
        }
    });
});
</script>

<style>
/* Enhanced animation and styling for flash messages */
.flash-message {
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .flash-message {
        border-width: 2px;
    }
    
    .flash-message button:focus {
        outline: 3px solid currentColor;
        outline-offset: 2px;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .flash-message {
        animation: none;
    }
    
    .flash-message [x-transition] {
        transition: none !important;
    }
}

/* Focus management for better accessibility */
.flash-message:focus-within {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}
</style>
