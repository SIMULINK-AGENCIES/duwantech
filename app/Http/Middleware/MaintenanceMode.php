<?php

namespace App\Http\Middleware;

use App\Models\GeneralSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip maintenance mode for admin routes and API routes
        if ($request->is('admin/*') || $request->is('api/*') || $request->is('login') || $request->is('register')) {
            return $next($request);
        }

        // Skip for authenticated admin users
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }

        try {
            $maintenanceMode = GeneralSetting::get('maintenance_mode', false);
            
            if ($maintenanceMode) {
                // You can customize this view or return a JSON response for APIs
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Site is currently under maintenance. Please try again later.',
                        'maintenance_mode' => true
                    ], 503);
                }

                // Return maintenance page view
                return response()->view('maintenance', [
                    'siteName' => GeneralSetting::get('site_name', 'Website'),
                    'contactEmail' => GeneralSetting::get('contact_email', ''),
                ], 503);
            }
        } catch (\Exception $e) {
            // If there's an error accessing settings (e.g., during migration), 
            // continue normally to avoid breaking the site
            \Log::warning('Failed to check maintenance mode: ' . $e->getMessage());
        }

        return $next($request);
    }
}
