<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip security headers for captcha image generation (needs to work properly)
        if ($request->is('captcha*')) {
            // Only set minimal headers for captcha
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
            return $response;
        }

        // X-Frame-Options: Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        
        // X-Content-Type-Options: Prevent MIME type sniffing
        // Note: Don't set nosniff for image responses as it can interfere with image display
        if (!$response->headers->has('Content-Type') || !str_contains($response->headers->get('Content-Type'), 'image')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        }
        
        // X-XSS-Protection: Enable XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block', false);
        
        // Referrer-Policy: Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        
        // Permissions-Policy: Restrict browser features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()', false);
        
        // Strict-Transport-Security: Force HTTPS (only in production)
        if (config('app.env') === 'production' && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload', false);
        }
        
        // Content-Security-Policy: Prevent XSS attacks
        // Allow captcha images in CSP - use wildcard for local development
        $imgSrc = config('app.env') === 'local' 
            ? "'self' data: https: " . config('app.url') . "/captcha* " . config('app.url') . "/captcha/*" 
            : "'self' data: https:";
        
        // Build CSP with all necessary external resources
        // style-src: Allow Google Fonts CSS, CDN stylesheets, and inline styles
        $styleSrc = "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        
        // font-src: Allow Google Fonts files and CDN fonts
        $fontSrc = "'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com";
        
        // script-src: Allow necessary JavaScript sources
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com";
        
        // connect-src: Allow API connections
        $connectSrc = "'self' " . config('app.url');
        
        $csp = "default-src 'self'; script-src {$scriptSrc}; style-src {$styleSrc}; img-src {$imgSrc}; font-src {$fontSrc}; connect-src {$connectSrc}; frame-ancestors 'self';";
        $response->headers->set('Content-Security-Policy', $csp, false);
        
        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}

