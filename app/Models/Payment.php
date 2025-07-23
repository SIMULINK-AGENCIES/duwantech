<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'mpesa_receipt_number',
        'transaction_id',
        'amount',
        'phone_number',
        'status',
        'mpesa_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'mpesa_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsSuccessful(string $receiptNumber, array $response = []): void
    {
        $this->update([
            'status' => 'success',
            'mpesa_receipt_number' => $receiptNumber,
            'mpesa_response' => $response,
            'paid_at' => now(),
        ]);

        // Mark the order as paid
        $this->order->markAsPaid();
    }

    public function markAsFailed(array $response = []): void
    {
        $this->update([
            'status' => 'failed',
            'mpesa_response' => $response,
        ]);

        // Mark the order as failed
        $this->order->markAsFailed();
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
