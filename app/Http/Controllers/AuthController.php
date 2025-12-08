<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'province' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        $emailVerification = EmailVerification::generate($user->email);
        $this->sendOTPEmail($user->email, $emailVerification->otp);

        return redirect()->route('verify.email')->with('email', $user->email);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user()->fresh();
            
            // Refresh user in session to ensure latest data (including avatar) is loaded
            Auth::setUser($user);
            
            // Validasi email: user harus verifikasi email dulu sebelum bisa login
            if (!$user->email_verified_at) {
                Auth::logout();
                
                // Generate dan kirim OTP otomatis
                $emailVerification = EmailVerification::generate($request->email);
                $this->sendOTPEmail($request->email, $emailVerification->otp);
                
                return redirect()->route('verify.email')
                    ->with('email', $request->email)
                    ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showVerifyEmail()
    {
        if (Auth::check()) {
            $user = Auth::user()->fresh();
            
            if ($user && $user->email_verified_at) {
                return redirect()->route('home');
            }
            
            if ($user && !session('email')) {
                session(['email' => $user->email]);
            }
        }
        
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $emailVerification = EmailVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$emailVerification || !$emailVerification->isValid()) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->email_verified_at = now();
        $user->save();
        $user = $user->fresh();
        
        $emailVerification->markAsUsed();

        Auth::login($user);
        $request->session()->regenerate();
        
        // Ensure user data in session is up-to-date
        Auth::setUser($user);

        return redirect()->route('home')->with('success', 'Email berhasil diverifikasi!');
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $emailVerification = EmailVerification::generate($request->email);
        $this->sendOTPEmail($request->email, $emailVerification->otp);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
    }

    private function sendOTPEmail(string $email, string $otp): void
    {
        try {
            Mail::to($email)->send(new OTPMail($otp));
            \Log::info("OTP email sent to: {$email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
        }
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            // Start session if not started
            if (!request()->hasSession()) {
                request()->session()->start();
            }
            
            // Regenerate session ID for security
            request()->session()->regenerate();
            
            // Save session explicitly before redirect
            request()->session()->save();
            
            \Log::info('Redirecting to Google OAuth', [
                'session_id' => request()->session()->getId(),
            ]);
            
            return Socialite::driver('google')
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Error redirecting to Google: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal mengarahkan ke Google: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle Google OAuth callback
     * 
     * Alur:
     * 1. Cek apakah user sudah punya google_id (sudah pernah login dengan Google)
     * 2. Jika belum, cek apakah email sudah terdaftar (link Google ke akun existing)
     * 3. Jika email belum terdaftar, buat user baru
     * 4. Auto-verify email karena Google sudah verifikasi
     */
    public function handleGoogleCallback()
    {
        try {
            // Ensure session is started
            if (!request()->hasSession()) {
                request()->session()->start();
            }
            
            \Log::info('Google OAuth callback received', [
                'session_id' => request()->session()->getId(),
                'has_state' => request()->has('state'),
            ]);
            
            // Get user from Google OAuth
            // Note: Socialite will automatically validate the state parameter
            $googleUser = Socialite::driver('google')->user();
            $googleAvatar = $googleUser->getAvatar();
            
            \Log::info('Google OAuth avatar data', [
                'google_avatar' => $googleAvatar,
                'has_avatar' => !empty($googleAvatar),
            ]);

            // Cek user dengan google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User sudah pernah login dengan Google
                // JANGAN overwrite avatar jika user sudah punya avatar manual (dari Cloudinary)
                // Hanya update avatar dari Google jika user belum punya avatar
                \Log::info('Existing Google user found', [
                    'user_id' => $user->id,
                    'current_avatar' => $user->avatar,
                    'new_avatar' => $googleAvatar,
                    'has_custom_avatar' => $user->avatar && !str_contains($user->avatar, 'googleusercontent.com'),
                ]);
                
                // Hanya update avatar dari Google jika:
                // 1. User belum punya avatar, ATAU
                // 2. Avatar saat ini adalah avatar Google (bukan avatar manual dari Cloudinary)
                $isGoogleAvatar = $user->avatar && str_contains($user->avatar, 'googleusercontent.com');
                $hasCustomAvatar = $user->avatar && !$isGoogleAvatar;
                
                if ($googleAvatar && (!$hasCustomAvatar)) {
                    // Update avatar hanya jika belum ada avatar atau avatar saat ini adalah avatar Google
                    $user->avatar = $googleAvatar;
                    $user->save();
                    \Log::info('Avatar updated for existing user', [
                        'user_id' => $user->id,
                        'avatar' => $user->avatar,
                        'reason' => $hasCustomAvatar ? 'skipped_custom_avatar' : 'no_avatar_or_google_avatar',
                    ]);
                } else {
                    \Log::info('Avatar not updated - preserving custom avatar', [
                        'user_id' => $user->id,
                        'current_avatar' => $user->avatar,
                    ]);
                }
            } else {
                // Cek apakah email sudah terdaftar (untuk link Google ke akun existing)
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    // Email sudah terdaftar, link Google account
                    // Validasi: pastikan google_id tidak sudah dipakai user lain
                    $existingGoogleUser = User::where('google_id', $googleUser->getId())->first();
                    if ($existingGoogleUser && $existingGoogleUser->id !== $user->id) {
                        return redirect()->route('login')
                            ->withErrors(['email' => 'Akun Google ini sudah terhubung dengan email lain.']);
                    }

                    \Log::info('Linking Google account to existing user', [
                        'user_id' => $user->id,
                        'current_avatar' => $user->avatar,
                        'new_avatar' => $googleAvatar,
                        'has_custom_avatar' => $user->avatar && !str_contains($user->avatar, 'googleusercontent.com'),
                    ]);

                    $user->google_id = $googleUser->getId();
                    
                    // Hanya set avatar dari Google jika user belum punya avatar manual
                    $isGoogleAvatar = $user->avatar && str_contains($user->avatar, 'googleusercontent.com');
                    $hasCustomAvatar = $user->avatar && !$isGoogleAvatar;
                    
                    if ($googleAvatar && (!$hasCustomAvatar)) {
                        $user->avatar = $googleAvatar;
                    }
                    
                    // Auto-verify email karena Google sudah verifikasi
                    if (!$user->email_verified_at) {
                        $user->email_verified_at = now();
                    }
                    
                    $user->save();
                    \Log::info('Avatar updated for linked user', [
                        'user_id' => $user->id,
                        'avatar' => $user->avatar,
                        'reason' => $hasCustomAvatar ? 'skipped_custom_avatar' : 'no_avatar_or_google_avatar',
                    ]);
                } else {
                    // Email belum terdaftar, buat user baru
                    // Double check untuk race condition
                    $existingUser = User::where('email', $googleUser->getEmail())->first();
                    if ($existingUser) {
                        return redirect()->route('login')
                            ->withErrors(['email' => 'Email ini sudah terdaftar. Silakan login dengan email dan password, atau gunakan akun Google lain.']);
                    }

                    \Log::info('Creating new user from Google OAuth', [
                        'email' => $googleUser->getEmail(),
                        'avatar' => $googleAvatar,
                    ]);

                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleAvatar,
                        'email_verified_at' => now(), // Google email sudah verified
                        'password' => Hash::make(uniqid()), // Random password, user tidak akan pakai
                    ]);
                    
                    \Log::info('New user created', [
                        'user_id' => $user->id,
                        'avatar' => $user->avatar,
                    ]);
                }
            }

            // Refresh user to get latest data (including avatar)
            $user = $user->fresh();
            
            \Log::info('User data before login', [
                'user_id' => $user->id,
                'avatar' => $user->avatar,
                'has_avatar' => !empty($user->avatar),
            ]);
            
            Auth::login($user);
            request()->session()->regenerate();
            
            // Ensure user data in session is up-to-date
            Auth::setUser($user);
            
            \Log::info('User logged in', [
                'user_id' => Auth::id(),
                'session_avatar' => Auth::user()->avatar,
                'has_avatar' => !empty(Auth::user()->avatar),
            ]);

            return redirect()->intended(route('home'))->with('success', 'Berhasil masuk dengan Google!');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error("Google OAuth HTTP error: " . $e->getMessage());
            \Log::error("Response: " . $e->response?->body());
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal terhubung ke Google. Pastikan redirect URI sudah benar di Google Console.']);
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error("Google OAuth state error: " . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi OAuth tidak valid. Silakan coba lagi.']);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error("Google OAuth database error: " . $e->getMessage());
            if (str_contains($e->getMessage(), 'duplicate key') || str_contains($e->getMessage(), 'unique constraint')) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Email atau akun Google ini sudah terdaftar. Silakan login dengan email dan password.']);
            }
            return redirect()->route('login')
                ->withErrors(['email' => 'Terjadi kesalahan database. Silakan coba lagi.']);
        } catch (\Exception $e) {
            \Log::error("Google OAuth error: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal masuk dengan Google: ' . $e->getMessage()]);
        }
    }
}

