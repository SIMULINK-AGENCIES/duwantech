@extends('emails.layouts.base')

@section('title', 'Order Confirmation - ' . $orderNumber)

@section('content')
    <div class="alert alert-success">
        <strong>🎉 Order Confirmed!</strong><br>
        Thank you for your purchase! Your order has been confirmed and is being processed.
    </div>

    <h2>Order Details</h2>
    
    <div class="card">
        <div class="card-header">Order #{{ $orderNumber }}</div>
        
        <table class="table">
            <tr>
                <td><strong>Order Date:</strong></td>
                <td>{{ $orderDate->format('M d, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ $paymentMethod }}</td>
            </tr>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td><strong>KES {{ number_format($orderAmount, 2) }}</strong></td>
            </tr>
            @if($estimatedDelivery)
            <tr>
                <td><strong>Estimated Delivery:</strong></td>
                <td>{{ $estimatedDelivery->format('M d, Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <h3>Product Information</h3>
    <div class="card">
        <table class="table">
            <tr>
                <td style="width: 80px;">
                    @if($productImage)
                        <img src="{{ $productImage }}" alt="{{ $productName }}" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                    @endif
                </td>
                <td>
                    <strong>{{ $productName }}</strong><br>
                    <span class="text-muted">Quantity: {{ $quantity }}</span><br>
                    <span class="text-muted">Unit Price: KES {{ number_format($unitPrice, 2) }}</span>
                </td>
                <td class="text-right">
                    <strong>KES {{ number_format($orderAmount, 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    @if($deliveryAddress)
    <h3>Delivery Information</h3>
    <div class="card">
        <div class="card-header">Shipping Address</div>
        <p>{{ $deliveryAddress }}</p>
        
        @if($deliveryNotes)
        <p><strong>Delivery Notes:</strong> {{ $deliveryNotes }}</p>
        @endif
    </div>
    @endif

    @if($paymentMethod === 'M-Pesa')
    <div class="alert alert-info">
        <strong>M-Pesa Payment Details</strong><br>
        Transaction ID: {{ $transactionId ?? 'Processing...' }}<br>
        @if(isset($mpesaReceiptNumber))
            Receipt Number: {{ $mpesaReceiptNumber }}<br>
        @endif
        Amount: KES {{ number_format($orderAmount, 2) }}
    </div>
    @endif

    <h3>What's Next?</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            <li>You will receive an email notification when your order is shipped</li>
            <li>Track your order status anytime using the button below</li>
            <li>Our customer service team is available if you have any questions</li>
            @if($estimatedDelivery)
            <li>Expected delivery: {{ $estimatedDelivery->format('l, M d, Y') }}</li>
            @endif
        </ul>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $trackingUrl }}" class="btn btn-primary">
            📦 Track Your Order
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

    <div class="alert alert-info mt-4">
        <strong>💡 Pro Tip:</strong> Save this email for your records and tracking information.
    </div>

    <p class="text-center text-muted text-small mt-4">
        Thank you for choosing {{ $storeName }}! We appreciate your business.
    </p>
@endsection

@section('tagline', 'Order confirmed and being processed')
