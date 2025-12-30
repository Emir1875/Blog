<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Memproses login user
     * Validasi kredensial dan redirect ke dashboard sesuai role
     * Jika gagal, tampilkan pesan error untuk email dan password
     */
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }

            if ($user->isPelanggan()) {
                return redirect()->route('pelanggan.dashboard');
            }
        } else {
            return redirect()->back()->withErrors([
                'email' => 'Email tidak ditemukan',
                'password' => 'Password salah',
            ]);
        }
    }

    /**
     * Logout user dan redirect ke halaman login
     */
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
