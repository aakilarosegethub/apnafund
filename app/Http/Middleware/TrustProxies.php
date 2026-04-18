<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * Trusts forwarded headers (e.g. behind Cloudflare/load balancers) so client IP and scheme are correct.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     * Use '*' to trust all proxies (Cloudflare, load balancer) so request()->ip() returns real visitor IP.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
