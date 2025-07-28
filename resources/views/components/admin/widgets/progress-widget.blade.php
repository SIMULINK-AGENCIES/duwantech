@props([
    'title' => 'Progress Widget',
    'type' => 'bar', // bar, circle, semicircle, line, radial, stepped
    'value' => 0,
    'max' => 100,
    'min' => 0,
    'target' => null,
    'unit' => '',
    'format' => 'percentage', // percentage, number, currency, custom
    'precision' => 1,
    'showValue' => true,
    'showTarget' => true,
    'showPercentage' => true,
    'showLabels' => true,
    'animated' => true,
    'striped' => false,
    'gradient' => false,
    'color' => 'blue',
    'size' => 'md',
    'height' => null,
    'thickness' => 'md',
    'backgroundColor' => null,
    'borderRadius' => 'rounded',
    'loading' => false,
    'realTimeEndpoint' => null,
    'refreshInterval' => 30000,
    'milestones' => [],
    'achievements' => [],
    'showMilestones' => true,
    'showAchievements' => true,
    'icon' => null,
    'description' => null,
    'trend' => null,
    'trendPeriod' => null,
    'customClass' => '',
    'theme' => 'light',
    'id' => null
])

@php
    $componentId = $id ?? 'progress-widget-' . uniqid();
    
    // Calculate percentage
    $percentage = $max > $min ? (($value - $min) / ($max - $min)) * 100 : 0;
    $percentage = max(0, min(100, $percentage));
    
    // Calculate target percentage if target is set
    $targetPercentage = null;
    if ($target !== null && $max > $min) {
        $targetPercentage = (($target - $min) / ($max - $min)) * 100;
        $targetPercentage = max(0, min(100, $targetPercentage));
    }
    
    // Color schemes
    $colorClasses = [
        'blue' => [
            'bg' => 'bg-blue-500',
            'gradient' => 'from-blue-400 to-blue-600',
            'text' => 'text-blue-600',
            'border' => 'border-blue-500',
            'light' => 'bg-blue-100'
        ],
        'green' => [
            'bg' => 'bg-green-500',
            'gradient' => 'from-green-400 to-green-600',
            'text' => 'text-green-600',
            'border' => 'border-green-500',
            'light' => 'bg-green-100'
        ],
        'red' => [
            'bg' => 'bg-red-500',
            'gradient' => 'from-red-400 to-red-600',
            'text' => 'text-red-600',
            'border' => 'border-red-500',
            'light' => 'bg-red-100'
        ],
        'yellow' => [
            'bg' => 'bg-yellow-500',
            'gradient' => 'from-yellow-400 to-yellow-600',
            'text' => 'text-yellow-600',
            'border' => 'border-yellow-500',
            'light' => 'bg-yellow-100'
        ],
        'purple' => [
            'bg' => 'bg-purple-500',
            'gradient' => 'from-purple-400 to-purple-600',
            'text' => 'text-purple-600',
            'border' => 'border-purple-500',
            'light' => 'bg-purple-100'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-500',
            'gradient' => 'from-indigo-400 to-indigo-600',
            'text' => 'text-indigo-600',
            'border' => 'border-indigo-500',
            'light' => 'bg-indigo-100'
        ]
    ];
    
    $colors = $colorClasses[$color] ?? $colorClasses['blue'];
    
    // Size configurations
    $sizeConfig = [
        'sm' => ['height' => 'h-2', 'text' => 'text-sm', 'padding' => 'p-3'],
        'md' => ['height' => 'h-3', 'text' => 'text-base', 'padding' => 'p-4'],
        'lg' => ['height' => 'h-4', 'text' => 'text-lg', 'padding' => 'p-6'],
        'xl' => ['height' => 'h-6', 'text' => 'text-xl', 'padding' => 'p-8']
    ];
    
    $config = $sizeConfig[$size] ?? $sizeConfig['md'];
    
    // Format value function
    $formatValue = function($val) use ($format, $precision, $unit) {
        switch ($format) {
            case 'currency':
                return '$' . number_format($val, $precision);
            case 'percentage':
                return number_format($val, $precision) . '%';
            case 'number':
                return number_format($val, $precision);
            default:
                return number_format($val, $precision) . ($unit ? ' ' . $unit : '');
        }
    };
@endphp

<div 
    id="{{ $componentId }}"
    @class([
        'progress-widget bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden',
        'dark:bg-gray-800 dark:border-gray-700' => $theme === 'dark',
        $config['padding'],
        $customClass
    ])
    @if($realTimeEndpoint)
        data-realtime-endpoint="{{ $realTimeEndpoint }}"
        data-refresh-interval="{{ $refreshInterval }}"
    @endif
    data-progress-widget
    data-type="{{ $type }}"
    data-animated="{{ $animated ? 'true' : 'false' }}"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            @if($icon)
                <div @class([
                    'flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center',
                    $colors['light']
                ])>
                    <i class="fas fa-{{ $icon }} {{ $colors['text'] }} text-lg"></i>
                </div>
            @endif
            
            <div>
                <h3 @class([
                    'font-semibold text-gray-900',
                    'dark:text-white' => $theme === 'dark',
                    $config['text']
                ])>
                    {{ $title }}
                </h3>
                
                @if($description)
                    <p @class([
                        'text-sm text-gray-500 mt-1',
                        'dark:text-gray-400' => $theme === 'dark'
                    ])>
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            @if($realTimeEndpoint)
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse progress-realtime-indicator"></div>
                    <span @class([
                        'text-xs text-gray-500',
                        'dark:text-gray-400' => $theme === 'dark'
                    ])>Live</span>
                </div>
            @endif
            
            @if($trend !== null)
                <div @class([
                    'flex items-center px-2 py-1 rounded-full text-xs',
                    'bg-green-100 text-green-800' => $trend > 0,
                    'bg-red-100 text-red-800' => $trend < 0,
                    'bg-gray-100 text-gray-800' => $trend == 0
                ])>
                    <i @class([
                        'fas mr-1',
                        'fa-arrow-up' => $trend > 0,
                        'fa-arrow-down' => $trend < 0,
                        'fa-minus' => $trend == 0
                    ])></i>
                    {{ abs($trend) }}%
                    @if($trendPeriod)
                        <span class="ml-1">{{ $trendPeriod }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <!-- Progress Display -->
    <div class="progress-container">
        @if($loading)
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 {{ $colors['border'] }}"></div>
            </div>
        @else
            @if($type === 'bar')
                <div class="space-y-3">
                    @if($showLabels && ($showValue || $showTarget || $showPercentage))
                        <div class="flex items-center justify-between text-sm">
                            <div class="space-x-2">
                                @if($showValue)
                                    <span @class([
                                        'font-medium text-gray-900',
                                        'dark:text-white' => $theme === 'dark'
                                    ])>
                                        {{ $formatValue($value) }}
                                    </span>
                                @endif
                                
                                @if($showTarget && $target !== null)
                                    <span @class([
                                        'text-gray-500',
                                        'dark:text-gray-400' => $theme === 'dark'
                                    ])>
                                        / {{ $formatValue($target) }}
                                    </span>
                                @endif
                            </div>
                            
                            @if($showPercentage)
                                <span @class([
                                    'font-medium',
                                    $colors['text']
                                ])>
                                    {{ number_format($percentage, 0) }}%
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <div @class([
                        'relative bg-gray-200 overflow-hidden',
                        'dark:bg-gray-700' => $theme === 'dark',
                        $config['height'],
                        $borderRadius
                    ])>
                        <!-- Progress Bar -->
                        <div 
                            @class([
                                'progress-bar h-full transition-all duration-1000 ease-out',
                                $gradient ? 'bg-gradient-to-r ' . $colors['gradient'] : $colors['bg'],
                                'progress-bar-striped' => $striped,
                                'progress-bar-animated' => $animated && $striped
                            ])
                            style="width: {{ $percentage }}%"
                            data-progress="{{ $percentage }}"
                        ></div>
                        
                        <!-- Target Indicator -->
                        @if($target !== null && $showTarget && $targetPercentage !== null)
                            <div 
                                class="absolute top-0 w-1 h-full bg-gray-800 dark:bg-gray-200"
                                style="left: {{ $targetPercentage }}%"
                                title="Target: {{ $formatValue($target) }}"
                            ></div>
                        @endif
                        
                        <!-- Milestones -->
                        @if($showMilestones && !empty($milestones))
                            @foreach($milestones as $milestone)
                                @php
                                    $milestonePercentage = (($milestone['value'] - $min) / ($max - $min)) * 100;
                                    $milestonePercentage = max(0, min(100, $milestonePercentage));
                                @endphp
                                <div 
                                    class="absolute top-0 w-1 h-full {{ $milestone['color'] ?? 'bg-yellow-500' }}"
                                    style="left: {{ $milestonePercentage }}%"
                                    title="{{ $milestone['label'] ?? 'Milestone' }}: {{ $formatValue($milestone['value']) }}"
                                ></div>
                            @endforeach
                        @endif
                    </div>
                </div>
                
            @elseif($type === 'circle' || $type === 'semicircle')
                <div class="flex items-center justify-center">
                    <div class="relative">
                        <svg 
                            class="transform -rotate-90 {{ $type === 'semicircle' ? 'rotate-180' : '' }}"
                            width="120" 
                            height="{{ $type === 'semicircle' ? '60' : '120' }}"
                            viewBox="0 0 120 120"
                        >
                            <!-- Background Circle -->
                            <circle
                                cx="60"
                                cy="60"
                                r="54"
                                stroke="currentColor"
                                stroke-width="12"
                                fill="transparent"
                                class="text-gray-200 dark:text-gray-700"
                                @if($type === 'semicircle')
                                    stroke-dasharray="169.65 339.3"
                                @endif
                            />
                            
                            <!-- Progress Circle -->
                            <circle
                                cx="60"
                                cy="60"
                                r="54"
                                stroke="currentColor"
                                stroke-width="12"
                                fill="transparent"
                                class="progress-circle {{ $colors['text'] }}"
                                stroke-dasharray="{{ $type === 'semicircle' ? '169.65' : '339.3' }}"
                                stroke-dashoffset="{{ $type === 'semicircle' ? (169.65 - (169.65 * $percentage / 100)) : (339.3 - (339.3 * $percentage / 100)) }}"
                                stroke-linecap="round"
                                data-progress="{{ $percentage }}"
                            />
                        </svg>
                        
                        <!-- Center Text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                @if($showPercentage)
                                    <div @class([
                                        'text-2xl font-bold text-gray-900',
                                        'dark:text-white' => $theme === 'dark'
                                    ])>
                                        {{ number_format($percentage, 0) }}%
                                    </div>
                                @endif
                                
                                @if($showValue)
                                    <div @class([
                                        'text-sm text-gray-500',
                                        'dark:text-gray-400' => $theme === 'dark'
                                    ])>
                                        {{ $formatValue($value) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($type === 'line')
                <div class="space-y-2">
                    @if($showLabels && ($showValue || $showTarget))
                        <div class="flex items-center justify-between text-sm">
                            @if($showValue)
                                <span @class([
                                    'font-medium text-gray-900',
                                    'dark:text-white' => $theme === 'dark'
                                ])>
                                    {{ $formatValue($value) }}
                                </span>
                            @endif
                            
                            @if($showTarget && $target !== null)
                                <span @class([
                                    'text-gray-500',
                                    'dark:text-gray-400' => $theme === 'dark'
                                ])>
                                    Target: {{ $formatValue($target) }}
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <div @class([
                        'relative h-1 bg-gray-200 overflow-hidden',
                        'dark:bg-gray-700' => $theme === 'dark',
                        $borderRadius
                    ])>
                        <div 
                            @class([
                                'progress-bar h-full transition-all duration-1000 ease-out',
                                $colors['bg']
                            ])
                            style="width: {{ $percentage }}%"
                            data-progress="{{ $percentage }}"
                        ></div>
                    </div>
                    
                    @if($showPercentage)
                        <div class="text-right">
                            <span @class([
                                'text-xs font-medium',
                                $colors['text']
                            ])>
                                {{ number_format($percentage, 0) }}%
                            </span>
                        </div>
                    @endif
                </div>
                
            @elseif($type === 'stepped')
                @php
                    $steps = 10;
                    $stepsCompleted = floor($percentage / (100 / $steps));
                @endphp
                
                <div class="space-y-3">
                    @if($showLabels && ($showValue || $showPercentage))
                        <div class="flex items-center justify-between text-sm">
                            @if($showValue)
                                <span @class([
                                    'font-medium text-gray-900',
                                    'dark:text-white' => $theme === 'dark'
                                ])>
                                    {{ $formatValue($value) }}
                                </span>
                            @endif
                            
                            @if($showPercentage)
                                <span @class([
                                    'font-medium',
                                    $colors['text']
                                ])>
                                    {{ $stepsCompleted }}/{{ $steps }} ({{ number_format($percentage, 0) }}%)
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    <div class="flex space-x-1">
                        @for($i = 0; $i < $steps; $i++)
                            <div @class([
                                'flex-1 h-3 rounded-sm transition-all duration-300',
                                $i < $stepsCompleted ? $colors['bg'] : 'bg-gray-200 dark:bg-gray-700'
                            ])></div>
                        @endfor
                    </div>
                </div>
            @endif
        @endif
    </div>
    
    <!-- Milestones List -->
    @if($showMilestones && !empty($milestones) && $type !== 'bar')
        <div class="mt-4 space-y-2">
            <h4 @class([
                'text-sm font-medium text-gray-700',
                'dark:text-gray-300' => $theme === 'dark'
            ])>Milestones</h4>
            
            <div class="space-y-1">
                @foreach($milestones as $milestone)
                    @php
                        $isReached = $value >= $milestone['value'];
                    @endphp
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-2">
                            <div @class([
                                'w-3 h-3 rounded-full',
                                $isReached ? ($milestone['color'] ?? 'bg-green-500') : 'bg-gray-300'
                            ])></div>
                            <span @class([
                                $isReached ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'
                            ])>
                                {{ $milestone['label'] ?? 'Milestone' }}
                            </span>
                        </div>
                        <span @class([
                            'font-medium',
                            $isReached ? 'text-green-600' : 'text-gray-500 dark:text-gray-400'
                        ])>
                            {{ $formatValue($milestone['value']) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Achievements -->
    @if($showAchievements && !empty($achievements))
        <div class="mt-4 space-y-2">
            <h4 @class([
                'text-sm font-medium text-gray-700',
                'dark:text-gray-300' => $theme === 'dark'
            ])>Achievements</h4>
            
            <div class="flex flex-wrap gap-2">
                @foreach($achievements as $achievement)
                    @php
                        $isUnlocked = isset($achievement['unlocked']) ? $achievement['unlocked'] : ($value >= ($achievement['requirement'] ?? 0));
                    @endphp
                    <div @class([
                        'flex items-center space-x-2 px-3 py-1 rounded-full text-xs',
                        $isUnlocked ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-500'
                    ])>
                        @if(isset($achievement['icon']))
                            <i class="fas fa-{{ $achievement['icon'] }}"></i>
                        @endif
                        <span>{{ $achievement['label'] }}</span>
                        @if($isUnlocked)
                            <i class="fas fa-check text-green-600"></i>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .progress-widget {
                transition: all 0.3s ease;
            }
            
            .progress-widget:hover {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .progress-bar {
                position: relative;
                overflow: hidden;
            }
            
            .progress-bar-striped {
                background-image: linear-gradient(
                    45deg,
                    rgba(255, 255, 255, 0.15) 25%,
                    transparent 25%,
                    transparent 50%,
                    rgba(255, 255, 255, 0.15) 50%,
                    rgba(255, 255, 255, 0.15) 75%,
                    transparent 75%,
                    transparent
                );
                background-size: 1rem 1rem;
            }
            
            .progress-bar-animated {
                animation: progress-bar-stripes 1s linear infinite;
            }
            
            @keyframes progress-bar-stripes {
                0% {
                    background-position-x: 1rem;
                }
            }
            
            .progress-circle {
                transition: stroke-dashoffset 1s ease-in-out;
            }
            
            .progress-realtime-indicator {
                animation: pulse 2s ease-in-out infinite;
            }
            
            @keyframes progress-fill {
                from {
                    width: 0%;
                }
            }
            
            .progress-widget[data-animated="true"] .progress-bar {
                animation: progress-fill 1s ease-out;
            }
            
            .milestone-marker {
                position: absolute;
                top: -2px;
                height: calc(100% + 4px);
                width: 2px;
                background-color: #374151;
                border-radius: 1px;
            }
            
            .milestone-marker::after {
                content: '';
                position: absolute;
                top: -4px;
                left: -3px;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: inherit;
            }
        </style>
    @endpush
    
    @push('scripts')
        <script>
            class ProgressWidget {
                constructor(element) {
                    this.element = element;
                    this.type = element.dataset.type;
                    this.animated = element.dataset.animated === 'true';
                    this.endpoint = element.dataset.realtimeEndpoint;
                    this.interval = parseInt(element.dataset.refreshInterval) || 30000;
                    
                    this.init();
                    
                    if (this.endpoint) {
                        this.startRealTimeUpdates();
                    }
                }
                
                init() {
                    if (this.animated) {
                        this.animateProgress();
                    }
                }
                
                animateProgress() {
                    if (this.type === 'bar' || this.type === 'line') {
                        const progressBar = this.element.querySelector('.progress-bar');
                        if (progressBar) {
                            const targetWidth = progressBar.dataset.progress + '%';
                            progressBar.style.width = '0%';
                            
                            requestAnimationFrame(() => {
                                progressBar.style.width = targetWidth;
                            });
                        }
                    } else if (this.type === 'circle' || this.type === 'semicircle') {
                        const progressCircle = this.element.querySelector('.progress-circle');
                        if (progressCircle) {
                            const targetProgress = parseFloat(progressCircle.dataset.progress);
                            const circumference = this.type === 'semicircle' ? 169.65 : 339.3;
                            const targetOffset = circumference - (circumference * targetProgress / 100);
                            
                            progressCircle.style.strokeDashoffset = circumference;
                            
                            requestAnimationFrame(() => {
                                progressCircle.style.strokeDashoffset = targetOffset;
                            });
                        }
                    }
                }
                
                async startRealTimeUpdates() {
                    this.updateData();
                    setInterval(() => this.updateData(), this.interval);
                }
                
                async updateData() {
                    try {
                        const response = await fetch(this.endpoint);
                        const data = await response.json();
                        
                        if (data.value !== undefined) {
                            this.updateProgress(data);
                        }
                        
                        this.flashIndicator();
                    } catch (error) {
                        console.error('Failed to update progress data:', error);
                        this.showError();
                    }
                }
                
                updateProgress(data) {
                    const { value, max = 100, min = 0, target = null } = data;
                    const percentage = max > min ? ((value - min) / (max - min)) * 100 : 0;
                    const clampedPercentage = Math.max(0, Math.min(100, percentage));
                    
                    // Update value displays
                    const valueElements = this.element.querySelectorAll('[data-value]');
                    valueElements.forEach(el => {
                        el.textContent = this.formatValue(value, data.format || 'number');
                    });
                    
                    // Update percentage displays
                    const percentageElements = this.element.querySelectorAll('[data-percentage]');
                    percentageElements.forEach(el => {
                        el.textContent = Math.round(clampedPercentage) + '%';
                    });
                    
                    // Update progress bars
                    if (this.type === 'bar' || this.type === 'line') {
                        const progressBar = this.element.querySelector('.progress-bar');
                        if (progressBar) {
                            progressBar.style.width = clampedPercentage + '%';
                            progressBar.dataset.progress = clampedPercentage;
                        }
                    } else if (this.type === 'circle' || this.type === 'semicircle') {
                        const progressCircle = this.element.querySelector('.progress-circle');
                        if (progressCircle) {
                            const circumference = this.type === 'semicircle' ? 169.65 : 339.3;
                            const offset = circumference - (circumference * clampedPercentage / 100);
                            progressCircle.style.strokeDashoffset = offset;
                            progressCircle.dataset.progress = clampedPercentage;
                        }
                    } else if (this.type === 'stepped') {
                        const steps = 10;
                        const stepsCompleted = Math.floor(clampedPercentage / (100 / steps));
                        const stepElements = this.element.querySelectorAll('.flex-1');
                        
                        stepElements.forEach((step, index) => {
                            if (index < stepsCompleted) {
                                step.classList.remove('bg-gray-200', 'dark:bg-gray-700');
                                step.classList.add('bg-blue-500'); // Default color
                            } else {
                                step.classList.add('bg-gray-200', 'dark:bg-gray-700');
                                step.classList.remove('bg-blue-500');
                            }
                        });
                    }
                    
                    // Update milestones
                    if (data.milestones) {
                        this.updateMilestones(data.milestones, value);
                    }
                    
                    // Update achievements
                    if (data.achievements) {
                        this.updateAchievements(data.achievements);
                    }
                }
                
                updateMilestones(milestones, currentValue) {
                    const milestoneElements = this.element.querySelectorAll('.milestone-item');
                    milestoneElements.forEach((element, index) => {
                        if (milestones[index]) {
                            const milestone = milestones[index];
                            const isReached = currentValue >= milestone.value;
                            const indicator = element.querySelector('.milestone-indicator');
                            
                            if (indicator) {
                                if (isReached) {
                                    indicator.classList.remove('bg-gray-300');
                                    indicator.classList.add(milestone.color || 'bg-green-500');
                                } else {
                                    indicator.classList.add('bg-gray-300');
                                    indicator.classList.remove(milestone.color || 'bg-green-500');
                                }
                            }
                        }
                    });
                }
                
                updateAchievements(achievements) {
                    const achievementElements = this.element.querySelectorAll('.achievement-item');
                    achievementElements.forEach((element, index) => {
                        if (achievements[index]) {
                            const achievement = achievements[index];
                            const checkIcon = element.querySelector('.fa-check');
                            
                            if (achievement.unlocked) {
                                element.classList.remove('bg-gray-100', 'text-gray-500');
                                element.classList.add('bg-yellow-100', 'text-yellow-800');
                                if (checkIcon) {
                                    checkIcon.classList.remove('hidden');
                                }
                            } else {
                                element.classList.add('bg-gray-100', 'text-gray-500');
                                element.classList.remove('bg-yellow-100', 'text-yellow-800');
                                if (checkIcon) {
                                    checkIcon.classList.add('hidden');
                                }
                            }
                        }
                    });
                }
                
                formatValue(value, format = 'number') {
                    switch (format) {
                        case 'currency':
                            return '$' + Number(value).toLocaleString();
                        case 'percentage':
                            return Number(value).toFixed(1) + '%';
                        case 'number':
                        default:
                            return Number(value).toLocaleString();
                    }
                }
                
                flashIndicator() {
                    const indicator = this.element.querySelector('.progress-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-green-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-green-400');
                        }, 200);
                    }
                }
                
                showError() {
                    const indicator = this.element.querySelector('.progress-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-red-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-red-400');
                        }, 1000);
                    }
                }
            }
            
            // Initialize progress widgets
            document.addEventListener('DOMContentLoaded', function() {
                const progressWidgets = document.querySelectorAll('[data-progress-widget]');
                progressWidgets.forEach(widget => new ProgressWidget(widget));
            });
        </script>
    @endpush
@endonce
