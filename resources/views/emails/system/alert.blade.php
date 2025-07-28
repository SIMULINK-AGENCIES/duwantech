@extends('emails.layouts.base')

@section('title', $alertTitle)

@section('content')
    <div class="alert" style="background-color: {{ $severityColor }}1a; border-left-color: {{ $severityColor }}; color: {{ $severityColor }};">
        <strong>{{ $severityIcon }} {{ $alertTitle }}</strong><br>
        {{ $alertMessage }}
    </div>

    @if($alertData)
    <h3>Alert Details</h3>
    <div class="card">
        <table class="table">
            @foreach($alertData as $key => $value)
            <tr>
                <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <h3>System Information</h3>
    <div class="card">
        <table class="table">
            <tr>
                <td><strong>Alert Type:</strong></td>
                <td>{{ ucfirst(str_replace('_', ' ', $alertType)) }}</td>
            </tr>
            <tr>
                <td><strong>Severity:</strong></td>
                <td>
                    <span class="status-badge" style="background-color: {{ $severityColor }}; color: white;">
                        {{ strtoupper($severity) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Timestamp:</strong></td>
                <td>{{ $timestamp->format('M d, Y \a\t g:i A T') }}</td>
            </tr>
            <tr>
                <td><strong>Environment:</strong></td>
                <td>{{ $serverInfo['environment'] }}</td>
            </tr>
        </table>
    </div>

    @if($recommendations)
    <h3>Recommended Actions</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($recommendations as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($systemHealth)
    <h3>System Health</h3>
    <div class="card">
        <table class="table">
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ ucfirst($systemHealth['status']) }}</td>
            </tr>
            <tr>
                <td><strong>Uptime:</strong></td>
                <td>{{ $systemHealth['uptime'] }}</td>
            </tr>
            <tr>
                <td><strong>Last Backup:</strong></td>
                <td>{{ $systemHealth['last_backup'] }}</td>
            </tr>
            <tr>
                <td><strong>Active Users:</strong></td>
                <td>{{ $systemHealth['active_users'] }}</td>
            </tr>
        </table>
    </div>
    @endif

    @if($actionUrl)
    <div class="text-center mt-4">
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            {{ $actionText ?? 'View Details' }}
        </a>
    </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ $dashboardUrl }}" class="btn btn-secondary">
            🏠 Admin Dashboard
        </a>
    </div>

    <div class="alert alert-info mt-4">
        <strong>🔒 Security Note:</strong> This is an automated system alert. 
        If you believe you received this message in error, please contact your system administrator.
    </div>
@endsection

@section('tagline', 'System alert notification')
