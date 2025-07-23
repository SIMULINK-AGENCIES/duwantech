<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $key = Str::lower($request->input('login')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['login' => __('Too many login attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])]);
        }
        $request->validate([
            'website' => ['max:0'], // Honeypot: must be empty
        ], [
            'website.max' => 'Bot detected.'
        ]);

        // Validate Google reCAPTCHA v3
        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if ($recaptchaSecret && $recaptchaResponse) {
            $recaptcha = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);
            if (!($recaptcha->json('success') && $recaptcha->json('score', 0) > 0.5)) {
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
            }
        }
        $response = null;
        try {
            $response = $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($key, 60);
            throw $e;
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
