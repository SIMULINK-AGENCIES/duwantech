<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http; // Add this import
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20', 'unique:'.User::class], // Add phone validation
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'website' => ['max:0'], // Honeypot: must be empty
        ], [
            'website.max' => 'Bot detected.'
        ]);

        // Domain blocklist for email and phone
        $blockedDomains = ['mailinator.com', 'tempmail.com', '10minutemail.com', 'guerrillamail.com'];
        if ($request->filled('email')) {
            $emailDomain = strtolower(substr(strrchr($request->email, '@'), 1));
            if (in_array($emailDomain, $blockedDomains)) {
                return back()->withErrors(['email' => 'Registration using this email domain is not allowed.']);
            }
        }
        if ($request->filled('phone')) {
            $blockedPhonePrefixes = ['+4470', '+4487', '+4857']; // Example: block some known virtual numbers
            foreach ($blockedPhonePrefixes as $prefix) {
                if (str_starts_with($request->phone, $prefix)) {
                    return back()->withErrors(['phone' => 'Registration using this phone number is not allowed.']);
                }
            }
        }

        // Validate Google reCAPTCHA v3
        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if ($recaptchaSecret && $recaptchaResponse) {
            $recaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);
            if (!($recaptcha->json('success') && $recaptcha->json('score', 0) > 0.5)) {
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed.']);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, // Store phone
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
