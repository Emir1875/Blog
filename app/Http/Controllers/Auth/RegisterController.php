<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman registrasi
     */
    public function index()
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi user baru
     * Validasi input dengan pesan error dalam bahasa Indonesia
     * User baru otomatis mendapat role 'pelanggan'
     * Redirect ke halaman login setelah berhasil registrasi
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ], [
            'email.unique' => 'Email sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
            'name.required' => 'Nama wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $register = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Register successfully');
    }
}
