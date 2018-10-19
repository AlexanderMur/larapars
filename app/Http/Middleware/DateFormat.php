<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class DateFormat
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
        if (($request->method() == 'POST') || ($request->method() == 'PATCH') || ($request->method() == 'PUT')) {
            $fields = ['deleted_at', 'trashed_at'];

            foreach ($fields as $field) {
                $date = $request->get($field);

                if (empty($date)) {
                    continue;
                }

                $new_date = Carbon::parse($date);

                $request->request->set($field, $new_date);
            }
        }

        return $next($request);
    }
}
