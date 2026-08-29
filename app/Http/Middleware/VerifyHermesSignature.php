<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyHermesSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.hermes.webhook_secret');
        $signature = $request->header('X-Hermes-Signature');
        $payload = $request->getContent();

        if (! $secret || ! $signature) {
            abort(401, 'Invalid Hermes signature.');
        }

        $expected = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            abort(401, 'Invalid Hermes signature.');
        }

        return $next($request);
    }
}
