<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra đã đăng nhập bằng guard admin chưa
        if (!auth()->guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Bạn cần đăng nhập với tài khoản Admin');
        }

        return $next($request);
    }
}
