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

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;
    public $order;
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, Order $order)
    {
        $this->payment = $payment;
        $this->order = $order;
        $this->customer = $order->user;
        
        // Set queue connection
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(3));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->customer->email],
            subject: 'Payment Confirmed - Order ' . $this->order->order_number,
            tags: ['payment-confirmation'],
            metadata: [
                'payment_id' => $this->payment->id,
                'order_id' => $this->order->id,
                'customer_id' => $this->customer->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payments.confirmation',
            with: [
                'customerName' => $this->customer->name,
                'orderNumber' => $this->order->order_number,
                'paymentAmount' => $this->payment->amount,
                'paymentMethod' => $this->payment->payment_method,
                'transactionId' => $this->payment->transaction_id,
                'mpesaReceiptNumber' => $this->payment->mpesa_receipt_number,
                'paymentDate' => $this->payment->created_at,
                'paymentStatus' => $this->payment->status,
                'productName' => $this->order->product->name,
                'orderDate' => $this->order->created_at,
                'orderStatus' => $this->order->status,
                'trackingUrl' => route('orders.track', $this->order->order_number),
                'receiptUrl' => route('orders.receipt', $this->order->id),
                'storeName' => config('app.name'),
                'storeUrl' => config('app.url'),
                'supportEmail' => config('mail.from.address'),
                'supportPhone' => config('app.support_phone', '+254 700 000 000'),
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
        // Send payment confirmations only for successful payments
        return $this->payment->status === 'completed' && 
               ($this->customer->email_notifications ?? true);
    }
}
