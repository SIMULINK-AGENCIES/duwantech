@props([
    'title' => 'Data Table',
    'data' => [],
    'columns' => [],
    'sortable' => true,
    'filterable' => true,
    'searchable' => true,
    'paginated' => true,
    'perPage' => 10,
    'perPageOptions' => [5, 10, 25, 50, 100],
    'showPagination' => true,
    'showPerPageSelector' => true,
    'showSearchBox' => true,
    'showColumnFilters' => true,
    'showBulkActions' => false,
    'bulkActions' => [],
    'rowActions' => [],
    'selectable' => false,
    'striped' => true,
    'hover' => true,
    'bordered' => true,
    'compact' => false,
    'responsive' => true,
    'loading' => false,
    'emptyMessage' => 'No data available',
    'realTimeEndpoint' => null,
    'refreshInterval' => 30000,
    'exportable' => false,
    'customClass' => '',
    'theme' => 'light',
    'id' => null
])

@php
    $componentId = $id ?? 'data-table-' . uniqid();
    $tableId = $componentId . '-table';
    
    // Process columns - ensure they have required properties
    $processedColumns = collect($columns)->map(function($column) {
        if (is_string($column)) {
            return [
                'key' => $column,
                'label' => ucfirst(str_replace('_', ' ', $column)),
                'sortable' => true,
                'filterable' => true,
                'searchable' => true,
                'width' => null,
                'align' => 'left',
                'type' => 'text'
            ];
        }
        
        return array_merge([
            'key' => '',
            'label' => '',
            'sortable' => true,
            'filterable' => true,
            'searchable' => true,
            'width' => null,
            'align' => 'left',
            'type' => 'text',
            'format' => null,
            'render' => null
        ], $column);
    })->toArray();
    
    // Convert data to collection for easier manipulation
    $dataCollection = collect($data);
    $totalRecords = $dataCollection->count();
@endphp

<div 
    id="{{ $componentId }}"
    @class([
        'data-table-widget bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden',
        'dark:bg-gray-800 dark:border-gray-700' => $theme === 'dark',
        $customClass
    ])
    @if($realTimeEndpoint)
        data-realtime-endpoint="{{ $realTimeEndpoint }}"
        data-refresh-interval="{{ $refreshInterval }}"
    @endif
    data-data-table
    data-per-page="{{ $perPage }}"
    data-sortable="{{ $sortable ? 'true' : 'false' }}"
    data-filterable="{{ $filterable ? 'true' : 'false' }}"
    data-searchable="{{ $searchable ? 'true' : 'false' }}"
>
    <!-- Header -->
    <div @class([
        'px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-4',
        'dark:border-gray-700' => $theme === 'dark'
    ])>
        <div class="flex items-center space-x-3">
            <h3 @class([
                'text-lg font-semibold text-gray-900',
                'dark:text-white' => $theme === 'dark'
            ])>{{ $title }}</h3>
            
            @if($realTimeEndpoint)
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse table-realtime-indicator"></div>
                    <span @class([
                        'text-xs text-gray-500',
                        'dark:text-gray-400' => $theme === 'dark'
                    ])>Live</span>
                </div>
            @endif
            
            <span @class([
                'text-sm text-gray-500 table-record-count',
                'dark:text-gray-400' => $theme === 'dark'
            ])>
                <span class="current-records">{{ $totalRecords }}</span> records
            </span>
        </div>
        
        <div class="flex items-center space-x-3 flex-wrap">
            @if($showSearchBox && $searchable)
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Search..." 
                        class="table-search-input w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            @endif
            
            @if($showPerPageSelector && $paginated)
                <div class="flex items-center space-x-2">
                    <span @class([
                        'text-sm text-gray-500',
                        'dark:text-gray-400' => $theme === 'dark'
                    ])>Show:</span>
                    <select class="table-per-page-selector text-sm border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" {{ $option == $perPage ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            @if($exportable)
                <button 
                    type="button"
                    class="export-table-btn px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    title="Export Data"
                >
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            @endif
            
            <button 
                type="button"
                class="refresh-table-btn p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                title="Refresh Data"
            >
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    
    <!-- Column Filters -->
    @if($showColumnFilters && $filterable)
        <div class="column-filters px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($processedColumns as $column)
                    @if($column['filterable'])
                        <div class="filter-group">
                            <label @class([
                                'block text-xs font-medium text-gray-700 mb-1',
                                'dark:text-gray-300' => $theme === 'dark'
                            ])>{{ $column['label'] }}</label>
                            
                            @if($column['type'] === 'select')
                                <select 
                                    class="column-filter w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    data-column="{{ $column['key'] }}"
                                >
                                    <option value="">All</option>
                                    @if(isset($column['options']))
                                        @foreach($column['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif($column['type'] === 'date')
                                <input 
                                    type="date" 
                                    class="column-filter w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    data-column="{{ $column['key'] }}"
                                >
                            @elseif($column['type'] === 'number')
                                <input 
                                    type="number" 
                                    placeholder="Filter..." 
                                    class="column-filter w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    data-column="{{ $column['key'] }}"
                                >
                            @else
                                <input 
                                    type="text" 
                                    placeholder="Filter..." 
                                    class="column-filter w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    data-column="{{ $column['key'] }}"
                                >
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-3 flex items-center space-x-2">
                <button type="button" class="clear-filters-btn text-xs text-blue-600 hover:text-blue-800">Clear Filters</button>
                <button type="button" class="toggle-filters-btn text-xs text-gray-500 hover:text-gray-700">Hide Filters</button>
            </div>
        </div>
    @endif
    
    <!-- Bulk Actions -->
    @if($showBulkActions && !empty($bulkActions))
        <div class="bulk-actions px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900 hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-yellow-800 dark:text-yellow-200">
                        <span class="selected-count">0</span> items selected
                    </span>
                    <div class="flex items-center space-x-2">
                        @foreach($bulkActions as $action)
                            <button 
                                type="button" 
                                class="bulk-action-btn px-3 py-1 text-xs rounded {{ $action['class'] ?? 'bg-gray-600 text-white hover:bg-gray-700' }}"
                                data-action="{{ $action['key'] }}"
                            >
                                @if(isset($action['icon']))
                                    <i class="fas fa-{{ $action['icon'] }} mr-1"></i>
                                @endif
                                {{ $action['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="clear-selection-btn text-xs text-yellow-700 hover:text-yellow-900 dark:text-yellow-300">
                    Clear Selection
                </button>
            </div>
        </div>
    @endif
    
    <!-- Table Container -->
    <div @class([
        'table-container overflow-x-auto',
        'max-h-96 overflow-y-auto' => $compact
    ])>
        @if($loading)
            <div class="loading-overlay absolute inset-0 bg-white bg-opacity-50 flex items-center justify-center z-10">
                <div class="flex flex-col items-center space-y-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-sm text-gray-500">Loading data...</span>
                </div>
            </div>
        @endif
        
        <table @class([
            'table w-full table-auto',
            'table-striped' => $striped,
            'table-hover' => $hover,
            'border-collapse' => $bordered,
            'text-sm' => $compact
        ])>
            <thead @class([
                'bg-gray-50 sticky top-0 z-10',
                'dark:bg-gray-700' => $theme === 'dark'
            ])>
                <tr>
                    @if($selectable)
                        <th class="px-4 py-3 text-left">
                            <input 
                                type="checkbox" 
                                class="select-all-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                        </th>
                    @endif
                    
                    @foreach($processedColumns as $column)
                        <th 
                            @class([
                                'px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none',
                                'dark:text-gray-300' => $theme === 'dark',
                                'sortable-header' => $column['sortable'] && $sortable,
                                'text-center' => $column['align'] === 'center',
                                'text-right' => $column['align'] === 'right'
                            ])
                            @if($column['sortable'] && $sortable)
                                data-column="{{ $column['key'] }}"
                                data-sort="none"
                            @endif
                            @if($column['width'])
                                style="width: {{ $column['width'] }}"
                            @endif
                        >
                            <div class="flex items-center space-x-1">
                                <span>{{ $column['label'] }}</span>
                                @if($column['sortable'] && $sortable)
                                    <span class="sort-icon">
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </span>
                                @endif
                                @if($column['filterable'] && $filterable && $showColumnFilters)
                                    <button 
                                        type="button" 
                                        class="toggle-column-filter text-gray-400 hover:text-gray-600"
                                        title="Filter this column"
                                    >
                                        <i class="fas fa-filter text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    
                    @if(!empty($rowActions))
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 table-body">
                @forelse($dataCollection->take($perPage) as $index => $row)
                    <tr @class([
                        'table-row hover:bg-gray-50 dark:hover:bg-gray-700',
                        'bg-gray-50 dark:bg-gray-750' => $striped && $index % 2 === 1
                    ])>
                        @if($selectable)
                            <td class="px-4 py-3">
                                <input 
                                    type="checkbox" 
                                    class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    value="{{ $row['id'] ?? $index }}"
                                >
                            </td>
                        @endif
                        
                        @foreach($processedColumns as $column)
                            <td @class([
                                'px-4 py-3 text-sm text-gray-900 dark:text-gray-100',
                                'text-center' => $column['align'] === 'center',
                                'text-right' => $column['align'] === 'right'
                            ])>
                                @if(isset($column['render']) && is_callable($column['render']))
                                    {!! $column['render']($row, $column['key']) !!}
                                @else
                                    @php
                                        $value = data_get($row, $column['key'], '');
                                        
                                        // Format value based on column type
                                        if ($column['type'] === 'currency' && is_numeric($value)) {
                                            $value = '$' . number_format($value, 2);
                                        } elseif ($column['type'] === 'percentage' && is_numeric($value)) {
                                            $value = number_format($value, 1) . '%';
                                        } elseif ($column['type'] === 'date' && $value) {
                                            $value = date('M d, Y', strtotime($value));
                                        } elseif ($column['type'] === 'datetime' && $value) {
                                            $value = date('M d, Y H:i', strtotime($value));
                                        } elseif ($column['type'] === 'number' && is_numeric($value)) {
                                            $value = number_format($value);
                                        } elseif ($column['type'] === 'badge' && $value) {
                                            $badgeClass = $column['badgeClass'][$value] ?? 'bg-gray-100 text-gray-800';
                                            $value = "<span class='px-2 py-1 text-xs rounded-full {$badgeClass}'>{$value}</span>";
                                        }
                                        
                                        // Apply custom format if provided
                                        if (isset($column['format']) && is_callable($column['format'])) {
                                            $value = $column['format']($value, $row);
                                        }
                                    @endphp
                                    
                                    {!! $value !!}
                                @endif
                            </td>
                        @endforeach
                        
                        @if(!empty($rowActions))
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @foreach($rowActions as $action)
                                        @if(!isset($action['condition']) || $action['condition']($row))
                                            <button 
                                                type="button"
                                                class="row-action-btn p-1 rounded {{ $action['class'] ?? 'text-gray-500 hover:text-gray-700' }}"
                                                data-action="{{ $action['key'] }}"
                                                data-id="{{ $row['id'] ?? $index }}"
                                                title="{{ $action['title'] ?? $action['label'] }}"
                                            >
                                                @if(isset($action['icon']))
                                                    <i class="fas fa-{{ $action['icon'] }}"></i>
                                                @else
                                                    {{ $action['label'] }}
                                                @endif
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($processedColumns) + ($selectable ? 1 : 0) + (!empty($rowActions) ? 1 : 0) }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center space-y-2">
                                <i class="fas fa-table text-4xl text-gray-300"></i>
                                <p>{{ $emptyMessage }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($showPagination && $paginated && $totalRecords > $perPage)
        <div class="pagination-container px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span @class([
                    'text-sm text-gray-500',
                    'dark:text-gray-400' => $theme === 'dark'
                ])>
                    Showing <span class="font-medium current-page-start">1</span> to 
                    <span class="font-medium current-page-end">{{ min($perPage, $totalRecords) }}</span> of 
                    <span class="font-medium total-records">{{ $totalRecords }}</span> results
                </span>
            </div>
            
            <nav class="pagination flex items-center space-x-1">
                <button 
                    type="button" 
                    class="pagination-btn prev-page px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="pagination-numbers flex items-center space-x-1">
                    <!-- Page numbers will be generated by JavaScript -->
                </div>
                
                <button 
                    type="button" 
                    class="pagination-btn next-page px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
            </nav>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            .data-table-widget {
                transition: all 0.3s ease;
            }
            
            .data-table-widget:hover {
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .sortable-header:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }
            
            .sort-icon {
                transition: transform 0.2s ease;
            }
            
            .sortable-header[data-sort="asc"] .sort-icon i {
                transform: rotate(180deg);
            }
            
            .sortable-header[data-sort="asc"] .sort-icon i::before {
                content: "\f0de";
            }
            
            .sortable-header[data-sort="desc"] .sort-icon i::before {
                content: "\f0dd";
            }
            
            .table-striped tbody tr:nth-child(even) {
                background-color: rgba(0, 0, 0, 0.02);
            }
            
            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.05);
                transform: translateY(-1px);
                transition: all 0.2s ease;
            }
            
            .loading-overlay {
                backdrop-filter: blur(2px);
            }
            
            .pagination-btn:hover:not(:disabled) {
                background-color: rgba(0, 0, 0, 0.05);
                border-radius: 0.375rem;
            }
            
            .pagination-btn.active {
                background-color: #3B82F6;
                color: white;
                border-radius: 0.375rem;
            }
            
            .column-filters {
                transition: all 0.3s ease;
            }
            
            .bulk-actions {
                transition: all 0.3s ease;
            }
            
            .row-action-btn:hover {
                transform: scale(1.1);
                transition: transform 0.2s ease;
            }
            
            .table-search-input:focus {
                transform: scale(1.02);
                transition: transform 0.2s ease;
            }
        </style>
    @endpush
    
    @push('scripts')
        <script>
            class DataTable {
                constructor(element) {
                    this.element = element;
                    this.tableId = element.id;
                    this.data = @json($dataCollection->toArray());
                    this.originalData = [...this.data];
                    this.filteredData = [...this.data];
                    this.columns = @json($processedColumns);
                    
                    this.currentPage = 1;
                    this.perPage = parseInt(element.dataset.perPage) || 10;
                    this.sortColumn = null;
                    this.sortDirection = 'asc';
                    this.searchTerm = '';
                    this.columnFilters = {};
                    this.selectedRows = new Set();
                    
                    this.endpoint = element.dataset.realtimeEndpoint;
                    this.interval = parseInt(element.dataset.refreshInterval) || 30000;
                    
                    this.init();
                }
                
                init() {
                    this.bindEvents();
                    this.updateDisplay();
                    this.updatePagination();
                    
                    if (this.endpoint) {
                        this.startRealTimeUpdates();
                    }
                }
                
                bindEvents() {
                    // Search input
                    const searchInput = this.element.querySelector('.table-search-input');
                    if (searchInput) {
                        searchInput.addEventListener('input', (e) => {
                            this.searchTerm = e.target.value;
                            this.applyFilters();
                        });
                    }
                    
                    // Per page selector
                    const perPageSelector = this.element.querySelector('.table-per-page-selector');
                    if (perPageSelector) {
                        perPageSelector.addEventListener('change', (e) => {
                            this.perPage = parseInt(e.target.value);
                            this.currentPage = 1;
                            this.updateDisplay();
                            this.updatePagination();
                        });
                    }
                    
                    // Sortable headers
                    const sortableHeaders = this.element.querySelectorAll('.sortable-header');
                    sortableHeaders.forEach(header => {
                        header.addEventListener('click', () => {
                            const column = header.dataset.column;
                            this.sort(column);
                        });
                    });
                    
                    // Column filters
                    const columnFilters = this.element.querySelectorAll('.column-filter');
                    columnFilters.forEach(filter => {
                        filter.addEventListener('input', (e) => {
                            const column = e.target.dataset.column;
                            this.columnFilters[column] = e.target.value;
                            this.applyFilters();
                        });
                    });
                    
                    // Clear filters
                    const clearFiltersBtn = this.element.querySelector('.clear-filters-btn');
                    if (clearFiltersBtn) {
                        clearFiltersBtn.addEventListener('click', () => this.clearFilters());
                    }
                    
                    // Toggle filters
                    const toggleFiltersBtn = this.element.querySelector('.toggle-filters-btn');
                    if (toggleFiltersBtn) {
                        toggleFiltersBtn.addEventListener('click', () => this.toggleFilters());
                    }
                    
                    // Select all checkbox
                    const selectAllCheckbox = this.element.querySelector('.select-all-checkbox');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', (e) => {
                            this.toggleSelectAll(e.target.checked);
                        });
                    }
                    
                    // Row checkboxes
                    this.element.addEventListener('change', (e) => {
                        if (e.target.classList.contains('row-checkbox')) {
                            this.handleRowSelection(e.target);
                        }
                    });
                    
                    // Row actions
                    this.element.addEventListener('click', (e) => {
                        if (e.target.closest('.row-action-btn')) {
                            const btn = e.target.closest('.row-action-btn');
                            const action = btn.dataset.action;
                            const id = btn.dataset.id;
                            this.handleRowAction(action, id);
                        }
                    });
                    
                    // Bulk actions
                    this.element.addEventListener('click', (e) => {
                        if (e.target.closest('.bulk-action-btn')) {
                            const btn = e.target.closest('.bulk-action-btn');
                            const action = btn.dataset.action;
                            this.handleBulkAction(action);
                        }
                    });
                    
                    // Pagination
                    this.element.addEventListener('click', (e) => {
                        if (e.target.closest('.pagination-btn')) {
                            const btn = e.target.closest('.pagination-btn');
                            if (btn.classList.contains('prev-page')) {
                                this.prevPage();
                            } else if (btn.classList.contains('next-page')) {
                                this.nextPage();
                            } else if (btn.dataset.page) {
                                this.goToPage(parseInt(btn.dataset.page));
                            }
                        }
                    });
                    
                    // Export button
                    const exportBtn = this.element.querySelector('.export-table-btn');
                    if (exportBtn) {
                        exportBtn.addEventListener('click', () => this.exportData());
                    }
                    
                    // Refresh button
                    const refreshBtn = this.element.querySelector('.refresh-table-btn');
                    if (refreshBtn) {
                        refreshBtn.addEventListener('click', () => this.refreshData());
                    }
                }
                
                sort(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                    
                    this.applySort();
                    this.updateSortIcons();
                }
                
                applySort() {
                    if (!this.sortColumn) return;
                    
                    this.filteredData.sort((a, b) => {
                        const aVal = this.getNestedValue(a, this.sortColumn);
                        const bVal = this.getNestedValue(b, this.sortColumn);
                        
                        let comparison = 0;
                        if (aVal < bVal) comparison = -1;
                        if (aVal > bVal) comparison = 1;
                        
                        return this.sortDirection === 'asc' ? comparison : -comparison;
                    });
                    
                    this.currentPage = 1;
                    this.updateDisplay();
                    this.updatePagination();
                }
                
                applyFilters() {
                    this.filteredData = this.originalData.filter(row => {
                        // Search filter
                        if (this.searchTerm) {
                            const searchMatch = this.columns.some(column => {
                                if (!column.searchable) return false;
                                const value = this.getNestedValue(row, column.key);
                                return String(value).toLowerCase().includes(this.searchTerm.toLowerCase());
                            });
                            if (!searchMatch) return false;
                        }
                        
                        // Column filters
                        for (const [column, filterValue] of Object.entries(this.columnFilters)) {
                            if (!filterValue) continue;
                            const rowValue = this.getNestedValue(row, column);
                            if (!String(rowValue).toLowerCase().includes(String(filterValue).toLowerCase())) {
                                return false;
                            }
                        }
                        
                        return true;
                    });
                    
                    if (this.sortColumn) {
                        this.applySort();
                    } else {
                        this.currentPage = 1;
                        this.updateDisplay();
                        this.updatePagination();
                    }
                    
                    this.updateRecordCount();
                }
                
                updateDisplay() {
                    const tbody = this.element.querySelector('.table-body');
                    const start = (this.currentPage - 1) * this.perPage;
                    const end = start + this.perPage;
                    const pageData = this.filteredData.slice(start, end);
                    
                    tbody.innerHTML = '';
                    
                    if (pageData.length === 0) {
                        const emptyMessage = '{{ $emptyMessage }}';
                        const colspan = this.columns.length + ({{ $selectable ? 'true' : 'false' }} ? 1 : 0) + ({{ !empty($rowActions) ? 'true' : 'false' }} ? 1 : 0);
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="${colspan}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center space-y-2">
                                        <i class="fas fa-table text-4xl text-gray-300"></i>
                                        <p>${emptyMessage}</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    pageData.forEach((row, index) => {
                        const tr = document.createElement('tr');
                        tr.className = 'table-row hover:bg-gray-50 dark:hover:bg-gray-700';
                        if ({{ $striped ? 'true' : 'false' }} && index % 2 === 1) {
                            tr.className += ' bg-gray-50 dark:bg-gray-750';
                        }
                        
                        let html = '';
                        
                        // Selection checkbox
                        if ({{ $selectable ? 'true' : 'false' }}) {
                            const rowId = row.id || (start + index);
                            const checked = this.selectedRows.has(rowId) ? 'checked' : '';
                            html += `
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${rowId}" ${checked}>
                                </td>
                            `;
                        }
                        
                        // Data columns
                        this.columns.forEach(column => {
                            const value = this.formatValue(this.getNestedValue(row, column.key), column, row);
                            const alignClass = column.align === 'center' ? 'text-center' : column.align === 'right' ? 'text-right' : '';
                            html += `<td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 ${alignClass}">${value}</td>`;
                        });
                        
                        // Row actions
                        if ({{ !empty($rowActions) ? 'true' : 'false' }}) {
                            const rowActions = @json($rowActions);
                            let actionsHtml = '<div class="flex items-center justify-center space-x-2">';
                            
                            rowActions.forEach(action => {
                                const actionClass = action.class || 'text-gray-500 hover:text-gray-700';
                                const icon = action.icon ? `<i class="fas fa-${action.icon}"></i>` : action.label;
                                const title = action.title || action.label;
                                const rowId = row.id || (start + index);
                                
                                actionsHtml += `
                                    <button type="button" class="row-action-btn p-1 rounded ${actionClass}" 
                                            data-action="${action.key}" data-id="${rowId}" title="${title}">
                                        ${icon}
                                    </button>
                                `;
                            });
                            
                            actionsHtml += '</div>';
                            html += `<td class="px-4 py-3 text-center">${actionsHtml}</td>`;
                        }
                        
                        tr.innerHTML = html;
                        tbody.appendChild(tr);
                    });
                }
                
                formatValue(value, column, row) {
                    if (value === null || value === undefined) return '';
                    
                    switch (column.type) {
                        case 'currency':
                            return isNaN(value) ? value : '$' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        case 'percentage':
                            return isNaN(value) ? value : Number(value).toFixed(1) + '%';
                        case 'number':
                            return isNaN(value) ? value : Number(value).toLocaleString();
                        case 'date':
                            return value ? new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '';
                        case 'datetime':
                            return value ? new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
                        case 'badge':
                            if (column.badgeClass && column.badgeClass[value]) {
                                return `<span class="px-2 py-1 text-xs rounded-full ${column.badgeClass[value]}">${value}</span>`;
                            }
                            return `<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">${value}</span>`;
                        default:
                            return String(value);
                    }
                }
                
                updatePagination() {
                    const totalPages = Math.ceil(this.filteredData.length / this.perPage);
                    const paginationContainer = this.element.querySelector('.pagination-container');
                    
                    if (!paginationContainer || totalPages <= 1) {
                        if (paginationContainer) {
                            paginationContainer.style.display = 'none';
                        }
                        return;
                    }
                    
                    paginationContainer.style.display = 'flex';
                    
                    // Update pagination info
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(this.currentPage * this.perPage, this.filteredData.length);
                    
                    this.element.querySelector('.current-page-start').textContent = start;
                    this.element.querySelector('.current-page-end').textContent = end;
                    this.element.querySelector('.total-records').textContent = this.filteredData.length;
                    
                    // Update pagination buttons
                    const prevBtn = this.element.querySelector('.prev-page');
                    const nextBtn = this.element.querySelector('.next-page');
                    
                    prevBtn.disabled = this.currentPage === 1;
                    nextBtn.disabled = this.currentPage === totalPages;
                    
                    // Generate page numbers
                    const numbersContainer = this.element.querySelector('.pagination-numbers');
                    numbersContainer.innerHTML = '';
                    
                    const maxVisiblePages = 5;
                    let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
                    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
                    
                    if (endPage - startPage < maxVisiblePages - 1) {
                        startPage = Math.max(1, endPage - maxVisiblePages + 1);
                    }
                    
                    for (let i = startPage; i <= endPage; i++) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = `pagination-btn px-3 py-2 text-sm ${i === this.currentPage ? 'active bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-700'}`;
                        btn.dataset.page = i;
                        btn.textContent = i;
                        numbersContainer.appendChild(btn);
                    }
                }
                
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.updateDisplay();
                        this.updatePagination();
                    }
                }
                
                nextPage() {
                    const totalPages = Math.ceil(this.filteredData.length / this.perPage);
                    if (this.currentPage < totalPages) {
                        this.currentPage++;
                        this.updateDisplay();
                        this.updatePagination();
                    }
                }
                
                goToPage(page) {
                    const totalPages = Math.ceil(this.filteredData.length / this.perPage);
                    if (page >= 1 && page <= totalPages) {
                        this.currentPage = page;
                        this.updateDisplay();
                        this.updatePagination();
                    }
                }
                
                updateSortIcons() {
                    const headers = this.element.querySelectorAll('.sortable-header');
                    headers.forEach(header => {
                        const icon = header.querySelector('.sort-icon i');
                        const column = header.dataset.column;
                        
                        if (column === this.sortColumn) {
                            header.dataset.sort = this.sortDirection;
                            icon.className = `fas fa-sort-${this.sortDirection === 'asc' ? 'up' : 'down'}`;
                        } else {
                            header.dataset.sort = 'none';
                            icon.className = 'fas fa-sort text-gray-400';
                        }
                    });
                }
                
                clearFilters() {
                    // Clear search
                    const searchInput = this.element.querySelector('.table-search-input');
                    if (searchInput) {
                        searchInput.value = '';
                        this.searchTerm = '';
                    }
                    
                    // Clear column filters
                    const columnFilters = this.element.querySelectorAll('.column-filter');
                    columnFilters.forEach(filter => {
                        filter.value = '';
                    });
                    this.columnFilters = {};
                    
                    this.applyFilters();
                }
                
                toggleFilters() {
                    const filtersContainer = this.element.querySelector('.column-filters');
                    const toggleBtn = this.element.querySelector('.toggle-filters-btn');
                    
                    if (filtersContainer.classList.contains('hidden')) {
                        filtersContainer.classList.remove('hidden');
                        toggleBtn.textContent = 'Hide Filters';
                    } else {
                        filtersContainer.classList.add('hidden');
                        toggleBtn.textContent = 'Show Filters';
                    }
                }
                
                toggleSelectAll(checked) {
                    const checkboxes = this.element.querySelectorAll('.row-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                        const rowId = checkbox.value;
                        if (checked) {
                            this.selectedRows.add(rowId);
                        } else {
                            this.selectedRows.delete(rowId);
                        }
                    });
                    
                    this.updateBulkActions();
                }
                
                handleRowSelection(checkbox) {
                    const rowId = checkbox.value;
                    if (checkbox.checked) {
                        this.selectedRows.add(rowId);
                    } else {
                        this.selectedRows.delete(rowId);
                    }
                    
                    this.updateSelectAllCheckbox();
                    this.updateBulkActions();
                }
                
                updateSelectAllCheckbox() {
                    const selectAllCheckbox = this.element.querySelector('.select-all-checkbox');
                    const checkboxes = this.element.querySelectorAll('.row-checkbox');
                    
                    if (selectAllCheckbox && checkboxes.length > 0) {
                        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                        selectAllCheckbox.checked = checkedCount === checkboxes.length;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                    }
                }
                
                updateBulkActions() {
                    const bulkActionsContainer = this.element.querySelector('.bulk-actions');
                    const selectedCountElement = this.element.querySelector('.selected-count');
                    
                    if (bulkActionsContainer) {
                        if (this.selectedRows.size > 0) {
                            bulkActionsContainer.classList.remove('hidden');
                            if (selectedCountElement) {
                                selectedCountElement.textContent = this.selectedRows.size;
                            }
                        } else {
                            bulkActionsContainer.classList.add('hidden');
                        }
                    }
                }
                
                handleRowAction(action, id) {
                    // Emit custom event for row action
                    this.element.dispatchEvent(new CustomEvent('rowAction', {
                        detail: { action, id, table: this }
                    }));
                }
                
                handleBulkAction(action) {
                    const selectedIds = Array.from(this.selectedRows);
                    
                    // Emit custom event for bulk action
                    this.element.dispatchEvent(new CustomEvent('bulkAction', {
                        detail: { action, ids: selectedIds, table: this }
                    }));
                }
                
                updateRecordCount() {
                    const recordCountElement = this.element.querySelector('.current-records');
                    if (recordCountElement) {
                        recordCountElement.textContent = this.filteredData.length;
                    }
                }
                
                exportData() {
                    const csvContent = this.generateCSV();
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', `${this.tableId}-${new Date().getTime()}.csv`);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
                
                generateCSV() {
                    const headers = this.columns.map(col => col.label);
                    const rows = this.filteredData.map(row => {
                        return this.columns.map(col => {
                            const value = this.getNestedValue(row, col.key);
                            return `"${String(value).replace(/"/g, '""')}"`;
                        });
                    });
                    
                    return [headers.join(','), ...rows.map(row => row.join(','))].join('\n');
                }
                
                async refreshData() {
                    if (this.endpoint) {
                        await this.updateData();
                    } else {
                        // Just refresh the display
                        this.updateDisplay();
                        this.updatePagination();
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
                        
                        if (data.data) {
                            this.data = data.data;
                            this.originalData = [...this.data];
                            this.applyFilters();
                        }
                        
                        this.flashIndicator();
                    } catch (error) {
                        console.error('Failed to update table data:', error);
                        this.showError();
                    }
                }
                
                flashIndicator() {
                    const indicator = this.element.querySelector('.table-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-green-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-green-400');
                        }, 200);
                    }
                }
                
                showError() {
                    const indicator = this.element.querySelector('.table-realtime-indicator');
                    if (indicator) {
                        indicator.classList.add('bg-red-400');
                        setTimeout(() => {
                            indicator.classList.remove('bg-red-400');
                        }, 1000);
                    }
                }
                
                getNestedValue(obj, path) {
                    return path.split('.').reduce((current, key) => current && current[key], obj);
                }
            }
            
            // Initialize data tables
            document.addEventListener('DOMContentLoaded', function() {
                const dataTables = document.querySelectorAll('[data-data-table]');
                dataTables.forEach(table => new DataTable(table));
            });
        </script>
    @endpush
@endonce
