<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Email client fixes */
        .ExternalClass {
            width: 100%;
        }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
            line-height: 100%;
        }
        .es-button {
            mso-style-priority: 100 !important;
            text-decoration: none !important;
        }
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-logo {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .email-tagline {
            color: #e2e8f0;
            font-size: 14px;
            margin: 0;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .email-greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .email-content {
            color: #4a5568;
            line-height: 1.6;
            font-size: 16px;
        }
        
        .email-content p {
            margin: 0 0 20px 0;
        }
        
        .email-content h1, .email-content h2, .email-content h3 {
            color: #2d3748;
            margin: 30px 0 15px 0;
        }
        
        .email-content h1 {
            font-size: 24px;
        }
        
        .email-content h2 {
            font-size: 20px;
        }
        
        .email-content h3 {
            font-size: 18px;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #667eea;
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background-color: #5a6fd8;
        }
        
        .btn-success {
            background-color: #10b981;
            color: #ffffff;
        }
        
        .btn-warning {
            background-color: #f59e0b;
            color: #ffffff;
        }
        
        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: #ffffff;
        }
        
        .alert {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #ecfdf5;
            border-left-color: #10b981;
            color: #065f46;
        }
        
        .alert-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
            color: #92400e;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            border-left-color: #ef4444;
            color: #991b1b;
        }
        
        .alert-info {
            background-color: #eff6ff;
            border-left-color: #3b82f6;
            color: #1e40af;
        }
        
        .card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            margin: 20px 0;
        }
        
        .card-header {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #2d3748;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-small {
            font-size: 14px;
        }
        
        .text-muted {
            color: #6b7280;
        }
        
        .mt-4 {
            margin-top: 32px;
        }
        
        .mb-4 {
            margin-bottom: 32px;
        }
        
        .email-footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .email-footer-content {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .email-footer-content a {
            color: #667eea;
            text-decoration: none;
        }
        
        .email-footer-content a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #6b7280;
            text-decoration: none;
            font-size: 16px;
        }
        
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            
            .email-header,
            .email-body,
            .email-footer {
                padding: 20px !important;
            }
            
            .btn {
                display: block !important;
                width: 85% !important;
                margin: 20px auto !important;
            }
            
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 8px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f2937 !important;
            }
            
            .email-content,
            .email-greeting {
                color: #f9fafb !important;
            }
            
            .card {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
            }
            
            .table th {
                background-color: #374151 !important;
                color: #f9fafb !important;
            }
            
            .table td {
                border-color: #4b5563 !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <a href="{{ config('app.url') }}" class="email-logo">
                {{ config('app.name') }}
            </a>
            <p class="email-tagline">@yield('tagline', 'Your trusted e-commerce platform')</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            @if(isset($greeting))
                <div class="email-greeting">{{ $greeting }}</div>
            @endif
            
            <div class="email-content">
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="email-footer-content">
                @yield('footer', view('emails.layouts.footer'))
            </div>
        </div>
    </div>
</body>
</html>
