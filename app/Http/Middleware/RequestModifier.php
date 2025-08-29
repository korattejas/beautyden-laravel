<?php

namespace App\Http\Middleware;

use Closure;
use Helpers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestModifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestData = $request->all();
        // $data = $request->getContent();
        // $requestData = json_decode($data, true);
        if (isset($requestData['response'])) {
            $data = decryptResponse(config('custom.encrypt_decrypt_key_app'), $requestData['response']);
            $finalData = json_decode($data, true);
            if (!is_array($finalData)) {
                $finalData = [];
            }
            $request->replace($finalData);
        }

        return $next($request);
    }
}
