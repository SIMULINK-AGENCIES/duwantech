<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected MpesaService $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    public function show($productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        
        return view('checkout.show', [
            'product' => $product,
        ]);
    }

    public function process(Request $request, $productSlug)
    {
        $product = Product::where('slug', $productSlug)->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'amount' => $product->price,
                'status' => 'pending',
                'payment_method' => 'mpesa',
                'phone_number' => $request->phone_number,
                'notes' => $request->notes,
            ]);

            // Increment product views
            $product->increment('views');

            // Initiate M-PESA STK Push
            $result = $this->mpesaService->initiateSTKPush($order, $request->phone_number);

            if ($result['success']) {
                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    public function success($orderNumber)
    {
        $order = Order::with(['product', 'payment'])
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.success', [
            'order' => $order,
        ]);
    }

    public function callback(Request $request)
    {
        // Verify the callback is from M-PESA (you should add proper validation)
        $callbackData = $request->all();
        
        $this->mpesaService->handleCallback($callbackData);

        return response()->json(['status' => 'success']);
    }

    public function status($orderNumber)
    {
        $order = Order::with(['product', 'payment'])
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'order_status' => $order->status,
            'payment_status' => $order->payment?->status ?? 'pending',
            'is_paid' => $order->isPaid(),
        ]);
    }
}
