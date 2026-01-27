<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminUser = config('auth.admin.user');
        $adminPasswordHash = config('auth.admin.password_hash');

        if (!$adminUser || !$adminPasswordHash) {
            return response('Admin not configured.', 500);
        }

        if ($request->getUser() !== $adminUser || !Hash::check($request->getPassword(), $adminPasswordHash)) {
            return response('Unauthorized.', 401, ['WWW-Authenticate' => 'Basic realm="Admin"']);
        }

        return $next($request);
    }
}
