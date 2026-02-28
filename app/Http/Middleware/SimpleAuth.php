<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * Simple auth check for UI demo - check if user session exists and not expired (1 year)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }

        // Check session expiry (1 year)
        $expiresAt = session()->get('expires_at');

        if ($expiresAt) {
            try {
                $expiryDate = Carbon::parse($expiresAt);

                if ($expiryDate->isPast()) {
                    // Session expired, clear it
                    session()->forget('user');
                    session()->forget('logged_in_at');
                    session()->forget('expires_at');

                    return redirect()->route('login')
                        ->with('error', __('messages.session_expired', ['Your session has expired. Please login again.']));
                }
            } catch (\Exception $e) {
                // Invalid expiry date, treat as expired
                session()->forget('user');
                session()->forget('logged_in_at');
                session()->forget('expires_at');

                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
