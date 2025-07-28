@extends('emails.layouts.base')

@section('title', 'Payment Failed - ' . $orderNumber)

@section('content')
    <div class="alert alert-danger">
        <strong>❌ Payment Failed</strong><br>
        We were unable to process your payment for order #{{ $orderNumber }}.
        {{ $failureReason }}
    </div>

    <h2>Order #{{ $orderNumber }}</h2>
    
    <div class="card">
        <table class="table">
            <tr>
                <td><strong>Order Date:</strong></td>
                <td>{{ $orderDate->format('M d, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Amount:</strong></td>
                <td><strong>KES {{ number_format($orderAmount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ $paymentMethod }}</td>
            </tr>
            <tr>
                <td><strong>Failure Reason:</strong></td>
                <td>{{ $failureReason }}</td>
            </tr>
        </table>
    </div>

    <h3>What Can You Do?</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($recommendations as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $retryUrl }}" class="btn btn-primary">
            🔄 Try Payment Again
        </a>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $supportUrl }}" style="color: #667eea; text-decoration: none;">
            Need help? Contact our support team
        </a><br>
        <span class="text-small text-muted">
            Email: {{ $supportEmail }} | Phone: {{ $supportPhone }}
        </span>
    </div>
@endsection

@section('tagline', 'Payment processing failed')
