<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        // Simpan referral code ke session sebelum redirect ke Google
        if (request()->filled('ref')) {
            session(['referral_code' => request('ref')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // User exists with Google ID, login
                Auth::login($user);
                return redirect()->intended(route('dashboard'))->with('success', 'Berhasil login dengan Google!');
            }
            
            // Check if user exists with this email
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // User exists with email, link Google account
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'provider' => 'google',
                    'email_verified_at' => now(), // Auto verify email
                ]);
                
                Auth::login($user);
                return redirect()->intended(route('dashboard'))->with('success', 'Akun Google berhasil dihubungkan!');
            }
            
            // Create new user
            $user = User::create([
                'name'              => $googleUser->name,
                'email'             => $googleUser->email,
                'google_id'         => $googleUser->id,
                'avatar'            => $googleUser->avatar,
                'provider'          => 'google',
                'password'          => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
                'role'              => 'client',
            ]);

            // Generate referral code
            app(\App\Services\ReferralService::class)->generateCode($user);

            // Track referral if ?ref= param present in session
            $refCode = session('referral_code');
            if ($refCode) {
                app(\App\Services\ReferralService::class)->trackSignup($user, $refCode);
                session()->forget('referral_code');
            }

            // Auto-assign free package (active, bukan trial)
            $freePackage = \App\Models\Package::where('price', 0)->where('is_active', true)->first();
            if ($freePackage) {
                \App\Models\UserSubscription::activateFree($user, $freePackage);
            }

            Auth::login($user);

            return redirect()->intended(route('dashboard'))->with('success', '🎉 Akun berhasil dibuat! Selamat datang di Pintar Menulis.');
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
