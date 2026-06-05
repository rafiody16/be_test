<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        return $driver->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            $emailGoogle = $driver->stateless()->user();

            $user = User::query()->where('email', $emailGoogle->getEmail())->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Akun dengan email ini belum terdaftar.',
                ], 404);
            }

            if (empty($user->provider_id)) {
                $user->update([
                    'provider_id' => $emailGoogle->getId(),
                ]);
            }

            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login dengan Google gagal.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
