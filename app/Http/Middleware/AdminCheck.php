<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if ($user && $user->status == '1' && Auth::guard('admin')->check()) {
            return $next($request);
        }

        if ($user && $user->status == '0') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login');
        }

        return redirect()->route('admin.login');
    }

}
