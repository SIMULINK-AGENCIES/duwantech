@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-40">
        <div class="flex items-center justify-between px-4 py-4 md:py-6 max-w-7xl mx-auto">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900">My Dashboard</h1>
            <div class="flex items-center space-x-4">
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Admin
                    </a>
                @endif
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    Shop
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-500 to-orange-400 rounded-2xl p-6 md:p-10 text-white mb-8 shadow">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-orange-100 text-lg">Track your orders and manage your account</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <x-dashboard.card icon="shopping-bag" color="blue" label="Total Orders" :value="$stats['total_orders']" />
                <x-dashboard.card icon="check-circle" color="green" label="Completed" :value="$stats['completed_orders']" />
                <x-dashboard.card icon="clock" color="yellow" label="Pending" :value="$stats['pending_orders']" />
                <x-dashboard.card icon="currency-dollar" color="purple" label="Total Spent" :value="'KES ' . number_format($stats['total_spent'])" />
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <x-dashboard.card action href="{{ route('dashboard.orders') }}" icon="clipboard-list" color="blue" label="View All Orders" />
                <x-dashboard.card action href="{{ route('dashboard.profile') }}" icon="user" color="green" label="Edit Profile" />
                <x-dashboard.card action href="{{ route('home') }}" icon="shopping-cart" color="purple" label="Continue Shopping" />
            </div>

            <!-- Recent Orders -->
            <x-dashboard.order-list :orders="$orders" />
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 lg:hidden">
        <div class="flex items-center justify-around py-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 text-blue-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                <span class="text-xs mt-1">Dashboard</span>
            </a>
            <a href="{{ route('dashboard.orders') }}" class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="text-xs mt-1">Orders</span>
            </a>
            <a href="{{ route('dashboard.profile') }}" class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection 