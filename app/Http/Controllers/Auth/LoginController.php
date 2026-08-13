<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limit brute-force: dikunci per kombinasi email + IP, hanya
        // menghitung percobaan yang GAGAL (login sukses langsung reset).
        $throttleKey = Str::transliterate(Str::lower($credentials['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);

        $user = Auth::user();

        if ($this->isInWilayahNonaktif($user)) {
            Auth::logout();
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Akun ini berada di wilayah yang sedang dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        return $this->authenticated($request, $user)
            ?: redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Cek apakah user terikat pada kecamatan/kelurahan yang sedang dinonaktifkan.
     * super_admin dan admin_kabupaten selalu lolos karena tidak terikat 1 kecamatan.
     */
    protected function isInWilayahNonaktif($user): bool
    {
        if ($user->hasRole('admin_kecamatan')) {
            return $user->kecamatan && !$user->kecamatan->is_active;
        }

        if ($user->hasRole('admin_kelurahan')) {
            return $user->kelurahan && (!$user->kelurahan->is_active || !$user->kelurahan->kecamatan?->is_active);
        }

        return false;
    }

    /**
     * Validasi role user setelah login
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasRole('super_admin')) {
            return redirect()->route('dashboard.super_admin');
        } elseif ($user->hasRole('admin_kecamatan')) {
            return redirect()->route('dashboard.kecamatan');
        } elseif ($user->hasRole('admin_kelurahan')) {
            return redirect()->route('dashboard.kelurahan');
        } elseif ($user->hasRole('admin_kabupaten')) {
            return redirect()->route('dashboard.kabupaten');
        }

        // Default redirect if no specific role matched (should not happen based on seeders)
        return redirect()->route('dashboard');
    }
}
