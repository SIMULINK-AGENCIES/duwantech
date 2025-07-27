<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name') . ' - Admin Dashboard')</title>
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>

<body class="h-full bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r border-gray-200">
            <div class="p-4">
                <h2 class="text-lg font-semibold">Admin Panel</h2>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1">
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</body>
</html>
