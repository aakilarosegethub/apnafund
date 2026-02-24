<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeRequestPath
{
    /**
     * Collapse multiple slashes in the request path so //api/... matches /api/...
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->getRequestUri();
        $normalized = preg_replace('#/+#', '/', $uri);
        if ($normalized !== $uri) {
            $request->server->set('REQUEST_URI', $normalized);
        }

        return $next($request);
    }
}
