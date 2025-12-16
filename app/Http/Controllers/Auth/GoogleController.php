<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Redirect user to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Đăng nhập Google thất bại!');
        }

        // Tìm user theo email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Nếu chưa có → tạo mới
        if (!$user) {
            $user = User::create([
                'name'  => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(16)),  // random password
                'gender' => 'other', // hoặc null
            ]);
        }

        // Login user đó
        Auth::login($user);

        return redirect('/'); // chuyển về trang chủ hoặc dashboard
    }
}
