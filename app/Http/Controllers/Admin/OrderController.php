<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'product', 'payment'])
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'product', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function transactions()
    {
        $transactions = Order::with(['user', 'product', 'payment'])
            ->whereHas('payment')
            ->latest()
            ->paginate(20);

        $stats = [
            'total_transactions' => Order::whereHas('payment')->count(),
            'total_revenue' => Order::whereHas('payment')->sum('amount'),
            'successful_payments' => Order::whereHas('payment', function($query) {
                $query->where('status', 'completed');
            })->count(),
            'pending_payments' => Order::whereHas('payment', function($query) {
                $query->where('status', 'pending');
            })->count(),
        ];

        return view('admin.orders.transactions', compact('transactions', 'stats'));
    }
} 