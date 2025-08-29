<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JWTTokenPhotographer
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $photographer = auth('photographer')->user();
            if (empty($photographer) || ($photographer && $photographer->status == 0)) {
                $responseData = 'Invalid token';
                $data = encryptResponse(config('custom.encrypt_decrypt_key_app'), config('custom.encrypt_decrypt_key_app_iv_app'), $responseData);

                return response()->json($data, 401);
            }
            $token = JWTAuth::getToken();

            if ($token == null && empty($token)) {
                $message = 'Add Token';
            } else {
                JWTAuth::checkOrFail($token);
                return $next($request);
            }
        } catch (TokenBlacklistedException $e) {
            $message = 'Invalid token';
        } catch (TokenExpiredException $e) {
            $message = 'Token expired';
        } catch (JWTException $e) {
            $message = 'Unauthorized';
        }

        $response = $next($request);
        $responseData = ['message' => $message];
        $response->setData($responseData);

        $data = encryptResponse(config('custom.encrypt_decrypt_key_app'), config('custom.encrypt_decrypt_key_app_iv_app'), $response->getContent());
        $response->setContent($data);

        return response()->json($data, 401);

    }

}
