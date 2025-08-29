<?php

namespace App\Http\Middleware;

use Closure;
use Helpers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseModifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $data = encryptResponse(config('custom.encrypt_decrypt_key_app'), config('custom.encrypt_decrypt_key_app_iv_app'), $response->getContent());
        $response->setContent($data);

        return $response;
    }

}
