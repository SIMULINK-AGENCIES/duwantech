@props([
    'title' => 'KPI Card',
    'value' => '0',
    'previousValue' => null,
    'icon' => 'chart-line',
    'color' => 'blue',
    'format' => 'number',
    'suffix' => '',
    'prefix' => '',
    'trend' => null,
    'loading' => false,
    'realTimeEndpoint' => null,
    'refreshInterval' => 30000,
    'clickable' => false,
    'href' => '#',
    'size' => 'md',
    'animated' => true,
    'showTrend' => true,
    'customClass' => '',
    'id' => null
])

@php
    $componentId = $id ?? 'kpi-card-' . uniqid();
    $trendDirection = null;
    $trendPercentage = 0;
    
    if ($previousValue !== null && $showTrend) {
        $current = (float) str_replace([',', '$', '%'], '', $value);
        $previous = (float) str_replace([',', '$', '%'], '', $previousValue);
        
        if ($previous > 0) {
            $trendPercentage = (($current - $previous) / $previous) * 100;
            $trendDirection = $trendPercentage >= 0 ? 'up' : 'down';
        }
    }
    
    $sizeClasses = [
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8'
    ];
    
    $colorClasses = [
        'blue' => 'border-blue-200 bg-blue-50 hover:bg-blue-100',
        'green' => 'border-green-200 bg-green-50 hover:bg-green-100',
        'red' => 'border-red-200 bg-red-50 hover:bg-red-100',
        'yellow' => 'border-yellow-200 bg-yellow-50 hover:bg-yellow-100',
        'purple' => 'border-purple-200 bg-purple-50 hover:bg-purple-100',
        'indigo' => 'border-indigo-200 bg-indigo-50 hover:bg-indigo-100',
        'gray' => 'border-gray-200 bg-gray-50 hover:bg-gray-100'
    ];
    
    $iconColorClasses = [
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'red' => 'text-red-600',
        'yellow' => 'text-yellow-600',
        'purple' => 'text-purple-600',
        'indigo' => 'text-indigo-600',
        'gray' => 'text-gray-600'
    ];
@endphp

<div 
    id="{{ $componentId }}"
    @class([
        'kpi-card relative overflow-hidden rounded-lg border transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg',
        $sizeClasses[$size] ?? $sizeClasses['md'],
        $colorClasses[$color] ?? $colorClasses['blue'],
        'cursor-pointer' => $clickable,
        'animate-pulse' => $loading,
        $customClass
    ])
    @if($clickable && $href !== '#')
        onclick="window.location.href='{{ $href }}'"
        role="button"
        tabindex="0"
        @keydown.enter="window.location.href='{{ $href }}'"
        @keydown.space.prevent="window.location.href='{{ $href }}'"
    @endif
    @if($realTimeEndpoint)
        data-realtime-endpoint="{{ $realTimeEndpoint }}"
        data-refresh-interval="{{ $refreshInterval }}"
    @endif
    data-kpi-card
>
    <!-- Background decoration -->
    <div class="absolute top-0 right-0 -mr-6 -mt-6 w-24 h-24 rounded-full opacity-10 {{ $iconColorClasses[$color] ?? $iconColorClasses['blue'] }} bg-current"></div>
    
    <!-- Loading overlay -->
    @if($loading)
        <div class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 {{ $iconColorClasses[$color] ?? $iconColorClasses['blue'] }} border-current"></div>
        </div>
    @endif
    
    <div class="relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                @if($icon)
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-lg {{ $iconColorClasses[$color] ?? $iconColorClasses['blue'] }} bg-current bg-opacity-10 flex items-center justify-center">
                            <i class="fas fa-{{ $icon }} {{ $iconColorClasses[$color] ?? $iconColorClasses['blue'] }} text-lg"></i>
                        </div>
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide">{{ $title }}</h3>
                </div>
            </div>
            
            @if($showTrend && $trendDirection)
                <div class="flex items-center">
                    <span @class([
                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                        'bg-green-100 text-green-800' => $trendDirection === 'up',
                        'bg-red-100 text-red-800' => $trendDirection === 'down'
                    ])>
                        <i @class([
                            'fas mr-1',
                            'fa-arrow-up' => $trendDirection === 'up',
                            'fa-arrow-down' => $trendDirection === 'down'
                        ])></i>
                        {{ number_format(abs($trendPercentage), 1) }}%
                    </span>
                </div>
            @endif
        </div>
        
        <!-- Main Value -->
        <div class="mb-2">
            <div class="flex items-baseline">
                @if($prefix)
                    <span class="text-2xl font-medium text-gray-500 mr-1">{{ $prefix }}</span>
                @endif
                
                <span class="text-3xl font-bold text-gray-900 kpi-value" data-value="{{ $value }}" data-format="{{ $format }}">
                    @if($format === 'currency')
                        ${{ number_format((float) str_replace(['$', ','], '', $value), 0) }}
                    @elseif($format === 'percentage')
                        {{ number_format((float) str_replace('%', '', $value), 1) }}%
                    @elseif($format === 'decimal')
                        {{ number_format((float) str_replace(',', '', $value), 2) }}
                    @else
                        {{ number_format((float) str_replace(',', '', $value), 0) }}
                    @endif
                </span>
                
                @if($suffix)
                    <span class="text-2xl font-medium text-gray-500 ml-1">{{ $suffix }}</span>
                @endif
            </div>
        </div>
        
        <!-- Trend Information -->
        @if($showTrend && $previousValue !== null)
            <div class="text-sm text-gray-600">
                <span>vs. previous period:</span>
                <span class="font-medium ml-1">
                    @if($format === 'currency')
                        ${{ number_format((float) str_replace(['$', ','], '', $previousValue), 0) }}
                    @elseif($format === 'percentage')
                        {{ number_format((float) str_replace('%', '', $previousValue), 1) }}%
                    @elseif($format === 'decimal')
                        {{ number_format((float) str_replace(',', '', $previousValue), 2) }}
                    @else
                        {{ number_format((float) str_replace(',', '', $previousValue), 0) }}
                    @endif
                </span>
            </div>
        @endif
        
        <!-- Interactive hover state indicator -->
        @if($clickable)
            <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <i class="fas fa-external-link-alt text-gray-400 text-sm"></i>
            </div>
        @endif
        
        <!-- Real-time indicator -->
        @if($realTimeEndpoint)
            <div class="absolute top-2 right-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse kpi-realtime-indicator"></div>
            </div>
        @endif
    </div>
    
    <!-- Accessibility -->
    <div class="sr-only">
        {{ $title }}: {{ $value }}{{ $suffix }}
        @if($showTrend && $trendDirection)
            , trending {{ $trendDirection }} by {{ number_format(abs($trendPercentage), 1) }}%
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            .kpi-card:hover .kpi-realtime-indicator {
                animation: pulse 1s ease-in-out infinite;
            }
            
            .kpi-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .kpi-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .kpi-value {
                transition: color 0.3s ease;
            }
            
            @keyframes countUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .kpi-value.animate-count {
                animation: countUp 0.6s ease-out;
            }
        </style>
    @endpush
    
    @push('scripts')
        <script>
            class KPICard {
                constructor(element) {
                    this.element = element;
                    this.endpoint = element.dataset.realtimeEndpoint;
                    this.interval = parseInt(element.dataset.refreshInterval) || 30000;
                    this.valueElement = element.querySelector('.kpi-value');
                    this.indicator = element.querySelector('.kpi-realtime-indicator');
                    
                    if (this.endpoint) {
                        this.startRealTimeUpdates();
                    }
                }
                
                startRealTimeUpdates() {
                    this.updateData();
                    setInterval(() => this.updateData(), this.interval);
                }
                
                async updateData() {
                    try {
                        const response = await fetch(this.endpoint);
                        const data = await response.json();
                        
                        if (data.value !== undefined) {
                            this.updateValue(data.value, data.format || 'number');
                        }
                        
                        if (data.trend !== undefined) {
                            this.updateTrend(data.trend);
                        }
                        
                        this.flashIndicator();
                    } catch (error) {
                        console.error('Failed to update KPI data:', error);
                        this.showError();
                    }
                }
                
                updateValue(newValue, format) {
                    if (this.valueElement) {
                        const oldValue = this.valueElement.dataset.value;
                        
                        if (oldValue !== newValue.toString()) {
                            this.animateValueChange(newValue, format);
                            this.valueElement.dataset.value = newValue;
                        }
                    }
                }
                
                animateValueChange(newValue, format) {
                    this.valueElement.classList.add('animate-count');
                    
                    const formatValue = (value) => {
                        const numValue = parseFloat(value);
                        switch (format) {
                            case 'currency':
                                return '$' + numValue.toLocaleString();
                            case 'percentage':
                                return numValue.toFixed(1) + '%';
                            case 'decimal':
                                return numValue.toFixed(2);
                            default:
                                return Math.round(numValue).toLocaleString();
                        }
                    };
                    
                    // Animate number counting
                    const oldValue = parseFloat(this.valueElement.dataset.value) || 0;
                    const targetValue = parseFloat(newValue);
                    const duration = 1000;
                    const steps = 60;
                    const increment = (targetValue - oldValue) / steps;
                    let currentValue = oldValue;
                    let step = 0;
                    
                    const timer = setInterval(() => {
                        currentValue += increment;
                        step++;
                        
                        this.valueElement.textContent = formatValue(currentValue);
                        
                        if (step >= steps) {
                            clearInterval(timer);
                            this.valueElement.textContent = formatValue(targetValue);
                            this.valueElement.classList.remove('animate-count');
                        }
                    }, duration / steps);
                }
                
                updateTrend(trendData) {
                    const trendElement = this.element.querySelector('.inline-flex.items-center');
                    if (trendElement && trendData.percentage !== undefined) {
                        const isPositive = trendData.percentage >= 0;
                        trendElement.className = `inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                            isPositive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }`;
                        
                        const icon = trendElement.querySelector('i');
                        const text = trendElement.querySelector('span') || trendElement;
                        
                        if (icon) {
                            icon.className = `fas mr-1 ${isPositive ? 'fa-arrow-up' : 'fa-arrow-down'}`;
                        }
                        
                        const textContent = Math.abs(trendData.percentage).toFixed(1) + '%';
                        if (trendElement.textContent.includes('%')) {
                            trendElement.innerHTML = trendElement.innerHTML.replace(/[\d.]+%/, textContent);
                        }
                    }
                }
                
                flashIndicator() {
                    if (this.indicator) {
                        this.indicator.classList.add('bg-green-400');
                        setTimeout(() => {
                            this.indicator.classList.remove('bg-green-400');
                        }, 200);
                    }
                }
                
                showError() {
                    if (this.indicator) {
                        this.indicator.classList.add('bg-red-400');
                        setTimeout(() => {
                            this.indicator.classList.remove('bg-red-400');
                        }, 1000);
                    }
                }
            }
            
            // Initialize KPI cards
            document.addEventListener('DOMContentLoaded', function() {
                const kpiCards = document.querySelectorAll('[data-kpi-card]');
                kpiCards.forEach(card => new KPICard(card));
            });
            
            // Handle keyboard navigation for clickable cards
            document.addEventListener('keydown', function(e) {
                if ((e.key === 'Enter' || e.key === ' ') && e.target.hasAttribute('data-kpi-card')) {
                    e.preventDefault();
                    const href = e.target.getAttribute('onclick');
                    if (href) {
                        const url = href.match(/'([^']+)'/)?.[1];
                        if (url) window.location.href = url;
                    }
                }
            });
        </script>
    @endpush
@endonce
