@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="min-h-screen bg-gray-50">
    <main class="max-w-7xl mx-auto px-4 sm:px-8 py-8 pb-32">
        <!-- Profile Header -->
        <div class="relative bg-white/80 backdrop-blur rounded-2xl shadow-lg flex flex-col md:flex-row items-center gap-6 p-6 md:p-8 mb-8 border border-gray-100">
            <div class="flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4F46E5&color=fff&size=128"
                     alt="Avatar"
                     class="w-28 h-28 rounded-full border-4 border-white shadow-md object-cover">
            </div>
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-1">{{ $user->name }}</h2>
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-6 justify-center md:justify-start text-gray-500 text-sm">
                    <span class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        {{ $user->email }}
                    </span>
                    <span class="hidden md:block">|</span>
                    <span>Member since {{ $user->created_at->format('M Y') }}</span>
                </div>
                <div class="flex gap-6 mt-4 justify-center md:justify-start">
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-bold text-gray-900">0</span>
                        <span class="text-xs text-gray-500">Posts</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-bold text-gray-900">0</span>
                        <span class="text-xs text-gray-500">Followers</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-lg font-bold text-gray-900">0</span>
                        <span class="text-xs text-gray-500">Following</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info / Account Details -->
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Contact Info
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Email</div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $user->email }}</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Email Verified</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Unverified</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Phone</div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800">{{ $user->phone ?? '-' }}</span>
                        @if($user->phone_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Phone Verified</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">Unverified</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Joined</div>
                    <span class="font-medium text-gray-800">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Role</div>
                    <span class="font-medium text-gray-800">{{ $user->hasRole('admin') ? 'Admin' : 'User' }}</span>
                </div>
            </div>
        </div>

        <!-- Profile Forms (Tabs/Accordion) -->
        <div x-data="{ tab: 'info' }" class="space-y-6 mb-8">
            <!-- Tabs -->
            <div class="flex gap-2 mb-4">
                <button @click="tab = 'info'" :class="tab === 'info' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-4 py-2 rounded-lg font-semibold shadow transition">Profile Info</button>
                <button @click="tab = 'password'" :class="tab === 'password' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-4 py-2 rounded-lg font-semibold shadow transition">Password</button>
                <button @click="tab = 'delete'" :class="tab === 'delete' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'" class="px-4 py-2 rounded-lg font-semibold shadow transition">Delete Account</button>
            </div>

            <!-- Profile Info Form -->
            <div x-show="tab === 'info'" x-transition>
                <div class="bg-white/80 backdrop-blur rounded-2xl shadow p-6 border border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        Profile Information
                    </h3>
                    <x-profile.update-profile-information-form />
                </div>
            </div>

            <!-- Password Form -->
            <div x-show="tab === 'password'" x-transition>
                <div class="bg-white/80 backdrop-blur rounded-2xl shadow p-6 border border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11c0-1.657-1.343-3-3-3s-3 1.343-3 3v2a3 3 0 006 0v-2z"/><path d="M17 16v-1a4 4 0 00-8 0v1"/></svg>
                        Change Password
                    </h3>
                    <x-profile.update-password-form />
                </div>
            </div>

            <!-- Delete Account Form -->
            <div x-show="tab === 'delete'" x-transition>
                <div class="bg-white/80 backdrop-blur rounded-2xl shadow p-6 border border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        Delete Account
                    </h3>
                    <x-profile.delete-user-form />
                </div>
            </div>
        </div>

        <!-- My Orders (Collapsible) -->
        <div x-data="{ open: false }" class="bg-white/80 backdrop-blur rounded-2xl shadow p-6 border border-gray-100">
            <button @click="open = !open" class="w-full flex items-center justify-between px-2 py-3 rounded-lg font-semibold text-gray-900 bg-gray-100 hover:bg-gray-200 transition mb-4">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
                    My Orders
                </span>
                <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-collapse>
                @if($user->orders->count())
                    <div class="divide-y divide-gray-200">
                        @foreach($user->orders as $order)
                            <div class="py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">#{{ $order->order_number }}</span>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                        <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('dashboard.orders') }}" class="text-blue-600 hover:underline text-xs font-medium mt-2 md:mt-0">Details</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">No orders yet.</div>
                @endif
            </div>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 lg:hidden">
        <div class="flex items-center justify-around py-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center p-2 text-gray-600 hover:text-blue-600">
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
            <a href="{{ route('dashboard.profile') }}" class="flex flex-col items-center p-2 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </div>
</div>
@endsection 