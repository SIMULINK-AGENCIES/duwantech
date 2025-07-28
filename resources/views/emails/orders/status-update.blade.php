@extends('emails.layouts.base')

@section('title', 'Order Update - ' . $statusTitle)

@section('content')
    <div class="alert" style="background-color: {{ $statusColor }}1a; border-left-color: {{ $statusColor }}; color: {{ $statusColor }};">
        <strong>{{ $statusTitle }}</strong><br>
        {{ $statusMessage }}
    </div>

    <h2>Order #{{ $orderNumber }}</h2>
    
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
                    <span class="text-muted">Order Date: {{ $orderDate->format('M d, Y') }}</span><br>
                    <span class="text-muted">Amount: KES {{ number_format($orderAmount, 2) }}</span>
                </td>
                <td class="text-right">
                    <span class="status-badge" style="background-color: {{ $statusColor }}; color: white;">
                        {{ $statusTitle }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    @if($newStatus === 'shipped' && $trackingNumber)
    <div class="alert alert-info">
        <strong>📦 Tracking Information</strong><br>
        Your package is on its way! Track it using:<br>
        <strong>Tracking Number: {{ $trackingNumber }}</strong>
    </div>
    @endif

    @if($newStatus === 'delivered')
    <div class="alert alert-success">
        <strong>🎉 Delivery Confirmed!</strong><br>
        Your order has been successfully delivered. We hope you love your purchase!
    </div>
    @endif

    @if($newStatus === 'cancelled' || $newStatus === 'refunded')
    <div class="alert alert-warning">
        <strong>⚠️ Order {{ ucfirst($newStatus) }}</strong><br>
        @if($newStatus === 'refunded')
            Your refund will be processed within 3-5 business days.
        @else
            If you have any questions about this cancellation, please contact our support team.
        @endif
    </div>
    @endif

    <!-- Order Timeline -->
    <h3>Order Progress</h3>
    <div class="card">
        <div style="position: relative;">
            @foreach($timeline as $index => $step)
                <div style="display: flex; align-items: center; margin-bottom: {{ $loop->last ? '0' : '20px' }};">
                    <div style="width: 24px; height: 24px; border-radius: 50%; 
                                background-color: {{ $step['completed'] ? '#10b981' : '#e5e7eb' }};
                                border: 2px solid {{ $step['current'] ?? false ? $statusColor : ($step['completed'] ? '#10b981' : '#d1d5db') }};
                                display: flex; align-items: center; justify-content: center; margin-right: 15px; position: relative; z-index: 2;">
                        @if($step['completed'])
                            <span style="color: white; font-size: 12px;">✓</span>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: {{ $step['current'] ?? false ? '600' : '400' }}; 
                                    color: {{ $step['current'] ?? false ? $statusColor : ($step['completed'] ? '#374151' : '#6b7280') }};">
                            {{ $step['title'] }}
                        </div>
                        @if(isset($step['date']) && $step['date'])
                            <div class="text-small text-muted">
                                {{ $step['date']->format('M d, Y \a\t g:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
                
                @if(!$loop->last)
                    <div style="width: 2px; height: 20px; background-color: {{ $step['completed'] ? '#10b981' : '#e5e7eb' }}; 
                                margin-left: 11px; margin-bottom: 5px; margin-top: -15px; position: relative; z-index: 1;"></div>
                @endif
            @endforeach
        </div>
    </div>

    @if($deliveryAddress && in_array($newStatus, ['shipped', 'out_for_delivery']))
    <h3>Delivery Address</h3>
    <div class="card">
        <p>{{ $deliveryAddress }}</p>
    </div>
    @endif

    <h3>What's Next?</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($nextSteps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ul>
    </div>

    <div class="text-center mt-4">
        @if(!in_array($newStatus, ['delivered', 'cancelled', 'refunded']))
            <a href="{{ $trackingUrl }}" class="btn btn-primary">
                📦 Track Your Order
            </a>
        @elseif($newStatus === 'delivered')
            <a href="{{ route('orders.review', $orderNumber) ?? '#' }}" class="btn btn-success">
                ⭐ Rate Your Experience
            </a>
        @endif
    </div>

    @if($newStatus === 'delivered')
    <div class="text-center mt-4">
        <p class="text-muted">Love your purchase? Share it with friends!</p>
        <div style="margin: 15px 0;">
            <a href="{{ config('app.social.facebook') }}?share={{ urlencode(config('app.url')) }}" 
               style="display: inline-block; margin: 0 10px; padding: 8px 16px; background-color: #1877f2; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">
                Share on Facebook
            </a>
            <a href="{{ config('app.social.twitter') }}?text={{ urlencode('Just received my order from ' . config('app.name') . '!') }}" 
               style="display: inline-block; margin: 0 10px; padding: 8px 16px; background-color: #1da1f2; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">
                Share on Twitter
            </a>
        </div>
    </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('contact') ?? '#' }}" style="color: #667eea; text-decoration: none;">
            Have questions? Contact our support team
        </a><br>
        <span class="text-small text-muted">
            Email: {{ $supportEmail }} | Phone: {{ $supportPhone }}
        </span>
    </div>

    @if($newStatus === 'delivered')
    <div class="alert alert-success mt-4">
        <strong>🛍️ Shop Again:</strong> Discover more amazing products in our store!<br>
        <a href="{{ $storeUrl }}" style="color: #065f46; font-weight: 600;">Browse our latest collection</a>
    </div>
    @endif

    <p class="text-center text-muted text-small mt-4">
        Thank you for choosing {{ $storeName }}!
    </p>
@endsection

@section('tagline')
    Order {{ $statusTitle }} - {{ $orderNumber }}
@endsection
