<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => $user,
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $identifier = trim((string) $request->input('email'));
        $password = (string) $request->input('password');

        $token = auth('api')->attempt([
            'email' => $identifier,
            'password' => $password,
        ]);

        if (!$token && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($identifier)])
                ->first();

            if ($user && Hash::check($password, (string) $user->password)) {
                $token = auth('api')->login($user);
            }
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Email/username atau password salah',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
            'data' => null,
        ]);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();

        return $this->respondWithToken($token);
    }

    protected function respondWithToken(string $token)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => auth('api')->user(),
            ],
        ]);
    }
}
