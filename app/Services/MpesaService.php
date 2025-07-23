<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $passkey;
    protected string $shortcode;
    protected string $environment;

    public function __construct()
    {
        $this->baseUrl = config('services.mpesa.base_url', 'https://sandbox.safaricom.co.ke');
        $this->consumerKey = config('services.mpesa.consumer_key', '');
        $this->consumerSecret = config('services.mpesa.consumer_secret', '');
        $this->passkey = config('services.mpesa.passkey', '');
        $this->shortcode = config('services.mpesa.shortcode', '');
        $this->environment = config('services.mpesa.environment', 'sandbox');
    }

    public function initiateSTKPush(Order $order, string $phoneNumber): array
    {
        try {
            // Format phone number (remove +254 and add 254)
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Generate timestamp
            $timestamp = date('YmdHis');
            
            // Generate password
            $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
            
            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $order->amount,
                'PartyA' => $phoneNumber,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => route('mpesa.callback'),
                'AccountReference' => $order->order_number,
                'TransactionDesc' => "Payment for {$order->product->name}",
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['CheckoutRequestID'])) {
                // Create payment record
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $responseData['CheckoutRequestID'],
                    'amount' => $order->amount,
                    'phone_number' => $phoneNumber,
                    'status' => 'pending',
                    'mpesa_response' => $responseData,
                ]);

                Log::info('STK Push initiated successfully', [
                    'order_id' => $order->id,
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment initiated successfully. Please check your phone for M-PESA prompt.',
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'payment_id' => $payment->id,
                ];
            } else {
                Log::error('STK Push failed', [
                    'order_id' => $order->id,
                    'response' => $responseData,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to initiate payment. Please try again.',
                    'error' => $responseData['errorMessage'] ?? 'Unknown error',
                ];
            }
        } catch (\Exception $e) {
            Log::error('STK Push exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment service temporarily unavailable. Please try again later.',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleCallback(array $callbackData): void
    {
        try {
            Log::info('M-PESA callback received', $callbackData);

            $resultCode = $callbackData['ResultCode'] ?? null;
            $checkoutRequestId = $callbackData['CheckoutRequestID'] ?? null;
            $merchantRequestId = $callbackData['MerchantRequestID'] ?? null;

            // Find payment by transaction ID
            $payment = Payment::where('transaction_id', $checkoutRequestId)->first();

            if (!$payment) {
                Log::error('Payment not found for callback', [
                    'checkout_request_id' => $checkoutRequestId,
                    'callback_data' => $callbackData,
                ]);
                return;
            }

            // Update payment with callback response
            $payment->update([
                'mpesa_response' => array_merge($payment->mpesa_response ?? [], $callbackData),
            ]);

            if ($resultCode === 0) {
                // Payment successful
                $receiptNumber = $callbackData['MpesaReceiptNumber'] ?? null;
                $payment->markAsSuccessful($receiptNumber, $callbackData);

                Log::info('Payment successful', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'receipt_number' => $receiptNumber,
                ]);
            } else {
                // Payment failed
                $payment->markAsFailed($callbackData);

                Log::error('Payment failed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'result_code' => $resultCode,
                    'result_desc' => $callbackData['ResultDesc'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Callback handling exception', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData,
            ]);
        }
    }

    protected function getAccessToken(): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret),
        ])->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        $data = $response->json();
        return $data['access_token'] ?? '';
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If it starts with 0, replace with 254
        if (strlen($phoneNumber) === 10 && $phoneNumber[0] === '0') {
            $phoneNumber = '254' . substr($phoneNumber, 1);
        }
        
        // If it starts with +254, remove the +
        if (strlen($phoneNumber) === 13 && $phoneNumber[0] === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        
        return $phoneNumber;
    }
} 