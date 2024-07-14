<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $title = 'Login';
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login', compact('title'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        try {
            if (auth()->attempt($request->only('username', 'password'))) {
                return redirect()->route('dashboard')->with('login_success', 'Login Berhasil');
            } else {
                return back()->with('error', 'Login Gagal');
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function ubahPassword()
    {
        $title = 'Ubah Password';

        return view('auth.ubah-password', compact('title'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6',
        ]);
        try {
            $password_old = $request->password_lama;
            $password_new = $request->password_baru;
            $db_user = User::where('id', auth()->user()->id)->first();
            if (Hash::check($password_old, $db_user->password)) {
                User::where('id', auth()->user()->id)->update([
                    'password' => bcrypt($password_new),
                ]);
                return redirect()->route('dashboard')->with('success', 'Password berhasil diubah');
            } else {
                return back()->with('error', 'Password lama tidak sesuai');
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
