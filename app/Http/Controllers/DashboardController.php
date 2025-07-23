<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with(['product', 'payment'])->latest()->paginate(10);
        
        $stats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'total_spent' => $user->orders()->sum('amount'),
        ];

        return view('dashboard.index', compact('user', 'orders', 'stats'));
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->with(['product', 'payment'])->latest()->paginate(15);
        
        return view('dashboard.orders', compact('orders'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'phone', 'email']));

        return redirect()->route('dashboard.profile')->with('success', 'Profile updated successfully!');
    }
} 