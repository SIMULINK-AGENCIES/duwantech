@extends('emails.layouts.base')

@section('title', 'Payment Confirmation - ' . $orderNumber)

@section('content')
    <div class="alert alert-success">
        <strong>✅ Payment Confirmed!</strong><br>
        Your payment has been successfully processed and confirmed.
    </div>

    <h2>Payment Receipt</h2>
    
    <div class="card">
        <div class="card-header">Transaction Details</div>
        
        <table class="table">
            <tr>
                <td><strong>Order Number:</strong></td>
                <td>{{ $orderNumber }}</td>
            </tr>
            <tr>
                <td><strong>Transaction ID:</strong></td>
                <td>{{ $transactionId }}</td>
            </tr>
            @if($mpesaReceiptNumber)
            <tr>
                <td><strong>M-Pesa Receipt:</strong></td>
                <td>{{ $mpesaReceiptNumber }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ $paymentMethod }}</td>
            </tr>
            <tr>
                <td><strong>Amount Paid:</strong></td>
                <td><strong>KES {{ number_format($amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Payment Date:</strong></td>
                <td>{{ $paymentDate->format('M d, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td><span class="status-badge" style="background-color: #10b981; color: white;">CONFIRMED</span></td>
            </tr>
        </table>
    </div>

    @if($paymentMethod === 'M-Pesa')
    <div class="card">
        <div class="card-header">M-Pesa Payment Details</div>
        
        <table class="table">
            @if(isset($mpesaPhone))
            <tr>
                <td><strong>Phone Number:</strong></td>
                <td>{{ $mpesaPhone }}</td>
            </tr>
            @endif
            @if(isset($mpesaAccount))
            <tr>
                <td><strong>Account Reference:</strong></td>
                <td>{{ $mpesaAccount }}</td>
            </tr>
            @endif
            @if(isset($mpesaTransactionDate))
            <tr>
                <td><strong>M-Pesa Transaction Date:</strong></td>
                <td>{{ $mpesaTransactionDate->format('M d, Y \a\t g:i A') }}</td>
            </tr>
            @endif
            @if(isset($mpesaBalance))
            <tr>
                <td><strong>Account Balance:</strong></td>
                <td>KES {{ number_format($mpesaBalance, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <h3>Order Summary</h3>
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
                    <span class="text-muted">Quantity: {{ $quantity ?? 1 }}</span><br>
                    @if(isset($unitPrice))
                        <span class="text-muted">Unit Price: KES {{ number_format($unitPrice, 2) }}</span>
                    @endif
                </td>
                <td class="text-right">
                    <strong>KES {{ number_format($amount, 2) }}</strong>
                </td>
            </tr>
        </table>

        <!-- Payment Breakdown -->
        <div style="border-top: 2px solid #e5e7eb; padding-top: 15px; margin-top: 15px;">
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">KES {{ number_format($subtotal ?? $amount, 2) }}</td>
                </tr>
                @if(isset($shipping) && $shipping > 0)
                <tr>
                    <td>Shipping:</td>
                    <td class="text-right">KES {{ number_format($shipping, 2) }}</td>
                </tr>
                @endif
                @if(isset($tax) && $tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">KES {{ number_format($tax, 2) }}</td>
                </tr>
                @endif
                @if(isset($discount) && $discount > 0)
                <tr style="color: #10b981;">
                    <td>Discount:</td>
                    <td class="text-right">-KES {{ number_format($discount, 2) }}</td>
                </tr>
                @endif
                <tr style="font-weight: 600; font-size: 16px; border-top: 1px solid #d1d5db; padding-top: 10px;">
                    <td>Total Paid:</td>
                    <td class="text-right">KES {{ number_format($amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if($customerEmail)
    <div class="alert alert-info">
        <strong>📧 Receipt Sent:</strong> A copy of this receipt has been sent to {{ $customerEmail }}
    </div>
    @endif

    <h3>What Happens Next?</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            <li>Your order is now confirmed and will be processed immediately</li>
            <li>You will receive shipping updates via email and SMS</li>
            <li>Track your order anytime using the link below</li>
            @if($estimatedDelivery)
            <li>Expected delivery: {{ $estimatedDelivery->format('l, M d, Y') }}</li>
            @endif
        </ul>
    </div>

    @if($paymentMethod === 'M-Pesa')
    <div class="alert alert-success">
        <strong>💡 M-Pesa Tip:</strong> Keep your M-Pesa receipt number <strong>{{ $mpesaReceiptNumber }}</strong> for your records.
        You can also check your M-Pesa balance by dialing *334# on your phone.
    </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ $trackingUrl }}" class="btn btn-primary">
            📦 Track Your Order
        </a>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $receiptUrl ?? '#' }}" class="btn btn-secondary">
            📄 Download Receipt
        </a>
    </div>

    <!-- Customer Support -->
    <div class="text-center mt-4">
        <a href="{{ $supportUrl }}" style="color: #667eea; text-decoration: none;">
            Questions about your payment? Contact our support team
        </a><br>
        <span class="text-small text-muted">
            Email: {{ $supportEmail }} | Phone: {{ $supportPhone }}
        </span>
    </div>

    <!-- Security Notice -->
    <div class="alert alert-info mt-4">
        <strong>🔒 Security Notice:</strong> This is an automated confirmation email. 
        {{ config('app.name') }} will never ask for your payment details via email.
        If you receive suspicious emails, please report them to {{ $supportEmail }}.
    </div>

    <!-- Referral Program -->
    @if(isset($referralUrl))
    <div class="card mt-4">
        <div class="card-header">💰 Earn Rewards!</div>
        <p>Love your purchase? Refer friends and earn credits for future orders!</p>
        <div class="text-center">
            <a href="{{ $referralUrl }}" class="btn btn-success">
                Share & Earn Credits
            </a>
        </div>
    </div>
    @endif

    <p class="text-center text-muted text-small mt-4">
        Thank you for your payment and for choosing {{ $storeName }}!
    </p>

    <!-- Tax Information -->
    @if(config('app.country') === 'Kenya')
    <p class="text-center text-muted text-small">
        VAT Registration Number: {{ config('app.vat_number', 'P000000000V') }}<br>
        This serves as your official receipt for tax purposes.
    </p>
    @endif
@endsection

@section('tagline', 'Payment confirmed successfully')
