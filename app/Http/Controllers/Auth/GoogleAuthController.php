<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Existing account (maybe created via password originally) —
            // link the google_id if it wasn't set yet.
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'username' => $this->generateUniqueUsername($googleUser->getName()),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => null,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }

    private function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '');
        $username = $base;
        $i = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $i++;
        }

        return $username;
    }
}
