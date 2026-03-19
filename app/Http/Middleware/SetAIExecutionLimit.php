<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAIExecutionLimit
{
    public function handle(Request $request, Closure $next, int $seconds = 300)
    {
        set_time_limit($seconds);
        ini_set('max_execution_time', $seconds);

        return $next($request);
    }
}
