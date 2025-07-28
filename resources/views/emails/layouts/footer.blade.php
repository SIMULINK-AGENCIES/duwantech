<p>
    <strong>{{ config('app.name') }}</strong><br>
    Your trusted e-commerce platform
</p>

<div class="social-links">
    <a href="{{ config('app.social.facebook', '#') }}">Facebook</a>
    <a href="{{ config('app.social.twitter', '#') }}">Twitter</a>
    <a href="{{ config('app.social.instagram', '#') }}">Instagram</a>
    <a href="{{ config('app.social.linkedin', '#') }}">LinkedIn</a>
</div>

<p>
    <a href="{{ config('app.url') }}">Visit our website</a> |
    <a href="{{ route('contact') ?? '#' }}">Contact Support</a> |
    <a href="{{ route('privacy') ?? '#' }}">Privacy Policy</a>
</p>

<p class="text-small text-muted">
    You're receiving this email because you have an account with {{ config('app.name') }}.<br>
    <a href="{{ route('user.preferences') ?? '#' }}">Manage your email preferences</a> |
    <a href="{{ route('unsubscribe') ?? '#' }}">Unsubscribe</a>
</p>

<p class="text-small text-muted">
    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
    {{ config('app.address', 'Nairobi, Kenya') }}
</p>
