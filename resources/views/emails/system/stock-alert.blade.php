@extends('emails.layouts.base')

@section('title', 'Stock Alert - ' . $productName)

@section('content')
    <div class="alert alert-warning">
        <strong>⚠️ Low Stock Alert</strong><br>
        {{ $productName }} is running low on stock and needs immediate attention.
    </div>

    <h2>{{ $productName }}</h2>
    
    <div class="card">
        <table class="table">
            <tr>
                <td><strong>Current Stock:</strong></td>
                <td><strong style="color: {{ $currentStock <= 5 ? '#ef4444' : '#f59e0b' }};">
                    {{ $currentStock }} units
                </strong></td>
            </tr>
            <tr>
                <td><strong>Threshold:</strong></td>
                <td>{{ $threshold }} units</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="status-badge" style="background-color: {{ $urgencyColor }}; color: white;">
                        {{ $urgencyLevel }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Alert Time:</strong></td>
                <td>{{ now()->format('M d, Y \a\t g:i A') }}</td>
            </tr>
        </table>
    </div>

    <h3>Recommended Actions</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($recommendations as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $inventoryUrl }}" class="btn btn-primary">
            📦 Manage Inventory
        </a>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $reorderUrl }}" class="btn btn-success">
            🔄 Reorder Stock
        </a>
    </div>
@endsection

@section('tagline', 'Inventory management alert')
