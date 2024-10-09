<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Kiểm tra xem người dùng có quyền là developer hoặc employer hay không
        if ($user->account_type === 2) {
            return $next($request);
        }

        // Nếu người dùng không có quyền, bạn có thể chuyển hướng hoặc trả về một lỗi tùy ý
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
