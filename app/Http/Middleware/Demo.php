<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            $toast[] = ['warning', 'You can not change anything over this demo'];
            $toast[] = ['info', 'This version is for demonstration purposes only and few actions are blocked'];

            return back()->withToasts($toast);
        }

        return $next($request);
    }
}
