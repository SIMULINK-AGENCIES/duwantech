<!-- Interactive World Map Widget -->
<div x-data="worldMapWidget()" 
     x-init="init()"
     class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Geographic User Map</h3>
            <p class="text-sm text-gray-500">Real-time user locations worldwide</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- View Mode Toggle -->
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button @click="setViewMode('map')" 
                        :class="viewMode === 'map' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-globe mr-1"></i>Map
                </button>
                <button @click="setViewMode('list')" 
                        :class="viewMode === 'list' ? 'bg-white shadow-sm' : 'hover:bg-gray-200'"
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors duration-200">
                    <i class="fas fa-list mr-1"></i>List
                </button>
            </div>
            
            <!-- Layer Toggle -->
            <button @click="toggleHeatmap()" 
                    :class="showHeatmap ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'"
                    class="px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-fire mr-1"></i>
                <span x-text="showHeatmap ? 'Hide Heatmap' : 'Show Heatmap'"></span>
            </button>
            
            <!-- Refresh Button -->
            <button @click="refreshData()" 
                    :disabled="loading"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
            </button>
        </div>
    </div>

    <!-- Map View -->
    <div x-show="viewMode === 'map'" class="relative">
        <!-- Map Container -->
        <div id="world-map" class="h-96 w-full bg-gray-50 relative">
            <!-- Map will be rendered here by Leaflet -->
        </div>
        
        <!-- Map Loading State -->
        <div x-show="loading" 
             class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
            <div class="flex items-center space-x-2 text-gray-500">
                <div class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-sm">Loading map data...</span>
            </div>
        </div>
        
        <!-- Map Controls -->
        <div class="absolute top-4 right-4 z-10 space-y-2">
            <div class="bg-white rounded-lg shadow-lg p-2 text-xs">
                <div class="flex items-center space-x-2 mb-1">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span>Authenticated Users</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    <span>Anonymous Users</span>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div x-show="viewMode === 'list'" class="p-6">
        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600" x-text="summary.total_active_users"></div>
                <div class="text-xs text-blue-600">Active Users</div>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600" x-text="summary.authenticated_users"></div>
                <div class="text-xs text-green-600">Authenticated</div>
            </div>
            <div class="text-center p-3 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600" x-text="summary.unique_countries"></div>
                <div class="text-xs text-purple-600">Countries</div>
            </div>
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600" x-text="summary.unique_cities"></div>
                <div class="text-xs text-orange-600">Cities</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 mb-4">
            <button @click="activeTab = 'countries'" 
                    :class="activeTab === 'countries' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="py-2 px-4 text-sm font-medium border-b-2 hover:text-gray-700">
                Top Countries
            </button>
            <button @click="activeTab = 'cities'" 
                    :class="activeTab === 'cities' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="py-2 px-4 text-sm font-medium border-b-2 hover:text-gray-700">
                Top Cities
            </button>
            <button @click="activeTab = 'users'" 
                    :class="activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="py-2 px-4 text-sm font-medium border-b-2 hover:text-gray-700">
                Active Users
            </button>
        </div>

        <!-- Tab Content -->
        <div class="space-y-3 max-h-64 overflow-y-auto">
            <!-- Countries Tab -->
            <template x-show="activeTab === 'countries'">
                <template x-for="country in topCountries" :key="country.name">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`" 
                                 :alt="country.name + ' flag'"
                                 class="w-5 h-4 rounded">
                            <div>
                                <div class="font-medium text-gray-900" x-text="country.name"></div>
                                <div class="text-sm text-gray-500">
                                    <span x-text="country.authenticated"></span> auth, 
                                    <span x-text="country.anonymous"></span> anon
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-900" x-text="country.count"></div>
                            <div class="text-xs text-gray-500">users</div>
                        </div>
                    </div>
                </template>
            </template>

            <!-- Cities Tab -->
            <template x-show="activeTab === 'cities'">
                <template x-for="city in topCities" :key="city.name + city.country">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-900" x-text="city.name"></div>
                            <div class="text-sm text-gray-500" x-text="city.country"></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-900" x-text="city.count"></div>
                            <div class="text-xs text-gray-500">users</div>
                        </div>
                    </div>
                </template>
            </template>

            <!-- Users Tab -->
            <template x-show="activeTab === 'users'">
                <template x-for="user in recentUsers" :key="user.id">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <i :class="user.is_authenticated ? 'fas fa-user text-blue-600' : 'fas fa-user-secret text-gray-500'"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900" x-text="user.user_name"></div>
                                <div class="text-sm text-gray-500">
                                    <span x-text="user.city"></span>, <span x-text="user.country"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600" x-text="formatTime(user.last_activity)"></div>
                            <div class="text-xs" :class="user.is_authenticated ? 'text-blue-600' : 'text-gray-500'">
                                <span x-text="user.is_authenticated ? 'Authenticated' : 'Anonymous'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    <!-- Footer with Last Updated -->
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
        Last updated: <span x-text="lastUpdated"></span>
        <span class="ml-4">Auto-refresh: <span x-text="autoRefresh ? 'ON' : 'OFF'"></span></span>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Heatmap Plugin -->
<script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
function worldMapWidget() {
    return {
        // State
        viewMode: 'map',
        activeTab: 'countries',
        showHeatmap: false,
        loading: false,
        autoRefresh: true,
        lastUpdated: '',
        
        // Data
        locations: [],
        summary: {
            total_active_users: 0,
            authenticated_users: 0,
            anonymous_users: 0,
            unique_countries: 0,
            unique_cities: 0
        },
        topCountries: [],
        topCities: [],
        recentUsers: [],
        
        // Map instances
        map: null,
        markersLayer: null,
        heatmapLayer: null,
        
        // Refresh settings
        refreshInterval: 30000, // 30 seconds
        refreshTimer: null,
        
        init() {
            this.initializeMap();
            this.loadMapData();
            this.startAutoRefresh();
            
            // Listen for dashboard refresh events
            window.addEventListener('dashboard-refresh', () => {
                this.loadMapData();
            });
        },
        
        destroy() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
            if (this.map) {
                this.map.remove();
            }
        },
        
        initializeMap() {
            // Initialize Leaflet map
            this.map = L.map('world-map', {
                center: [20, 0],
                zoom: 2,
                zoomControl: true,
                attributionControl: false
            });
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
            
            // Initialize layers
            this.markersLayer = L.layerGroup().addTo(this.map);
            this.heatmapLayer = L.heatLayer([], {
                radius: 25,
                blur: 15,
                maxZoom: 18,
                max: 1.0,
                gradient: {
                    0.4: '#3B82F6',
                    0.6: '#10B981',
                    0.8: '#F59E0B',
                    1.0: '#EF4444'
                }
            });
        },
        
        async loadMapData() {
            this.loading = true;
            
            try {
                // Load active locations
                const locationsResponse = await fetch('/api/geographic/active-locations');
                const locationsResult = await locationsResponse.json();
                
                if (locationsResult.success) {
                    this.locations = locationsResult.data.locations || [];
                    this.summary = locationsResult.data.summary || this.summary;
                    this.updateMap();
                }
                
                // Load location stats
                const statsResponse = await fetch('/api/geographic/location-stats');
                const statsResult = await statsResponse.json();
                
                if (statsResult.success) {
                    this.topCountries = statsResult.data.countries.slice(0, 10) || [];
                    this.topCities = statsResult.data.cities.slice(0, 10) || [];
                }
                
                // Recent users from locations
                this.recentUsers = this.locations
                    .sort((a, b) => new Date(b.last_activity) - new Date(a.last_activity))
                    .slice(0, 20);
                
                this.lastUpdated = new Date().toLocaleTimeString();
                
            } catch (error) {
                console.error('Failed to load map data:', error);
            } finally {
                this.loading = false;
            }
        },
        
        updateMap() {
            if (!this.map) return;
            
            // Clear existing markers
            this.markersLayer.clearLayers();
            
            // Add markers for each location
            this.locations.forEach(location => {
                if (!location.latitude || !location.longitude) return;
                
                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="w-4 h-4 rounded-full border-2 border-white shadow-lg ${
                        location.is_authenticated ? 'bg-blue-500' : 'bg-gray-400'
                    }"></div>`,
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });
                
                const marker = L.marker([location.latitude, location.longitude], { icon })
                    .bindPopup(`
                        <div class="p-2">
                            <div class="font-semibold text-gray-900">${location.user_name}</div>
                            <div class="text-sm text-gray-600">${location.city}, ${location.country}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                ${location.is_authenticated ? 'Authenticated User' : 'Anonymous Visitor'}
                            </div>
                            <div class="text-xs text-gray-500">
                                Last active: ${this.formatTime(location.last_activity)}
                            </div>
                            <div class="text-xs text-gray-500">
                                Session: ${location.session_duration} min
                            </div>
                        </div>
                    `);
                
                this.markersLayer.addLayer(marker);
            });
            
            // Update heatmap
            this.updateHeatmap();
        },
        
        async updateHeatmap() {
            if (!this.showHeatmap) return;
            
            try {
                const response = await fetch('/api/geographic/heatmap-data');
                const result = await response.json();
                
                if (result.success && result.data.points) {
                    const heatPoints = result.data.points.map(point => [
                        point.lat,
                        point.lng,
                        point.intensity || 0.5
                    ]);
                    
                    this.heatmapLayer.setLatLngs(heatPoints);
                }
            } catch (error) {
                console.error('Failed to load heatmap data:', error);
            }
        },
        
        setViewMode(mode) {
            this.viewMode = mode;
            
            if (mode === 'map' && this.map) {
                // Refresh map when switching back to map view
                setTimeout(() => {
                    this.map.invalidateSize();
                }, 100);
            }
        },
        
        toggleHeatmap() {
            this.showHeatmap = !this.showHeatmap;
            
            if (this.showHeatmap) {
                this.map.addLayer(this.heatmapLayer);
                this.updateHeatmap();
            } else {
                this.map.removeLayer(this.heatmapLayer);
            }
        },
        
        startAutoRefresh() {
            if (!this.autoRefresh) return;
            
            this.refreshTimer = setInterval(() => {
                this.loadMapData();
            }, this.refreshInterval);
        },
        
        stopAutoRefresh() {
            if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
                this.refreshTimer = null;
            }
        },
        
        refreshData() {
            this.loadMapData();
        },
        
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString();
        }
    }
}
</script>

<style>
.custom-div-icon {
    background: transparent;
    border: none;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
}

.leaflet-popup-content {
    margin: 0;
}
</style>
