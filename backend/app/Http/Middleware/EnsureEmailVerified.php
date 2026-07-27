<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->email_verifie) {

            return response()->json([
                'message' => 'Veuillez vérifier votre adresse email.'
            ],403);

        }

        return $next($request);
    } 
}