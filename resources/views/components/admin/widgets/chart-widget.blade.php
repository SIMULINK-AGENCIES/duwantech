@props([
    'title' => 'Chart Widget',
    'type' => 'line', // line, bar, pie, donut, area, scatter
    'data' => [],
    'labels' => [],
    'datasets' => [],
    'height' => '400px',
    'width' => '100%',
    'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16'],
    'loading' => false,
    'realTimeEndpoint' => null,
    'refreshInterval' => 30000,
    'showLegend' => true,
    'showGrid' => true,
    'showTooltips' => true,
    'drillDown' => false,
    'drillDownEndpoint' => null,
    'responsive' => true,
    'animated' => true,
    'theme' => 'light',
    'customOptions' => [],
    'id' => null,
    'customClass' => '',
    'showControls' => true,
    'exportable' => false,
    'zoomable' => false
])

@php
    $componentId = $id ?? 'chart-widget-' . uniqid();
    $chartId = $componentId . '-chart';
    
    // Default color schemes
    $lightColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'];
    $darkColors = ['#60A5FA', '#34D399', '#FBBF24', '#F87171', '#A78BFA', '#22D3EE', '#A3E635', '#FB923C', '#F472B6', '#818CF8'];
    
    $finalColors = $theme === 'dark' ? $darkColors : $lightColors;
    if (!empty($colors)) {
        $finalColors = array_merge($colors, $finalColors);
    }
    
    // Prepare chart data
    $chartData = [
        'labels' => $labels,
        'datasets' => $datasets
    ];
    
    if (empty($datasets) && !empty($data)) {
        // Single dataset from data array
        $chartData['datasets'] = [[
            'label' => $title,
            'data' => $data,
            'backgroundColor' => $type === 'pie' || $type === 'donut' ? $finalColors : $finalColors[0],
            'borderColor' => $finalColors[0],
            'borderWidth' => 2,
            'fill' => $type === 'area'
        ]];
    }
@endphp

<div 
    id="{{ $componentId }}"
    @class([
        'chart-widget bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden',
        'dark:bg-gray-800 dark:border-gray-700' => $theme === 'dark',
        $customClass
    ])
    @if($realTimeEndpoint)
        data-realtime-endpoint="{{ $realTimeEndpoint }}"
        data-refresh-interval="{{ $refreshInterval }}"
    @endif
    @if($drillDown && $drillDownEndpoint)
        data-drilldown-endpoint="{{ $drillDownEndpoint }}"
    @endif
    data-chart-widget
>
    <!-- Header -->
    <div @class([
        'px-6 py-4 border-b border-gray-200 flex items-center justify-between',
        'dark:border-gray-700' => $theme === 'dark'
    ])>
        <div class="flex items-center space-x-3">
            <h3 @class([
                'text-lg font-semibold text-gray-900',
                'dark:text-white' => $theme === 'dark'
            ])>{{ $title }}</h3>
            
            @if($realTimeEndpoint)
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse chart-realtime-indicator"></div>
                    <span @class([
                        'text-xs text-gray-500',
                        'dark:text-gray-400' => $theme === 'dark'
                    ])>Live</span>
                </div>
            @endif
        </div>
        
        @if($showControls)
            <div class="flex items-center space-x-2">
                <!-- Chart type selector -->
                <div class="relative">
                    <select 
                        class="chart-type-selector text-sm border border-gray-300 rounded px-3 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        data-chart-id="{{ $chartId }}"
                    >
                        <option value="line" {{ $type === 'line' ? 'selected' : '' }}>Line</option>
                        <option value="bar" {{ $type === 'bar' ? 'selected' : '' }}>Bar</option>
                        <option value="pie" {{ $type === 'pie' ? 'selected' : '' }}>Pie</option>
                        <option value="donut" {{ $type === 'donut' ? 'selected' : '' }}>Donut</option>
                        <option value="area" {{ $type === 'area' ? 'selected' : '' }}>Area</option>
                    </select>
                </div>
                
                @if($exportable)
                    <button 
                        type="button"
                        class="export-chart-btn p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        data-chart-id="{{ $chartId }}"
                        title="Export Chart"
                    >
                        <i class="fas fa-download"></i>
                    </button>
                @endif
                
                @if($zoomable)
                    <button 
                        type="button"
                        class="reset-zoom-btn p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        data-chart-id="{{ $chartId }}"
                        title="Reset Zoom"
                    >
                        <i class="fas fa-search-minus"></i>
                    </button>
                @endif
                
                <button 
                    type="button"
                    class="refresh-chart-btn p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    data-chart-id="{{ $chartId }}"
                    title="Refresh Data"
                >
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        @endif
    </div>
    
    <!-- Chart Container -->
    <div class="relative px-6 py-4">
        @if($loading)
            <div class="absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                <div class="flex flex-col items-center space-y-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-sm text-gray-500">Loading chart data...</span>
                </div>
            </div>
        @endif
        
        <div class="chart-container" style="height: {{ $height }}; width: {{ $width }};">
            <canvas 
                id="{{ $chartId }}"
                class="chart-canvas"
                @if($drillDown)
                    style="cursor: pointer;"
                @endif
            ></canvas>
        </div>
        
        <!-- No Data Message -->
        <div class="no-data-message hidden flex items-center justify-center" style="height: {{ $height }};">
            <div class="text-center">
                <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No data available</p>
            </div>
        </div>
    </div>
    
    <!-- Drill-down Modal -->
    @if($drillDown)
        <div id="{{ $componentId }}-modal" class="drill-down-modal fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-auto">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Detailed View</h3>
                        <button type="button" class="close-modal text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="drill-down-content p-6">
                        <!-- Drill-down content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .chart-widget {
                transition: all 0.3s ease;
            }
            
            .chart-widget:hover {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .chart-container {
                position: relative;
            }
            
            .chart-canvas {
                transition: opacity 0.3s ease;
            }
            
            .chart-realtime-indicator {
                animation: pulse 2s ease-in-out infinite;
            }
            
            .drill-down-modal {
                backdrop-filter: blur(4px);
            }
            
            .export-chart-btn:hover,
            .refresh-chart-btn:hover,
            .reset-zoom-btn:hover {
                transform: scale(1.1);
                transition: transform 0.2s ease;
            }
            
            .chart-type-selector {
                transition: all 0.2s ease;
            }
            
            .chart-type-selector:focus {
                transform: scale(1.02);
            }
        </style>
    @endpush
    
    @push('scripts')
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
        
        <script>
            class ChartWidget {
                constructor(element) {
                    this.element = element;
                    this.chartId = element.querySelector('.chart-canvas').id;
                    this.canvas = document.getElementById(this.chartId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    
                    this.endpoint = element.dataset.realtimeEndpoint;
                    this.interval = parseInt(element.dataset.refreshInterval) || 30000;
                    this.drillDownEndpoint = element.dataset.drilldownEndpoint;
                    
                    this.chartData = @json($chartData);
                    this.chartType = '{{ $type }}';
                    this.colors = @json($finalColors);
                    this.options = this.getChartOptions();
                    
                    this.initChart();
                    this.bindEvents();
                    
                    if (this.endpoint) {
                        this.startRealTimeUpdates();
                    }
                }
                
                getChartOptions() {
                    const baseOptions = {
                        responsive: {{ $responsive ? 'true' : 'false' }},
                        maintainAspectRatio: false,
                        animation: {
                            duration: {{ $animated ? '1000' : '0' }},
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: {{ $showLegend ? 'true' : 'false' }},
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            },
                            tooltip: {
                                enabled: {{ $showTooltips ? 'true' : 'false' }},
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.y || context.parsed;
                                        return label + ': ' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: this.getScaleOptions(),
                        onHover: (event, elements) => {
                            this.canvas.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                        },
                        onClick: (event, elements) => {
                            if (elements.length > 0 && {{ $drillDown ? 'true' : 'false' }}) {
                                this.handleDrillDown(elements[0]);
                            }
                        }
                    };
                    
                    // Add zoom plugin options if zoomable
                    if ({{ $zoomable ? 'true' : 'false' }}) {
                        baseOptions.plugins.zoom = {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy',
                            },
                            pan: {
                                enabled: true,
                                mode: 'xy',
                            }
                        };
                    }
                    
                    return { ...baseOptions, ...@json($customOptions) };
                }
                
                getScaleOptions() {
                    if (this.chartType === 'pie' || this.chartType === 'donut') {
                        return {};
                    }
                    
                    return {
                        x: {
                            display: true,
                            grid: {
                                display: {{ $showGrid ? 'true' : 'false' }},
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        y: {
                            display: true,
                            grid: {
                                display: {{ $showGrid ? 'true' : 'false' }},
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            beginAtZero: true
                        }
                    };
                }
                
                initChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    
                    // Apply colors to datasets
                    this.chartData.datasets.forEach((dataset, index) => {
                        if (this.chartType === 'pie' || this.chartType === 'donut') {
                            dataset.backgroundColor = this.colors.slice(0, dataset.data.length);
                            dataset.borderColor = this.colors.slice(0, dataset.data.length);
                        } else {
                            dataset.backgroundColor = this.chartType === 'area' ? 
                                this.hexToRgba(this.colors[index % this.colors.length], 0.2) : 
                                this.colors[index % this.colors.length];
                            dataset.borderColor = this.colors[index % this.colors.length];
                        }
                    });
                    
                    const chartType = this.chartType === 'donut' ? 'doughnut' : 
                                     this.chartType === 'area' ? 'line' : this.chartType;
                    
                    this.chart = new Chart(this.ctx, {
                        type: chartType,
                        data: this.chartData,
                        options: this.options
                    });
                    
                    this.toggleNoDataMessage(false);
                }
                
                bindEvents() {
                    // Chart type selector
                    const typeSelector = this.element.querySelector('.chart-type-selector');
                    if (typeSelector) {
                        typeSelector.addEventListener('change', (e) => {
                            this.changeChartType(e.target.value);
                        });
                    }
                    
                    // Export button
                    const exportBtn = this.element.querySelector('.export-chart-btn');
                    if (exportBtn) {
                        exportBtn.addEventListener('click', () => this.exportChart());
                    }
                    
                    // Refresh button
                    const refreshBtn = this.element.querySelector('.refresh-chart-btn');
                    if (refreshBtn) {
                        refreshBtn.addEventListener('click', () => this.refreshData());
                    }
                    
                    // Reset zoom button
                    const resetZoomBtn = this.element.querySelector('.reset-zoom-btn');
                    if (resetZoomBtn) {
                        resetZoomBtn.addEventListener('click', () => this.resetZoom());
                    }
                    
                    // Modal close button
                    const closeModalBtn = this.element.querySelector('.close-modal');
                    if (closeModalBtn) {
                        closeModalBtn.addEventListener('click', () => this.closeDrillDownModal());
                    }
                }
                
                changeChartType(newType) {
                    this.chartType = newType;
                    this.options = this.getChartOptions();
                    this.initChart();
                }
                
                async startRealTimeUpdates() {
                    this.updateData();
                    setInterval(() => this.updateData(), this.interval);
                }
                
                async updateData() {
                    try {
                        const response = await fetch(this.endpoint);
                        const data = await response.json();
                        
                        if (data.labels && data.datasets) {
                            this.chartData = data;
                            this.initChart();
                        }
                        
                        this.flashIndicator();
                    } catch (error) {
                        console.error('Failed to update chart data:', error);
                        this.showError();
                    }
                }
                
                async refreshData() {
                    if (this.endpoint) {
                        await this.updateData();
                    } else {
                        // Refresh animation
                        this.chart.update('active');
                    }
                }
                
                exportChart() {
                    const link = document.createElement('a');
                    link.download = `${this.chartId}-${new Date().getTime()}.png`;
                    link.href = this.canvas.toDataURL();
                    link.click();
                }
                
                resetZoom() {
                    if (this.chart && this.chart.resetZoom) {
                        this.chart.resetZoom();
                    }
                }
                
                async handleDrillDown(element) {
                    if (!this.drillDownEndpoint) return;
                    
                    const dataIndex = element.index;
                    const label = this.chartData.labels[dataIndex];
                    const value = this.chartData.datasets[0].data[dataIndex];
                    
                    try {
                        const response = await fetch(`${this.drillDownEndpoint}?label=${encodeURIComponent(label)}&value=${value}`);
                        const html = await response.text();
                        
                        this.showDrillDownModal(html);
                    } catch (error) {
                        console.error('Failed to load drill-down data:', error);
                    }
                }
                
                showDrillDownModal(content) {
                    const modal = this.element.querySelector('.drill-down-modal');
                    const contentContainer = modal.querySelector('.drill-down-content');
                    
                    contentContainer.innerHTML = content;
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                
                closeDrillDownModal() {
                    const modal = this.element.querySelector('.drill-down-modal');
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
                
                toggleNoDataMessage(show) {
                    const noDataMsg = this.element.querySelector('.no-data-message');
                    const chartContainer = this.element.querySelector('.chart-container');
                    
                    if (show) {
                        noDataMsg.classList.remove('hidden');
                        chartContainer.style.display = 'none';
                    } else {
                        noDataMsg.classList.add('hidden');
                        chartContainer.style.display = 'block';
                    }
                }
                
                flashIndicator() {
                    const indicator = this.element.querySelector('.chart-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-green-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-green-400');
                        }, 200);
                    }
                }
                
                showError() {
                    const indicator = this.element.querySelector('.chart-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-red-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-red-400');
                        }, 1000);
                    }
                }
                
                hexToRgba(hex, alpha) {
                    const r = parseInt(hex.slice(1, 3), 16);
                    const g = parseInt(hex.slice(3, 5), 16);
                    const b = parseInt(hex.slice(5, 7), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                }
            }
            
            // Initialize chart widgets
            document.addEventListener('DOMContentLoaded', function() {
                const chartWidgets = document.querySelectorAll('[data-chart-widget]');
                chartWidgets.forEach(widget => new ChartWidget(widget));
            });
            
            // Handle modal clicks outside content
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('drill-down-modal')) {
                    e.target.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        </script>
    @endpush
@endonce
