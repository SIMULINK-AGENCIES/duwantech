<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }} - Under Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <i class="fas fa-tools text-6xl text-yellow-500 mb-4"></i>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Under Maintenance</h1>
            <p class="text-gray-600">{{ $siteName }} is currently undergoing scheduled maintenance.</p>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-500 text-sm">
                We're working hard to improve your experience. Please check back soon!
            </p>
        </div>
        
        @if($contactEmail)
        <div class="border-t pt-4">
            <p class="text-sm text-gray-500">
                Questions? Contact us at 
                <a href="mailto:{{ $contactEmail }}" class="text-blue-600 hover:text-blue-800">
                    {{ $contactEmail }}
                </a>
            </p>
        </div>
        @endif
        
        <div class="mt-6">
            <button onclick="window.location.reload()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                <i class="fas fa-sync-alt mr-2"></i>
                Try Again
            </button>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>
