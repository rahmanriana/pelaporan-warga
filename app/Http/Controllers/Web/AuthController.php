<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($data['email']);
        $password = (string) $data['password'];

        $loggedIn = Auth::attempt([
            'email' => $identifier,
            'password' => $password,
        ]);

        if (!$loggedIn) {
            $user = User::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($identifier)])
                ->first();

            if (!$user || !Hash::check($password, (string) $user->password)) {
                return back()->withErrors(['email' => 'Email/username atau password salah'])->onlyInput('email');
            }

            Auth::login($user);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $isAdmin = ($user->role ?? null) === 'admin';

        return redirect()->intended($isAdmin ? route('admin.panel') : route('home'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'warga',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Registrasi berhasil.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Logout berhasil.');
    }
}
