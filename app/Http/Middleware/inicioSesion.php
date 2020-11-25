<?php

namespace App\Http\Middleware;

use Closure;

class inicioSesion
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
        if ($request->email === 'amauryrdzrz@gmail.com') {
            $request->role = 'admin:admin';
        } else {
            $request->role = 'user:info';
        }        
        return $next($request);
    }
}
