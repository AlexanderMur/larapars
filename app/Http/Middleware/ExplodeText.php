<?php

namespace App\Http\Middleware;

use Closure;

class ExplodeText
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $field
     * @return mixed
     */
    public function handle($request, Closure $next,$field)
    {
        $request->request->set($field,preg_split('/\r\n/', $request->get($field)));
        return $next($request);
    }
}
