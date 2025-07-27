{{-- User Activity Widget Template --}}
<div class="widget-container bg-white rounded-lg shadow-sm border p-6" 
     x-data="{ loading: false, error: null }"
     style="background-color: var(--bg-primary); border-color: var(--border-primary);">
     
    {{-- Widget Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold" style="color: var(--text-primary);">
                {{ $widget_config['title'] }}
            </h3>
            <p class="text-sm" style="color: var(--text-secondary);">
                {{ $widget_config['description'] }}
            </p>
        </div>
        
        <div class="flex items-center space-x-2">
            {{-- Refresh Button --}}
            <button @click="refreshWidget()" 
                    :disabled="loading"
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    title="Refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     :class="{ 'animate-spin': loading }">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            
            {{-- Settings Button --}}
            <button class="p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>
    </div>
    
    {{-- Error State --}}
    <div x-show="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
        <div class="flex">
            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Widget Error</h3>
                <p class="text-sm text-red-700 mt-1" x-text="error"></p>
            </div>
        </div>
    </div>
    
    {{-- Widget Content --}}
    @if(!isset($error))
    <div class="space-y-6">
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Total Users --}}
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($total_users) }}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Total Users</div>
            </div>
            
            {{-- New Users --}}
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-green-600">{{ number_format($new_users) }}</div>
                <div class="text-sm" style="color: var(--text-secondary);">New Users ({{ $period_days }}d)</div>
                @if($growth_percentage != 0)
                <div class="text-xs mt-1 {{ $growth_percentage > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $growth_percentage > 0 ? '+' : '' }}{{ $growth_percentage }}%
                </div>
                @endif
            </div>
            
            {{-- Active Users --}}
            <div class="text-center p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($active_users) }}</div>
                <div class="text-sm" style="color: var(--text-secondary);">Active (7d)</div>
            </div>
        </div>
        
        {{-- Growth Chart --}}
        <div class="p-4 rounded-lg" style="background-color: var(--bg-secondary);">
            <h4 class="text-sm font-semibold mb-4" style="color: var(--text-primary);">User Registration Trend</h4>
            <div class="h-32">
                <canvas id="userGrowthChart_{{ $widget_id }}" class="w-full h-full"></canvas>
            </div>
        </div>
        
        {{-- Last Updated --}}
        <div class="text-xs text-center" style="color: var(--text-tertiary);">
            Last updated: {{ \Carbon\Carbon::parse($last_updated)->diffForHumans() }}
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart if Chart.js is available
    if (typeof Chart !== 'undefined') {
        const ctx = document.getElementById('userGrowthChart_{{ $widget_id }}');
        if (ctx) {
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($user_growth, 'date')) !!},
                    datasets: [{
                        label: 'New Users',
                        data: {!! json_encode(array_column($user_growth, 'count')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0,
                            hoverRadius: 4
                        }
                    }
                }
            });
        }
    }
});

// Widget refresh function
window.refreshWidget = function() {
    // Implementation would trigger widget refresh via AJAX
    console.log('Refreshing user activity widget...');
};
</script>
