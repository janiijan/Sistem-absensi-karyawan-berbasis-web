<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('absensi.index');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_karyawan' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'id_karyawan.required' => 'ID karyawan wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $karyawan = Karyawan::query()
            ->where('id_karyawan', $validated['id_karyawan'])
            ->first();

        if ($karyawan && $karyawan->isLocked()) {
            return back()
                ->withInput($request->only('id_karyawan'))
                ->withErrors([
                    'id_karyawan' => 'Akun terkunci sementara karena terlalu banyak percobaan gagal. Coba lagi nanti.',
                ]);
        }

        if (! $karyawan || ! Hash::check($validated['password'], $karyawan->password)) {
            if ($karyawan) {
                $attempts = $karyawan->failed_login_attempts + 1;
                $payload = ['failed_login_attempts' => $attempts];

                if ($attempts >= 5) {
                    $payload['locked_until'] = now()->addMinutes(15);
                    $payload['failed_login_attempts'] = 0;
                }

                $karyawan->update($payload);
            }

            return back()
                ->withInput($request->only('id_karyawan'))
                ->withErrors([
                    'id_karyawan' => 'ID karyawan atau password salah.',
                ]);
        }

        $karyawan->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        Auth::login($karyawan, false);
        $request->session()->regenerate();

        return redirect()->intended(route('absensi.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
