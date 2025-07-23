<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PhoneVerificationController extends Controller
{
    // Show the phone verification form
    public function show()
    {
        return view('auth.verify-phone');
    }

    // Send OTP to the user's phone
    public function send(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->phone) {
            return redirect()->route('dashboard')->withErrors(['phone' => 'No phone number found.']);
        }
        $otp = rand(100000, 999999);
        $user->phone_otp = $otp;
        $user->save();
        // Here you would integrate with an SMS provider
        Log::info('OTP for '.$user->phone.': '.$otp); // For development only
        Session::flash('status', 'OTP sent to your phone.');
        return redirect()->route('phone.verify.form');
    }

    // Verify the OTP
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);
        $user = Auth::user();
        if ($user && $user->phone_otp === $request->otp) {
            $user->phone_verified_at = now();
            $user->phone_otp = null;
            $user->save();
            return redirect()->route('dashboard')->with('status', 'Phone verified!');
        }
        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }
} 