@props([
    'title' => 'Alert Widget',
    'type' => 'info', // success, info, warning, error, custom
    'variant' => 'filled', // filled, outlined, ghost, solid, gradient
    'size' => 'md', // sm, md, lg, xl
    'icon' => null,
    'iconPosition' => 'left', // left, right, top
    'message' => '',
    'description' => null,
    'showIcon' => true,
    'showTitle' => true,
    'showClose' => true,
    'showActions' => true,
    'dismissible' => true,
    'autoClose' => false,
    'autoCloseDelay' => 5000,
    'bordered' => true,
    'rounded' => true,
    'shadow' => true,
    'animated' => true,
    'pulse' => false,
    'blinking' => false,
    'progress' => false,
    'progressDuration' => 5000,
    'customClass' => '',
    'theme' => 'light',
    'id' => null,
    'status' => 'active', // active, inactive, pending, resolved, archived
    'priority' => 'normal', // low, normal, high, critical, urgent
    'timestamp' => null,
    'source' => null,
    'category' => null,
    'actions' => [],
    'data' => [],
    'realTimeEndpoint' => null,
    'refreshInterval' => 30000,
    'soundAlert' => false,
    'soundFile' => null,
    'persistent' => false,
    'collapsible' => false,
    'collapsed' => false,
    'stackable' => false,
    'maxHeight' => null,
    'overflow' => 'visible'
])

@php
    $componentId = $id ?? 'alert-widget-' . uniqid();
    
    // Alert type configurations
    $alertTypes = [
        'success' => [
            'icon' => 'check-circle',
            'colors' => [
                'filled' => 'bg-green-500 text-white border-green-500',
                'outlined' => 'bg-transparent text-green-700 border-green-500',
                'ghost' => 'bg-green-50 text-green-700 border-green-200',
                'solid' => 'bg-green-100 text-green-800 border-green-300',
                'gradient' => 'bg-gradient-to-r from-green-400 to-green-600 text-white border-transparent'
            ],
            'iconColor' => 'text-green-500',
            'buttonColor' => 'bg-green-600 hover:bg-green-700'
        ],
        'info' => [
            'icon' => 'info-circle',
            'colors' => [
                'filled' => 'bg-blue-500 text-white border-blue-500',
                'outlined' => 'bg-transparent text-blue-700 border-blue-500',
                'ghost' => 'bg-blue-50 text-blue-700 border-blue-200',
                'solid' => 'bg-blue-100 text-blue-800 border-blue-300',
                'gradient' => 'bg-gradient-to-r from-blue-400 to-blue-600 text-white border-transparent'
            ],
            'iconColor' => 'text-blue-500',
            'buttonColor' => 'bg-blue-600 hover:bg-blue-700'
        ],
        'warning' => [
            'icon' => 'exclamation-triangle',
            'colors' => [
                'filled' => 'bg-yellow-500 text-white border-yellow-500',
                'outlined' => 'bg-transparent text-yellow-700 border-yellow-500',
                'ghost' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'solid' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'gradient' => 'bg-gradient-to-r from-yellow-400 to-yellow-600 text-white border-transparent'
            ],
            'iconColor' => 'text-yellow-500',
            'buttonColor' => 'bg-yellow-600 hover:bg-yellow-700'
        ],
        'error' => [
            'icon' => 'times-circle',
            'colors' => [
                'filled' => 'bg-red-500 text-white border-red-500',
                'outlined' => 'bg-transparent text-red-700 border-red-500',
                'ghost' => 'bg-red-50 text-red-700 border-red-200',
                'solid' => 'bg-red-100 text-red-800 border-red-300',
                'gradient' => 'bg-gradient-to-r from-red-400 to-red-600 text-white border-transparent'
            ],
            'iconColor' => 'text-red-500',
            'buttonColor' => 'bg-red-600 hover:bg-red-700'
        ],
        'custom' => [
            'icon' => 'bell',
            'colors' => [
                'filled' => 'bg-gray-500 text-white border-gray-500',
                'outlined' => 'bg-transparent text-gray-700 border-gray-500',
                'ghost' => 'bg-gray-50 text-gray-700 border-gray-200',
                'solid' => 'bg-gray-100 text-gray-800 border-gray-300',
                'gradient' => 'bg-gradient-to-r from-gray-400 to-gray-600 text-white border-transparent'
            ],
            'iconColor' => 'text-gray-500',
            'buttonColor' => 'bg-gray-600 hover:bg-gray-700'
        ]
    ];
    
    $config = $alertTypes[$type] ?? $alertTypes['info'];
    $alertIcon = $icon ?? $config['icon'];
    
    // Size configurations
    $sizeConfig = [
        'sm' => [
            'padding' => 'p-3',
            'text' => 'text-sm',
            'titleText' => 'text-base',
            'iconSize' => 'text-lg',
            'buttonSize' => 'px-3 py-1 text-xs',
            'spacing' => 'space-x-2'
        ],
        'md' => [
            'padding' => 'p-4',
            'text' => 'text-base',
            'titleText' => 'text-lg',
            'iconSize' => 'text-xl',
            'buttonSize' => 'px-4 py-2 text-sm',
            'spacing' => 'space-x-3'
        ],
        'lg' => [
            'padding' => 'p-6',
            'text' => 'text-lg',
            'titleText' => 'text-xl',
            'iconSize' => 'text-2xl',
            'buttonSize' => 'px-6 py-3 text-base',
            'spacing' => 'space-x-4'
        ],
        'xl' => [
            'padding' => 'p-8',
            'text' => 'text-xl',
            'titleText' => 'text-2xl',
            'iconSize' => 'text-3xl',
            'buttonSize' => 'px-8 py-4 text-lg',
            'spacing' => 'space-x-6'
        ]
    ];
    
    $sizeClasses = $sizeConfig[$size] ?? $sizeConfig['md'];
    
    // Priority indicators
    $priorityConfig = [
        'low' => ['color' => 'bg-gray-400', 'pulse' => false],
        'normal' => ['color' => 'bg-blue-400', 'pulse' => false],
        'high' => ['color' => 'bg-orange-400', 'pulse' => true],
        'critical' => ['color' => 'bg-red-500', 'pulse' => true],
        'urgent' => ['color' => 'bg-red-600', 'pulse' => true]
    ];
    
    $prioritySettings = $priorityConfig[$priority] ?? $priorityConfig['normal'];
    
    // Status indicators
    $statusConfig = [
        'active' => ['color' => 'bg-green-500', 'label' => 'Active'],
        'inactive' => ['color' => 'bg-gray-400', 'label' => 'Inactive'],
        'pending' => ['color' => 'bg-yellow-500', 'label' => 'Pending'],
        'resolved' => ['color' => 'bg-blue-500', 'label' => 'Resolved'],
        'archived' => ['color' => 'bg-gray-600', 'label' => 'Archived']
    ];
    
    $statusSettings = $statusConfig[$status] ?? $statusConfig['active'];
@endphp

<div 
    id="{{ $componentId }}"
    @class([
        'alert-widget relative overflow-hidden transition-all duration-300',
        $config['colors'][$variant],
        $sizeClasses['padding'],
        'rounded-lg' => $rounded,
        'border' => $bordered,
        'shadow-md' => $shadow,
        'dark:bg-gray-800 dark:border-gray-700' => $theme === 'dark' && $variant === 'ghost',
        'opacity-50' => $status === 'inactive',
        'animate-pulse' => $pulse || $prioritySettings['pulse'],
        'animate-bounce' => $blinking,
        $customClass
    ])
    data-alert-widget
    data-type="{{ $type }}"
    data-status="{{ $status }}"
    data-priority="{{ $priority }}"
    data-dismissible="{{ $dismissible ? 'true' : 'false' }}"
    data-auto-close="{{ $autoClose ? 'true' : 'false' }}"
    data-auto-close-delay="{{ $autoCloseDelay }}"
    data-collapsible="{{ $collapsible ? 'true' : 'false' }}"
    data-collapsed="{{ $collapsed ? 'true' : 'false' }}"
    @if($realTimeEndpoint)
        data-realtime-endpoint="{{ $realTimeEndpoint }}"
        data-refresh-interval="{{ $refreshInterval }}"
    @endif
    @if($soundAlert && $soundFile)
        data-sound-alert="{{ $soundFile }}"
    @endif
    role="alert"
    aria-live="polite"
    style="{{ $maxHeight ? 'max-height: ' . $maxHeight . '; overflow-y: ' . $overflow . ';' : '' }}"
>
    <!-- Progress Bar for Auto Close -->
    @if($progress && $autoClose)
        <div class="absolute top-0 left-0 h-1 bg-black bg-opacity-20 w-full">
            <div 
                class="h-full bg-white bg-opacity-40 progress-bar"
                style="animation: alert-progress {{ $progressDuration }}ms linear;"
            ></div>
        </div>
    @endif
    
    <!-- Priority Indicator -->
    @if($priority !== 'normal')
        <div @class([
            'absolute top-0 left-0 w-1 h-full',
            $prioritySettings['color'],
            'animate-pulse' => $prioritySettings['pulse']
        ])></div>
    @endif
    
    <!-- Main Content -->
    <div @class([
        'flex items-start',
        $sizeClasses['spacing'],
        'ml-1' => $priority !== 'normal'
    ])>
        <!-- Icon -->
        @if($showIcon && $iconPosition === 'left')
            <div class="flex-shrink-0">
                @if($variant === 'filled' || $variant === 'gradient')
                    <i class="fas fa-{{ $alertIcon }} {{ $sizeClasses['iconSize'] }}"></i>
                @else
                    <i class="fas fa-{{ $alertIcon }} {{ $config['iconColor'] }} {{ $sizeClasses['iconSize'] }}"></i>
                @endif
            </div>
        @endif
        
        <!-- Content -->
        <div class="flex-1 min-w-0">
            @if($iconPosition === 'top' && $showIcon)
                <div class="flex justify-center mb-3">
                    @if($variant === 'filled' || $variant === 'gradient')
                        <i class="fas fa-{{ $alertIcon }} {{ $sizeClasses['iconSize'] }}"></i>
                    @else
                        <i class="fas fa-{{ $alertIcon }} {{ $config['iconColor'] }} {{ $sizeClasses['iconSize'] }}"></i>
                    @endif
                </div>
            @endif
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    @if($showTitle && $title)
                        <h3 @class([
                            'font-semibold',
                            $sizeClasses['titleText']
                        ])>
                            {{ $title }}
                        </h3>
                    @endif
                    
                    <!-- Status Indicator -->
                    <div class="flex items-center space-x-1">
                        <div @class([
                            'w-2 h-2 rounded-full',
                            $statusSettings['color']
                        ])></div>
                        <span @class([
                            'text-xs opacity-75'
                        ])>
                            {{ $statusSettings['label'] }}
                        </span>
                    </div>
                    
                    <!-- Category Badge -->
                    @if($category)
                        <span @class([
                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                            'bg-black bg-opacity-20' => $variant === 'filled' || $variant === 'gradient',
                            'bg-gray-100 text-gray-800' => $variant !== 'filled' && $variant !== 'gradient'
                        ])>
                            {{ $category }}
                        </span>
                    @endif
                </div>
                
                <!-- Metadata -->
                <div class="flex items-center space-x-2 text-xs opacity-75">
                    @if($source)
                        <span>{{ $source }}</span>
                    @endif
                    
                    @if($timestamp)
                        <span>{{ $timestamp }}</span>
                    @endif
                    
                    @if($realTimeEndpoint)
                        <div class="flex items-center space-x-1">
                            <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse alert-realtime-indicator"></div>
                            <span>Live</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Message -->
            @if($message)
                <div @class([
                    'alert-message',
                    $sizeClasses['text'],
                    'mb-3' => $description || $showActions || !empty($actions) || !empty($data)
                ])>
                    {{ $message }}
                </div>
            @endif
            
            <!-- Description -->
            @if($description)
                <div @class([
                    'alert-description opacity-90',
                    $sizeClasses['text'],
                    'mb-3' => $showActions || !empty($actions) || !empty($data)
                ])>
                    {{ $description }}
                </div>
            @endif
            
            <!-- Collapsible Content -->
            @if($collapsible && (!empty($data) || $slot->isNotEmpty()))
                <div 
                    class="collapsible-content {{ $collapsed ? 'hidden' : '' }}"
                    data-collapsible-content
                >
                    <!-- Additional Data -->
                    @if(!empty($data))
                        <div class="mt-3 space-y-2">
                            @foreach($data as $key => $value)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-medium opacity-75">{{ $key }}:</span>
                                    <span>{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Slot Content -->
                    {{ $slot }}
                </div>
                
                @if($collapsible)
                    <button 
                        type="button"
                        class="mt-2 text-xs underline opacity-75 hover:opacity-100 collapsible-toggle"
                        data-collapsible-toggle
                    >
                        <span class="show-text {{ $collapsed ? '' : 'hidden' }}">Show Details</span>
                        <span class="hide-text {{ $collapsed ? 'hidden' : '' }}">Hide Details</span>
                        <i class="fas fa-chevron-down ml-1 transform transition-transform collapsible-icon {{ $collapsed ? '' : 'rotate-180' }}"></i>
                    </button>
                @endif
            @else
                <!-- Additional Data (always visible) -->
                @if(!empty($data))
                    <div class="mt-3 space-y-2">
                        @foreach($data as $key => $value)
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-medium opacity-75">{{ $key }}:</span>
                                <span>{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Slot Content -->
                {{ $slot }}
            @endif
            
            <!-- Actions -->
            @if($showActions && !empty($actions))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($actions as $action)
                        <button 
                            type="button"
                            @class([
                                'inline-flex items-center font-medium rounded-md transition-colors duration-200',
                                $sizeClasses['buttonSize'],
                                $action['style'] === 'primary' ? $config['buttonColor'] . ' text-white' : '',
                                $action['style'] === 'secondary' ? 'bg-white bg-opacity-20 hover:bg-opacity-30' : '',
                                $action['style'] === 'ghost' ? 'hover:bg-white hover:bg-opacity-10' : '',
                                $action['style'] === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : '',
                                $action['style'] === 'outline' ? 'border border-current hover:bg-current hover:text-white' : ''
                            ])
                            @if(isset($action['action']))
                                onclick="{{ $action['action'] }}"
                            @endif
                            @if(isset($action['href']))
                                onclick="window.location.href='{{ $action['href'] }}'"
                            @endif
                            @if(isset($action['disabled']) && $action['disabled'])
                                disabled
                            @endif
                        >
                            @if(isset($action['icon']))
                                <i class="fas fa-{{ $action['icon'] }} mr-2"></i>
                            @endif
                            {{ $action['label'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Right Icon -->
        @if($showIcon && $iconPosition === 'right')
            <div class="flex-shrink-0">
                @if($variant === 'filled' || $variant === 'gradient')
                    <i class="fas fa-{{ $alertIcon }} {{ $sizeClasses['iconSize'] }}"></i>
                @else
                    <i class="fas fa-{{ $alertIcon }} {{ $config['iconColor'] }} {{ $sizeClasses['iconSize'] }}"></i>
                @endif
            </div>
        @endif
        
        <!-- Close Button -->
        @if($showClose && $dismissible)
            <div class="flex-shrink-0">
                <button 
                    type="button" 
                    class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-black hover:bg-opacity-10 alert-close-btn"
                    data-alert-close
                    aria-label="Close alert"
                >
                    <i class="fas fa-times {{ $sizeClasses['iconSize'] }}"></i>
                </button>
            </div>
        @endif
    </div>
    
    <!-- Stack Indicator -->
    @if($stackable)
        <div class="absolute -bottom-1 -right-1 w-full h-full bg-black bg-opacity-5 rounded-lg -z-10"></div>
        <div class="absolute -bottom-2 -right-2 w-full h-full bg-black bg-opacity-5 rounded-lg -z-20"></div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .alert-widget {
                position: relative;
                word-wrap: break-word;
            }
            
            .alert-widget:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .alert-widget[data-status="inactive"] {
                opacity: 0.6;
                filter: grayscale(20%);
            }
            
            .alert-widget[data-priority="critical"],
            .alert-widget[data-priority="urgent"] {
                animation: alert-urgent 2s ease-in-out infinite;
            }
            
            @keyframes alert-urgent {
                0%, 100% { 
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); 
                }
                50% { 
                    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); 
                }
            }
            
            @keyframes alert-progress {
                from { width: 100%; }
                to { width: 0%; }
            }
            
            .alert-widget .progress-bar {
                width: 100%;
                animation: alert-progress var(--duration, 5000ms) linear;
            }
            
            .alert-widget[data-collapsible="true"] .collapsible-content {
                transition: all 0.3s ease;
                overflow: hidden;
            }
            
            .alert-widget[data-collapsed="true"] .collapsible-content {
                max-height: 0;
                opacity: 0;
                margin: 0;
                padding: 0;
            }
            
            .alert-widget .collapsible-toggle:hover .collapsible-icon {
                transform: scale(1.1);
            }
            
            .alert-widget .alert-close-btn:hover {
                background-color: rgba(0, 0, 0, 0.1);
            }
            
            .alert-widget .alert-message,
            .alert-widget .alert-description {
                line-height: 1.6;
            }
            
            .alert-widget .alert-realtime-indicator {
                animation: realtime-pulse 2s ease-in-out infinite;
            }
            
            @keyframes realtime-pulse {
                0%, 100% { 
                    opacity: 1; 
                    transform: scale(1);
                }
                50% { 
                    opacity: 0.5; 
                    transform: scale(1.2);
                }
            }
            
            /* Responsive adjustments */
            @media (max-width: 640px) {
                .alert-widget .flex-wrap {
                    flex-direction: column;
                }
                
                .alert-widget .flex-wrap > * {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .alert-widget .flex-wrap > *:last-child {
                    margin-bottom: 0;
                }
            }
            
            /* Dark theme enhancements */
            .dark .alert-widget {
                border-color: rgba(55, 65, 81, 0.3);
            }
            
            .dark .alert-widget:hover {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            }
            
            /* Print styles */
            @media print {
                .alert-widget {
                    break-inside: avoid;
                    box-shadow: none;
                    border: 1px solid #ccc;
                }
                
                .alert-widget .alert-close-btn,
                .alert-widget .collapsible-toggle {
                    display: none;
                }
            }
        </style>
    @endpush
    
    @push('scripts')
        <script>
            class AlertWidget {
                constructor(element) {
                    this.element = element;
                    this.type = element.dataset.type;
                    this.status = element.dataset.status;
                    this.priority = element.dataset.priority;
                    this.dismissible = element.dataset.dismissible === 'true';
                    this.autoClose = element.dataset.autoClose === 'true';
                    this.autoCloseDelay = parseInt(element.dataset.autoCloseDelay) || 5000;
                    this.collapsible = element.dataset.collapsible === 'true';
                    this.collapsed = element.dataset.collapsed === 'true';
                    this.endpoint = element.dataset.realtimeEndpoint;
                    this.interval = parseInt(element.dataset.refreshInterval) || 30000;
                    this.soundAlert = element.dataset.soundAlert;
                    
                    this.init();
                }
                
                init() {
                    this.setupEventListeners();
                    
                    if (this.autoClose) {
                        this.startAutoClose();
                    }
                    
                    if (this.endpoint) {
                        this.startRealTimeUpdates();
                    }
                    
                    if (this.soundAlert) {
                        this.playSound();
                    }
                    
                    // Announce to screen readers
                    this.announceAlert();
                }
                
                setupEventListeners() {
                    // Close button
                    const closeBtn = this.element.querySelector('[data-alert-close]');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', () => this.close());
                    }
                    
                    // Collapsible toggle
                    const toggleBtn = this.element.querySelector('[data-collapsible-toggle]');
                    if (toggleBtn) {
                        toggleBtn.addEventListener('click', () => this.toggleCollapse());
                    }
                    
                    // Keyboard navigation
                    this.element.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && this.dismissible) {
                            this.close();
                        }
                        if (e.key === 'Enter' || e.key === ' ') {
                            if (e.target.matches('[data-collapsible-toggle]')) {
                                e.preventDefault();
                                this.toggleCollapse();
                            }
                        }
                    });
                    
                    // Action buttons
                    const actionButtons = this.element.querySelectorAll('button[onclick]');
                    actionButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            this.trackAction(button.textContent.trim());
                        });
                    });
                }
                
                close() {
                    if (!this.dismissible) return;
                    
                    this.element.style.animation = 'slideOut 0.3s ease-out forwards';
                    
                    setTimeout(() => {
                        this.element.remove();
                        this.dispatchEvent('alert:closed');
                    }, 300);
                }
                
                toggleCollapse() {
                    if (!this.collapsible) return;
                    
                    this.collapsed = !this.collapsed;
                    this.element.dataset.collapsed = this.collapsed;
                    
                    const content = this.element.querySelector('[data-collapsible-content]');
                    const showText = this.element.querySelector('.show-text');
                    const hideText = this.element.querySelector('.hide-text');
                    const icon = this.element.querySelector('.collapsible-icon');
                    
                    if (content) {
                        if (this.collapsed) {
                            content.classList.add('hidden');
                            content.style.maxHeight = '0';
                            content.style.opacity = '0';
                        } else {
                            content.classList.remove('hidden');
                            content.style.maxHeight = 'none';
                            content.style.opacity = '1';
                        }
                    }
                    
                    if (showText && hideText) {
                        showText.classList.toggle('hidden', !this.collapsed);
                        hideText.classList.toggle('hidden', this.collapsed);
                    }
                    
                    if (icon) {
                        icon.style.transform = this.collapsed ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                    
                    this.dispatchEvent('alert:toggled', { collapsed: this.collapsed });
                }
                
                startAutoClose() {
                    setTimeout(() => {
                        this.close();
                    }, this.autoCloseDelay);
                }
                
                async startRealTimeUpdates() {
                    this.updateData();
                    setInterval(() => this.updateData(), this.interval);
                }
                
                async updateData() {
                    try {
                        const response = await fetch(this.endpoint);
                        const data = await response.json();
                        
                        this.updateAlert(data);
                        this.flashIndicator();
                    } catch (error) {
                        console.error('Failed to update alert data:', error);
                        this.showError();
                    }
                }
                
                updateAlert(data) {
                    // Update status
                    if (data.status && data.status !== this.status) {
                        this.status = data.status;
                        this.element.dataset.status = data.status;
                        this.updateStatusIndicator(data.status);
                    }
                    
                    // Update message
                    if (data.message) {
                        const messageEl = this.element.querySelector('.alert-message');
                        if (messageEl) {
                            messageEl.textContent = data.message;
                        }
                    }
                    
                    // Update description
                    if (data.description) {
                        const descEl = this.element.querySelector('.alert-description');
                        if (descEl) {
                            descEl.textContent = data.description;
                        }
                    }
                    
                    // Update timestamp
                    if (data.timestamp) {
                        const timestampEl = this.element.querySelector('[data-timestamp]');
                        if (timestampEl) {
                            timestampEl.textContent = data.timestamp;
                        }
                    }
                    
                    // Update additional data
                    if (data.data) {
                        this.updateDataFields(data.data);
                    }
                    
                    this.dispatchEvent('alert:updated', data);
                }
                
                updateStatusIndicator(status) {
                    const statusConfig = {
                        'active': { color: 'bg-green-500', label: 'Active' },
                        'inactive': { color: 'bg-gray-400', label: 'Inactive' },
                        'pending': { color: 'bg-yellow-500', label: 'Pending' },
                        'resolved': { color: 'bg-blue-500', label: 'Resolved' },
                        'archived': { color: 'bg-gray-600', label: 'Archived' }
                    };
                    
                    const config = statusConfig[status] || statusConfig['active'];
                    const indicator = this.element.querySelector('.w-2.h-2.rounded-full');
                    const label = indicator ? indicator.nextElementSibling : null;
                    
                    if (indicator) {
                        indicator.className = `w-2 h-2 rounded-full ${config.color}`;
                    }
                    
                    if (label) {
                        label.textContent = config.label;
                    }
                }
                
                updateDataFields(data) {
                    Object.entries(data).forEach(([key, value]) => {
                        const field = this.element.querySelector(`[data-field="${key}"]`);
                        if (field) {
                            field.textContent = value;
                        }
                    });
                }
                
                playSound() {
                    if (this.soundAlert) {
                        const audio = new Audio(this.soundAlert);
                        audio.volume = 0.3;
                        audio.play().catch(error => {
                            console.warn('Could not play alert sound:', error);
                        });
                    }
                }
                
                announceAlert() {
                    const message = this.element.querySelector('.alert-message');
                    if (message && this.priority === 'critical' || this.priority === 'urgent') {
                        // Create announcement for screen readers
                        const announcement = document.createElement('div');
                        announcement.setAttribute('aria-live', 'assertive');
                        announcement.setAttribute('aria-atomic', 'true');
                        announcement.className = 'sr-only';
                        announcement.textContent = `${this.type} alert: ${message.textContent}`;
                        document.body.appendChild(announcement);
                        
                        setTimeout(() => {
                            document.body.removeChild(announcement);
                        }, 1000);
                    }
                }
                
                trackAction(actionName) {
                    this.dispatchEvent('alert:action', { 
                        action: actionName,
                        alert: {
                            type: this.type,
                            status: this.status,
                            priority: this.priority
                        }
                    });
                }
                
                flashIndicator() {
                    const indicator = this.element.querySelector('.alert-realtime-indicator');
                    if (indicator) {
                        indicator.style.backgroundColor = '#10b981';
                        setTimeout(() => {
                            indicator.style.backgroundColor = '';
                        }, 200);
                    }
                }
                
                showError() {
                    const indicator = this.element.querySelector('.alert-realtime-indicator');
                    if (indicator) {
                        indicator.style.backgroundColor = '#ef4444';
                        setTimeout(() => {
                            indicator.style.backgroundColor = '';
                        }, 1000);
                    }
                }
                
                dispatchEvent(eventName, detail = {}) {
                    const event = new CustomEvent(eventName, {
                        detail: { ...detail, element: this.element },
                        bubbles: true
                    });
                    this.element.dispatchEvent(event);
                }
                
                // Public methods
                updateStatus(status) {
                    this.status = status;
                    this.element.dataset.status = status;
                    this.updateStatusIndicator(status);
                }
                
                updateMessage(message) {
                    const messageEl = this.element.querySelector('.alert-message');
                    if (messageEl) {
                        messageEl.textContent = message;
                    }
                }
                
                show() {
                    this.element.style.display = 'block';
                    this.element.style.animation = 'slideIn 0.3s ease-out forwards';
                }
                
                hide() {
                    this.element.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        this.element.style.display = 'none';
                    }, 300);
                }
            }
            
            // Initialize alert widgets
            document.addEventListener('DOMContentLoaded', function() {
                const alertWidgets = document.querySelectorAll('[data-alert-widget]');
                alertWidgets.forEach(widget => new AlertWidget(widget));
            });
            
            // Global alert creation function
            window.createAlert = function(options) {
                const alertContainer = document.getElementById('alert-container') || document.body;
                const alertHTML = `
                    <div class="alert-widget ${options.classes || ''}" data-alert-widget data-type="${options.type || 'info'}">
                        <!-- Alert content based on options -->
                    </div>
                `;
                
                alertContainer.insertAdjacentHTML('beforeend', alertHTML);
                const newAlert = alertContainer.lastElementChild;
                new AlertWidget(newAlert);
                
                return newAlert;
            };
            
            // CSS Animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes slideOut {
                    from {
                        opacity: 1;
                        transform: translateY(0);
                    }
                    to {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                }
            `;
            document.head.appendChild(style);
        </script>
    @endpush
@endonce
