<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
                return route('login');
            
            //$response = ['success' => false, 'data' => '', 'message' => 'No allowed'];
            //return response()->json($response, 404);
            
            //return response()->json([
             //   'message' => 'Not a valid API request.',
            //]);
        }
    }
}
