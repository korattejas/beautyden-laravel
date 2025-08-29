<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request method is POST or PUT (or any other method you want to target)
        if ($request->isMethod('post') || $request->isMethod('put')) {
            $requestData = $request->all();
            // Sanitize the input data recursively
            array_walk_recursive($requestData, function (&$value, $key) {
                $value = $this->sanitize($value);
            });

            $request->replace($requestData);
        }

        return $next($request);
    }

    private function sanitize($data)
    {
        if (!empty($data)) {
            return strip_tags($data);
        }
        // If the data is empty, just return it as is
        return $data;
    }
}
