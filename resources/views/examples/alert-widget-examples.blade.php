<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Widget Examples</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .example-section {
            margin-bottom: 3rem;
            padding: 2rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: #f9fafb;
        }
        
        .example-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #374151;
        }
        
        .example-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .code-block {
            background-color: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-top: 1rem;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-full">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Alert Widget Examples</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Comprehensive examples of the alert widget component featuring system alerts, status indicators, and action buttons with various configurations and real-time capabilities.
            </p>
        </div>

        <!-- Basic Alert Types -->
        <div class="example-section">
            <h2 class="example-title">1. Basic Alert Types</h2>
            <p class="example-description">
                Standard alert types with different severity levels and default styling.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="success"
                    title="Success Alert"
                    message="Your operation completed successfully!"
                    :actions="[
                        ['label' => 'View Details', 'style' => 'primary', 'icon' => 'eye'],
                        ['label' => 'Dismiss', 'style' => 'ghost', 'action' => 'this.closest(\'.alert-widget\').remove()']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Information Alert"
                    message="System maintenance scheduled for tonight at 2:00 AM."
                    description="The maintenance window is expected to last 2 hours. All services will be temporarily unavailable."
                    category="System"
                    :actions="[
                        ['label' => 'Learn More', 'style' => 'primary', 'icon' => 'info-circle'],
                        ['label' => 'Set Reminder', 'style' => 'secondary', 'icon' => 'bell']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="Warning Alert"
                    message="High CPU usage detected on server cluster."
                    priority="high"
                    source="Monitoring System"
                    :timestamp="now()->format('Y-m-d H:i:s')"
                    :data="[
                        'CPU Usage' => '87%',
                        'Memory Usage' => '65%',
                        'Affected Servers' => '3'
                    ]"
                    :actions="[
                        ['label' => 'Scale Up', 'style' => 'primary', 'icon' => 'arrow-up'],
                        ['label' => 'View Metrics', 'style' => 'secondary', 'icon' => 'chart-line'],
                        ['label' => 'Acknowledge', 'style' => 'ghost', 'icon' => 'check']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    title="Critical Error"
                    message="Database connection failed!"
                    description="Unable to connect to the primary database. Failover to backup database initiated."
                    priority="critical"
                    status="pending"
                    category="Database"
                    pulse="true"
                    :actions="[
                        ['label' => 'Investigate', 'style' => 'danger', 'icon' => 'search'],
                        ['label' => 'Check Logs', 'style' => 'secondary', 'icon' => 'file-alt'],
                        ['label' => 'Contact Support', 'style' => 'outline', 'icon' => 'phone']
                    ]"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="success"
    title="Success Alert"
    message="Your operation completed successfully!"
    :actions="[
        ['label' => 'View Details', 'style' => 'primary', 'icon' => 'eye'],
        ['label' => 'Dismiss', 'style' => 'ghost']
    ]"
/&gt;
            </div>
        </div>

        <!-- Alert Variants -->
        <div class="example-section">
            <h2 class="example-title">2. Alert Variants</h2>
            <p class="example-description">
                Different visual styles and variants including filled, outlined, ghost, and gradient styles.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    variant="filled"
                    title="Filled Variant"
                    message="This is a filled alert with solid background color."
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    variant="outlined"
                    title="Outlined Variant"
                    message="This is an outlined alert with border and transparent background."
                />
                
                <x-admin.widgets.alert-widget
                    type="success"
                    variant="ghost"
                    title="Ghost Variant"
                    message="This is a ghost alert with subtle background color."
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    variant="gradient"
                    title="Gradient Variant"
                    message="This is a gradient alert with beautiful color transitions."
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="info"
    variant="outlined"
    title="Outlined Alert"
    message="Alert with border styling."
/&gt;
            </div>
        </div>

        <!-- Status Indicators and Priority -->
        <div class="example-section">
            <h2 class="example-title">3. Status Indicators and Priority</h2>
            <p class="example-description">
                Alerts with different status indicators and priority levels for better visual hierarchy.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Low Priority Alert"
                    message="Routine system update available."
                    priority="low"
                    status="active"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="High Priority Alert"
                    message="Disk space running low on primary server."
                    priority="high"
                    status="pending"
                    pulse="true"
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    title="Critical Priority Alert"
                    message="Security breach detected!"
                    priority="critical"
                    status="active"
                    pulse="true"
                    :actions="[
                        ['label' => 'Lock Account', 'style' => 'danger', 'icon' => 'lock'],
                        ['label' => 'Investigate', 'style' => 'primary', 'icon' => 'search']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="success"
                    title="Resolved Alert"
                    message="Server performance issue has been resolved."
                    priority="normal"
                    status="resolved"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="error"
    title="Critical Alert"
    priority="critical"
    status="active"
    pulse="true"
    message="Security breach detected!"
/&gt;
            </div>
        </div>

        <!-- Interactive Features -->
        <div class="example-section">
            <h2 class="example-title">4. Interactive Features</h2>
            <p class="example-description">
                Alerts with interactive elements including collapsible content, auto-close, and action buttons.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Auto-Close Alert"
                    message="This alert will automatically close in 10 seconds."
                    :auto-close="true"
                    :auto-close-delay="10000"
                    :progress="true"
                    :progress-duration="10000"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="Collapsible Alert"
                    message="Click 'Show Details' to view additional information."
                    :collapsible="true"
                    :collapsed="true"
                    :data="[
                        'Error Code' => 'ERR_001',
                        'Timestamp' => '2025-07-28 10:30:45',
                        'User Agent' => 'Mozilla/5.0...',
                        'IP Address' => '192.168.1.100',
                        'Session ID' => 'sess_abc123'
                    ]"
                >
                    <div class="mt-3 p-3 bg-black bg-opacity-10 rounded">
                        <h4 class="font-semibold mb-2">Stack Trace:</h4>
                        <pre class="text-sm opacity-75">
at Object.handleError (main.js:45:12)
at processRequest (router.js:78:9)
at IncomingMessage.&lt;anonymous&gt; (server.js:123:5)
                        </pre>
                    </div>
                </x-admin.widgets.alert-widget>
                
                <x-admin.widgets.alert-widget
                    type="success"
                    title="Stackable Alert"
                    message="This alert has a stacked appearance effect."
                    :stackable="true"
                    :actions="[
                        ['label' => 'Primary Action', 'style' => 'primary'],
                        ['label' => 'Secondary', 'style' => 'secondary'],
                        ['label' => 'Ghost', 'style' => 'ghost']
                    ]"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="info"
    title="Auto-Close Alert"
    :auto-close="true"
    :auto-close-delay="5000"
    :progress="true"
    message="This will close automatically."
/&gt;
            </div>
        </div>

        <!-- Size Variations -->
        <div class="example-section">
            <h2 class="example-title">5. Size Variations</h2>
            <p class="example-description">
                Different alert sizes to fit various layout requirements.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    size="sm"
                    title="Small Alert"
                    message="Compact alert for limited spaces."
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    size="md"
                    title="Medium Alert"
                    message="Standard size alert for most use cases."
                    description="This is the default size with balanced proportions."
                />
                
                <x-admin.widgets.alert-widget
                    type="success"
                    size="lg"
                    title="Large Alert"
                    message="Prominent alert for important messages."
                    description="Larger size with more spacing and emphasis."
                    :actions="[
                        ['label' => 'Primary', 'style' => 'primary'],
                        ['label' => 'Secondary', 'style' => 'secondary']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    size="xl"
                    title="Extra Large Alert"
                    message="Maximum impact alert for critical situations."
                    description="Largest size for maximum attention and readability."
                    :actions="[
                        ['label' => 'Take Action', 'style' => 'danger', 'icon' => 'exclamation-triangle'],
                        ['label' => 'Learn More', 'style' => 'outline', 'icon' => 'info-circle']
                    ]"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="info"
    size="lg"
    title="Large Alert"
    message="Prominent alert message."
/&gt;
            </div>
        </div>

        <!-- Icon Positions -->
        <div class="example-section">
            <h2 class="example-title">6. Icon Positions</h2>
            <p class="example-description">
                Alerts with icons positioned at different locations for varied layouts.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Left Icon"
                    message="Icon positioned on the left side (default)."
                    icon-position="left"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="Right Icon"
                    message="Icon positioned on the right side."
                    icon-position="right"
                />
                
                <x-admin.widgets.alert-widget
                    type="success"
                    title="Top Icon"
                    message="Icon positioned at the top center."
                    description="This layout works well for centered alert designs."
                    icon-position="top"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="success"
    title="Top Icon Alert"
    icon-position="top"
    message="Centered layout with top icon."
/&gt;
            </div>
        </div>

        <!-- Real-time Features -->
        <div class="example-section">
            <h2 class="example-title">7. Real-time and Advanced Features</h2>
            <p class="example-description">
                Alerts with real-time updates, sound alerts, and advanced configurations.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="Real-time Alert"
                    message="This alert updates in real-time from server data."
                    description="Live status monitoring with automatic updates."
                    real-time-endpoint="/api/alerts/status"
                    :refresh-interval="5000"
                    category="Monitoring"
                    :data="[
                        'CPU Usage' => '45%',
                        'Memory' => '32%',
                        'Disk I/O' => '12%'
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    title="Sound Alert"
                    message="Critical system failure detected!"
                    description="This alert will play a sound notification."
                    :sound-alert="true"
                    sound-file="/sounds/alert-critical.mp3"
                    priority="urgent"
                    :persistent="true"
                />
                
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Custom Styled Alert"
                    message="Alert with custom CSS classes and styling."
                    custom-class="custom-gradient-border"
                    :max-height="'200px'"
                    overflow="auto"
                    :data="[
                        'Request ID' => 'REQ-789456',
                        'User' => 'john.doe@example.com',
                        'Action' => 'File Upload',
                        'Size' => '2.4 MB',
                        'Status' => 'Processing'
                    ]"
                >
                    <div class="mt-3 p-3 bg-blue-50 rounded">
                        <h4 class="font-semibold text-blue-800 mb-2">Processing Details:</h4>
                        <div class="text-sm text-blue-700">
                            <p>• File validation: Complete</p>
                            <p>• Virus scan: In progress...</p>
                            <p>• Upload progress: 75%</p>
                        </div>
                    </div>
                </x-admin.widgets.alert-widget>
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="warning"
    title="Real-time Alert"
    real-time-endpoint="/api/alerts/status"
    :refresh-interval="5000"
    message="Live updating alert."
/&gt;
            </div>
        </div>

        <!-- System Status Dashboard -->
        <div class="example-section">
            <h2 class="example-title">8. System Status Dashboard Example</h2>
            <p class="example-description">
                A complete dashboard example showing how alerts can be used for system monitoring.
            </p>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-admin.widgets.alert-widget
                    type="success"
                    variant="ghost"
                    title="API Services"
                    message="All services operational"
                    status="active"
                    category="Services"
                    :show-close="false"
                    :data="[
                        'Uptime' => '99.9%',
                        'Response Time' => '125ms',
                        'Active Connections' => '1,247'
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    variant="ghost"
                    title="Database Performance"
                    message="Elevated query response time"
                    status="pending"
                    priority="high"
                    category="Database"
                    :show-close="false"
                    :data="[
                        'Query Time' => '450ms',
                        'Active Queries' => '89',
                        'Lock Waits' => '12'
                    ]"
                    :actions="[
                        ['label' => 'Optimize', 'style' => 'primary', 'icon' => 'cog']
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="info"
                    variant="ghost"
                    title="Cache Status"
                    message="Cache hit ratio optimal"
                    status="active"
                    category="Cache"
                    :show-close="false"
                    :data="[
                        'Hit Ratio' => '94.2%',
                        'Memory Usage' => '67%',
                        'Evictions/min' => '3'
                    ]"
                />
                
                <x-admin.widgets.alert-widget
                    type="error"
                    variant="ghost"
                    title="Security Alerts"
                    message="Multiple failed login attempts"
                    status="active"
                    priority="critical"
                    category="Security"
                    :show-close="false"
                    :data="[
                        'Failed Attempts' => '15',
                        'Blocked IPs' => '3',
                        'Last Attempt' => '2 min ago'
                    ]"
                    :actions="[
                        ['label' => 'Investigate', 'style' => 'danger', 'icon' => 'search'],
                        ['label' => 'Block IP', 'style' => 'secondary', 'icon' => 'ban']
                    ]"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="success"
    variant="ghost"
    title="API Services"
    message="All services operational"
    :show-close="false"
    :data="['Uptime' =&gt; '99.9%', 'Response Time' =&gt; '125ms']"
/&gt;
            </div>
        </div>

        <!-- Dark Theme -->
        <div class="example-section bg-gray-800" style="background-color: #1f2937;">
            <h2 class="example-title text-white">9. Dark Theme Support</h2>
            <p class="example-description text-gray-300">
                Alerts with dark theme support for better integration with dark interfaces.
            </p>
            
            <div class="space-y-4">
                <x-admin.widgets.alert-widget
                    type="info"
                    title="Dark Theme Alert"
                    message="This alert is optimized for dark backgrounds."
                    theme="dark"
                    variant="ghost"
                />
                
                <x-admin.widgets.alert-widget
                    type="warning"
                    title="Dark Warning"
                    message="Warning message with dark theme styling."
                    description="Enhanced contrast and readability for dark interfaces."
                    theme="dark"
                    variant="outlined"
                    :actions="[
                        ['label' => 'Action', 'style' => 'primary'],
                        ['label' => 'Cancel', 'style' => 'ghost']
                    ]"
                />
            </div>
            
            <div class="code-block">
&lt;x-admin.widgets.alert-widget
    type="info"
    theme="dark"
    variant="ghost"
    title="Dark Theme Alert"
    message="Optimized for dark backgrounds."
/&gt;
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-12 py-8 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Alert Widget Component</h3>
            <p class="text-gray-600">
                Comprehensive alert system with status indicators, priority levels, interactive features, and real-time capabilities.
            </p>
            <div class="mt-4 flex justify-center space-x-4 text-sm text-gray-500">
                <span>✓ 5 Alert types</span>
                <span>✓ 5 Variants</span>
                <span>✓ Real-time updates</span>
                <span>✓ Interactive features</span>
                <span>✓ Accessibility support</span>
            </div>
        </div>
    </div>

    <!-- Custom styles for examples -->
    <style>
        .custom-gradient-border {
            border: 2px solid;
            border-image: linear-gradient(45deg, #3b82f6, #8b5cf6) 1;
        }
    </style>

    <!-- Include the alert widget styles and scripts -->
    @stack('styles')
    @stack('scripts')
</body>
</html>
