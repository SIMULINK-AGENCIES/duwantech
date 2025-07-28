<!-- Enhanced Loading States Component -->
<div class="loading-states">
    <!-- Skeleton Loading for Cards -->
    <div class="skeleton-card" x-show="loading" x-transition>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="animate-pulse">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gray-300 rounded-lg loading-skeleton"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-300 rounded loading-skeleton"></div>
                        <div class="h-3 bg-gray-300 rounded w-3/4 loading-skeleton"></div>
                    </div>
                </div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 bg-gray-300 rounded loading-skeleton"></div>
                    <div class="h-4 bg-gray-300 rounded w-5/6 loading-skeleton"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spinner Loading -->
    <div class="spinner-loading" x-show="loading" x-transition>
        <div class="flex items-center justify-center p-8">
            <div class="relative">
                <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                <div class="absolute inset-0 w-12 h-12 border-4 border-transparent border-t-blue-400 rounded-full animate-spin" style="animation-delay: 0.15s;"></div>
            </div>
        </div>
    </div>

    <!-- Dots Loading -->
    <div class="dots-loading" x-show="loading" x-transition>
        <div class="flex items-center justify-center space-x-2 p-4">
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce"></div>
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
            <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
        </div>
    </div>

    <!-- Progress Bar Loading -->
    <div class="progress-loading" x-show="loading" x-transition>
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full animate-pulse" 
                 style="width: 60%; animation: progressBar 2s ease-in-out infinite;"></div>
        </div>
        <p class="text-sm text-gray-600 mt-2 text-center">Loading data<span class="loading-dots">...</span></p>
    </div>

    <!-- Shimmer Loading for Tables -->
    <div class="table-skeleton" x-show="loading" x-transition>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="h-6 bg-gray-300 rounded w-1/4 loading-skeleton"></div>
            </div>
            <div class="divide-y divide-gray-200">
                <template x-for="i in 5" :key="i">
                    <div class="px-6 py-4 flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-300 rounded-full loading-skeleton"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-300 rounded w-3/4 loading-skeleton"></div>
                            <div class="h-3 bg-gray-300 rounded w-1/2 loading-skeleton"></div>
                        </div>
                        <div class="w-20 h-4 bg-gray-300 rounded loading-skeleton"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Chart Loading -->
    <div class="chart-skeleton" x-show="loading" x-transition>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-6 bg-gray-300 rounded w-1/3 loading-skeleton"></div>
                <div class="h-4 bg-gray-300 rounded w-16 loading-skeleton"></div>
            </div>
            <div class="h-64 bg-gray-100 rounded relative overflow-hidden">
                <div class="absolute inset-0 loading-skeleton"></div>
                <div class="absolute bottom-0 left-0 right-0 flex justify-between items-end p-4">
                    <div class="w-8 bg-gray-300 rounded-t" style="height: 60%;"></div>
                    <div class="w-8 bg-gray-300 rounded-t" style="height: 80%;"></div>
                    <div class="w-8 bg-gray-300 rounded-t" style="height: 40%;"></div>
                    <div class="w-8 bg-gray-300 rounded-t" style="height: 90%;"></div>
                    <div class="w-8 bg-gray-300 rounded-t" style="height: 70%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Loading States -->
    <div class="button-loading-states">
        <button class="btn-loading" :disabled="loading" :class="{'opacity-50 cursor-not-allowed': loading}">
            <span x-show="!loading" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Action
            </span>
            <span x-show="loading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </div>

    <!-- Full Screen Loading Overlay -->
    <div class="loading-overlay" x-show="loading" x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 shadow-2xl">
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                        <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-t-blue-400 rounded-full animate-spin" style="animation-delay: 0.15s;"></div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900">Loading...</h3>
                        <p class="text-sm text-gray-500 mt-1">Please wait while we process your request</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error States -->
    <div class="state-indicators">
        <!-- Success State -->
        <div class="success-state" x-show="success" x-transition>
            <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600 success-checkmark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Success!</p>
                    <p class="text-sm text-green-700 mt-1">Operation completed successfully.</p>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div class="error-state" x-show="error" x-transition>
            <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-lg error-shake">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">Error!</p>
                    <p class="text-sm text-red-700 mt-1" x-text="errorMessage || 'Something went wrong. Please try again.'"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional loading state styles */
.btn-loading {
    @apply px-4 py-2 bg-blue-600 text-white rounded-lg font-medium transition-all duration-200;
}

.btn-loading:hover:not(:disabled) {
    @apply bg-blue-700 transform translateY(-1px) shadow-lg;
}

.loading-overlay {
    backdrop-filter: blur(4px);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .loading-overlay .bg-white {
        @apply mx-4 w-full max-w-sm;
    }
    
    .skeleton-card {
        @apply mx-4;
    }
}
</style>
