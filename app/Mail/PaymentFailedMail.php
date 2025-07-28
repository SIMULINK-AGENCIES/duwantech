<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;
    public $order;
    public $customer;
    public $failureReason;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, Order $order, string $failureReason = null)
    {
        $this->payment = $payment;
        $this->order = $order;
        $this->customer = $order->user;
        $this->failureReason = $failureReason ?? 'Payment could not be processed';
        
        // Set queue connection with higher priority for failed payments
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(1));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->customer->email],
            subject: 'Payment Failed - Order ' . $this->order->order_number,
            tags: ['payment-failed'],
            metadata: [
                'payment_id' => $this->payment->id,
                'order_id' => $this->order->id,
                'customer_id' => $this->customer->id,
                'failure_reason' => $this->failureReason,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payments.failed',
            with: [
                'customerName' => $this->customer->name,
                'orderNumber' => $this->order->order_number,
                'paymentAmount' => $this->payment->amount,
                'paymentMethod' => $this->payment->payment_method,
                'failureReason' => $this->failureReason,
                'transactionId' => $this->payment->transaction_id,
                'paymentDate' => $this->payment->created_at,
                'productName' => $this->order->product->name,
                'retryPaymentUrl' => route('orders.retry-payment', $this->order->id),
                'supportEmail' => config('mail.from.address'),
                'supportPhone' => config('app.support_phone', '+254 700 000 000'),
                'storeName' => config('app.name'),
                'storeUrl' => config('app.url'),
                'alternativePaymentMethods' => [
                    'M-Pesa' => 'Pay via M-Pesa mobile money',
                    'Bank Transfer' => 'Direct bank transfer',
                    'Cash on Delivery' => 'Pay when you receive your order',
                ],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSend(): bool
    {
        // Always send payment failure notifications
        return $this->customer->email_notifications ?? true;
    }
}
