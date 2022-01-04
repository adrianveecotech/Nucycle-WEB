<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HubAdmin_Reader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user() && (in_array(5, $request->user()->users_roles_id()) && in_array(4, $request->user()->users_roles_id())))
        {
            abort('404');
        }
        return $next($request);
    }
}
