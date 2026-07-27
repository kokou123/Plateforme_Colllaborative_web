<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEntrepriseActive
{
    public function handle(Request $request, Closure $next)
    {

        if (!$request->user()->entreprise->active) {

            return response()->json([
                'message'=>'Entreprise désactivée.'
            ],403);

        }

        return $next($request);

    }
}