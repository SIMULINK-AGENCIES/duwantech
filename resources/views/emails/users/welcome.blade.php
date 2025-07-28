@extends('emails.layouts.base')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')
    <div class="text-center mb-4">
        <h1 style="color: #667eea; margin-bottom: 10px;">🎉 Welcome to {{ config('app.name') }}!</h1>
        <p style="font-size: 18px; color: #6b7280; margin: 0;">
            We're excited to have you join our community!
        </p>
    </div>

    <div class="alert alert-success">
        <strong>✅ Account Created Successfully!</strong><br>
        Your account has been created and is ready to use.
    </div>

    <h2>Hello {{ $userName }}! 👋</h2>
    
    <p>
        Welcome to {{ config('app.name') }}, your trusted e-commerce platform! 
        We're thrilled to have you as part of our growing community of satisfied customers.
    </p>

    <h3>🚀 Get Started</h3>
    <div class="card">
        <ul style="margin: 0; padding-left: 20px;">
            <li><strong>Complete your profile:</strong> Add your delivery address and phone number for faster checkout</li>
            <li><strong>Explore our products:</strong> Browse thousands of quality products at great prices</li>
            <li><strong>Set up payment methods:</strong> Add M-Pesa or card details for quick payments</li>
            <li><strong>Enable notifications:</strong> Stay updated on orders, offers, and new arrivals</li>
        </ul>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $profileUrl }}" class="btn btn-primary">
            👤 Complete Your Profile
        </a>
    </div>

    <h3>🛍️ Start Shopping</h3>
    <div class="card">
        <div class="card-header">Popular Categories</div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
            @foreach($popularCategories as $category)
            <div style="text-align: center; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <div style="font-size: 24px; margin-bottom: 8px;">{{ $category['icon'] }}</div>
                <div style="font-weight: 600; margin-bottom: 5px;">{{ $category['name'] }}</div>
                <a href="{{ $category['url'] }}" style="color: #667eea; text-decoration: none; font-size: 14px;">
                    Browse {{ $category['count'] }}+ items
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ $shopUrl }}" class="btn btn-success">
            🛒 Start Shopping Now
        </a>
    </div>

    @if($welcomeOffer)
    <div class="alert" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
        <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">
                🎁 Special Welcome Offer!
            </div>
            <div style="font-size: 16px; margin-bottom: 15px;">
                {{ $welcomeOffer['description'] }}
            </div>
            <div style="background: rgba(255, 255, 255, 0.2); padding: 10px 20px; border-radius: 6px; display: inline-block; font-weight: bold; letter-spacing: 1px;">
                Code: {{ $welcomeOffer['code'] }}
            </div>
            <div style="font-size: 14px; margin-top: 10px; opacity: 0.9;">
                Valid until {{ $welcomeOffer['expires']->format('M d, Y') }}
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ $shopUrl }}?code={{ $welcomeOffer['code'] }}" class="btn btn-warning">
            🏷️ Use Your Welcome Offer
        </a>
    </div>
    @endif

    <h3>📱 Stay Connected</h3>
    <div class="card">
        <div style="text-align: center;">
            <p>Follow us on social media for the latest updates, exclusive offers, and community highlights!</p>
            
            <div style="margin: 20px 0;">
                <a href="{{ config('app.social.facebook') }}" 
                   style="display: inline-block; margin: 0 10px; padding: 10px 20px; background-color: #1877f2; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    📘 Facebook
                </a>
                <a href="{{ config('app.social.instagram') }}" 
                   style="display: inline-block; margin: 0 10px; padding: 10px 20px; background-color: #E4405F; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    📸 Instagram
                </a>
                <a href="{{ config('app.social.twitter') }}" 
                   style="display: inline-block; margin: 0 10px; padding: 10px 20px; background-color: #1da1f2; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    🐦 Twitter
                </a>
            </div>

            @if($mobileApp)
            <p style="margin-top: 25px;"><strong>📲 Download Our Mobile App</strong></p>
            <div>
                <a href="{{ $mobileApp['android'] ?? '#' }}" 
                   style="display: inline-block; margin: 0 10px;">
                    <img src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png" 
                         alt="Get it on Google Play" style="height: 60px;">
                </a>
                <a href="{{ $mobileApp['ios'] ?? '#' }}" 
                   style="display: inline-block; margin: 0 10px;">
                    <img src="https://developer.apple.com/app-store/marketing/guidelines/images/badge-download-on-the-app-store.svg" 
                         alt="Download on the App Store" style="height: 60px;">
                </a>
            </div>
            @endif
        </div>
    </div>

    <h3>💬 We're Here to Help</h3>
    <div class="card">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 32px; margin-bottom: 10px;">📞</div>
                <div style="font-weight: 600; margin-bottom: 5px;">Call Us</div>
                <div style="color: #6b7280;">{{ $supportPhone }}</div>
                <div style="color: #6b7280; font-size: 14px;">Mon-Fri, 8AM-6PM</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; margin-bottom: 10px;">💬</div>
                <div style="font-weight: 600; margin-bottom: 5px;">Live Chat</div>
                <a href="{{ $liveChatUrl ?? '#' }}" style="color: #667eea; text-decoration: none;">
                    Start a conversation
                </a>
                <div style="color: #6b7280; font-size: 14px;">Available 24/7</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; margin-bottom: 10px;">📧</div>
                <div style="font-weight: 600; margin-bottom: 5px;">Email Support</div>
                <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none;">
                    {{ $supportEmail }}
                </a>
                <div style="color: #6b7280; font-size: 14px;">We reply within 2 hours</div>
            </div>
        </div>
    </div>

    <h3>🔐 Account Security</h3>
    <div class="card">
        <p>Your account security is important to us. Here are some tips to keep your account safe:</p>
        <ul style="margin: 15px 0; padding-left: 20px;">
            <li>Use a strong, unique password</li>
            <li>Enable two-factor authentication (2FA)</li>
            <li>Never share your login credentials</li>
            <li>Log out from shared devices</li>
        </ul>
        <div class="text-center">
            <a href="{{ $securityUrl ?? route('user.security') }}" class="btn btn-secondary">
                🛡️ Manage Security Settings
            </a>
        </div>
    </div>

    @if($loyaltyProgram)
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="text-center">
            <div style="font-size: 24px; font-weight: bold; margin-bottom: 15px;">
                🏆 Join Our Loyalty Program
            </div>
            <p style="margin-bottom: 20px; opacity: 0.95;">
                Earn points with every purchase and unlock exclusive rewards, early access to sales, and VIP support!
            </p>
            <div style="background: rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="font-size: 18px; font-weight: 600;">Welcome Bonus</div>
                <div style="font-size: 32px; font-weight: bold;">{{ $loyaltyProgram['welcomePoints'] }} Points</div>
                <div style="font-size: 14px; opacity: 0.9;">Added to your account!</div>
            </div>
            <a href="{{ $loyaltyProgram['url'] }}" 
               style="background: white; color: #f5576c; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                View Loyalty Dashboard
            </a>
        </div>
    </div>
    @endif

    <div class="alert alert-info mt-4">
        <strong>📢 Pro Tip:</strong> Add {{ $supportEmail }} to your contacts to ensure you never miss important updates about your orders and account.
    </div>

    <div class="text-center mt-4">
        <p style="font-size: 18px; color: #2d3748; margin-bottom: 15px;">
            Ready to start your shopping journey?
        </p>
        <a href="{{ $shopUrl }}" class="btn btn-primary" style="font-size: 18px; padding: 16px 32px;">
            🛍️ Explore Our Store
        </a>
    </div>

    <p class="text-center text-muted text-small mt-4">
        Welcome aboard, {{ $userName }}! We can't wait to serve you. 🚀
    </p>
@endsection

@section('tagline', 'Welcome to our community!')
