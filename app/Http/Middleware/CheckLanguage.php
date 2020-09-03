<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\App;

class CheckLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->headers->get('X-lang');

        if (in_array($locale, ['en', 'es', 'pt'])) {
            App::setLocale($locale);
        }
    
        return $next($request); 
    }
}
