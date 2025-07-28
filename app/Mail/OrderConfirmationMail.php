<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $product;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->customer = $order->user;
        $this->product = $order->product;
        
        // Set queue connection and delay if needed
        $this->onQueue('emails');
        $this->delay(now()->addSeconds(5)); // Small delay for better UX
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->customer->email],
            subject: 'Order Confirmation - ' . $this->order->order_number,
            tags: ['order-confirmation'],
            metadata: [
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
            view: 'emails.orders.confirmation',
            with: [
                'orderNumber' => $this->order->order_number,
                'customerName' => $this->customer->name,
                'productName' => $this->product->name,
                'productImage' => $this->product->image,
                'orderAmount' => $this->order->amount,
                'orderDate' => $this->order->created_at,
                'orderStatus' => $this->order->status,
                'quantity' => $this->order->quantity ?? 1,
                'paymentMethod' => $this->order->payment_method ?? 'M-Pesa',
                'deliveryAddress' => $this->order->delivery_address,
                'phoneNumber' => $this->order->phone_number,
                'estimatedDelivery' => $this->order->estimated_delivery ?? now()->addDays(3),
                'trackingUrl' => route('orders.track', $this->order->order_number),
                'supportEmail' => config('mail.from.address'),
                'supportPhone' => config('app.support_phone', '+254 700 000 000'),
                'storeName' => config('app.name'),
                'storeUrl' => config('app.url'),
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
        // Check if customer has email notifications enabled
        return $this->customer->email_notifications ?? true;
    }
}
