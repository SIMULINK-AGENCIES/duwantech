{{-- Live User Counter Widget --}}
<div x-data="liveUserCounter" 
     class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
     
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                    </path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Live Users</h3>
                <p class="text-sm text-gray-500">Currently online</p>
            </div>
        </div>
        
        {{-- Status Indicator --}}
        <div class="flex items-center space-x-2">
            <div class="flex items-center space-x-1">
                <div :class="[
                    'w-2 h-2 rounded-full transition-colors duration-300',
                    connected ? 'bg-green-500' : 'bg-red-500',
                    pulseClass
                ]"></div>
                <span :class="statusClass" class="text-xs font-medium" x-text="statusText"></span>
            </div>
            
            {{-- Refresh Button --}}
            <button @click="refresh()" 
                    :disabled="loading"
                    class="p-1 rounded-md hover:bg-gray-100 transition-colors duration-200 disabled:opacity-50">
                <svg :class="loading ? 'animate-spin' : ''" 
                     class="w-4 h-4 text-gray-400" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Loading State --}}
    <div x-show="loading" class="animate-pulse">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="h-16 bg-gray-200 rounded-lg"></div>
            <div class="h-16 bg-gray-200 rounded-lg"></div>
        </div>
        <div class="space-y-2">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
    </div>
    
    {{-- Error State --}}
    <div x-show="error && !loading" 
         class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
        <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
            </path>
        </svg>
        <p class="text-red-700 text-sm" x-text="error"></p>
        <button @click="refresh()" 
                class="mt-2 text-red-600 hover:text-red-800 text-sm font-medium underline">
            Try Again
        </button>
    </div>
    
    {{-- Main Content --}}
    <div x-show="!loading && !error" class="space-y-6">
        
        {{-- Primary Stats Grid --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Total Active Users --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-1" 
                     x-text="stats.active_users"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    0
                </div>
                <div class="text-sm text-blue-700 font-medium">Total Active</div>
            </div>
            
            {{-- Users Today --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg p-4 text-center">
                <div class="text-3xl font-bold text-green-600 mb-1" 
                     x-text="stats.users_today">
                    0
                </div>
                <div class="text-sm text-green-700 font-medium">Today</div>
            </div>
        </div>
        
        {{-- User Type Breakdown --}}
        <div class="space-y-3">
            <h4 class="text-sm font-medium text-gray-700">User Breakdown</h4>
            
            {{-- Authenticated Users --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Authenticated</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900" x-text="stats.authenticated_users">0</span>
                    <span class="text-xs text-gray-500" x-text="`(${userPercentage.authenticated}%)`">0%</span>
                </div>
            </div>
            
            {{-- Progress Bar for Authenticated --}}
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-500" 
                     :style="`width: ${userPercentage.authenticated}%`"></div>
            </div>
            
            {{-- Guest Users --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    <span class="text-sm text-gray-600">Guests</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-900" x-text="stats.guest_users">0</span>
                    <span class="text-xs text-gray-500" x-text="`(${userPercentage.guest}%)`">0%</span>
                </div>
            </div>
            
            {{-- Progress Bar for Guests --}}
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-gray-400 h-1.5 rounded-full transition-all duration-500" 
                     :style="`width: ${userPercentage.guest}%`"></div>
            </div>
        </div>
        
        {{-- Sessions Info --}}
        <div class="pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Sessions Today:</span>
                <span class="font-medium text-gray-900" x-text="stats.sessions_today">0</span>
            </div>
            
            {{-- Last Updated --}}
            <div class="mt-2 text-xs text-gray-500 text-center" x-show="stats.timestamp">
                Last updated: <span x-text="stats.timestamp ? new Date(stats.timestamp).toLocaleTimeString() : ''"></span>
            </div>
        </div>
    </div>
</div>

{{-- Mobile Responsive Compact Version --}}
<div class="lg:hidden bg-white rounded-lg shadow-sm border border-gray-200 p-4" x-data="liveUserCounter">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                    </path>
                </svg>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-900" x-text="stats.active_users">0</div>
                <div class="text-sm text-gray-500">users online</div>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <div :class="[
                'w-2 h-2 rounded-full',
                connected ? 'bg-green-500' : 'bg-red-500',
                pulseClass
            ]"></div>
            <span class="text-xs text-gray-500" x-text="statusText"></span>
        </div>
    </div>
</div>

{{-- Pusher Configuration Script --}}
<script>
window.pusherConfig = {
    key: '{{ config("broadcasting.connections.pusher.key") }}',
    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
    encrypted: true
};
</script>

{{-- Include Alpine.js Component --}}
@push('scripts')
<script src="{{ asset('js/components/LiveUserCounter.js') }}"></script>
@endpush
