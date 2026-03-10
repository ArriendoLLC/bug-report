<?php

namespace Arriendo\BugReport\Http\Middleware;

use Arriendo\BugReport\Exceptions\RateLimitExceededException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class BugReportRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxAttempts = config('bug-report.rate_limit.max_reports_per_hour', 10);
        $decayMinutes = 60;

        // Create rate limiter key based on user ID or IP
        $key = 'bug-report-submit:'.($request->user()?->id ?? $request->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new RateLimitExceededException();
        }

        // Increment the rate limiter
        RateLimiter::hit($key, $decayMinutes * 60);

        return $next($request);
    }
}
