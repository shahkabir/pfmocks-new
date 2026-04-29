<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EvaluatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Access denied.');
        }

        $role = auth()->user()->role;
        if ($role !== 'evaluator' && $role !== 'admin') {
            abort(403, 'Access denied. Evaluator only.');
        }

        return $next($request);
    }
}
