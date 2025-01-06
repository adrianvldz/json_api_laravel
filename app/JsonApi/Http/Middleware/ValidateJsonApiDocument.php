<?php

namespace App\JsonApi\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonApiDocument
{
    public function handle(Request $request, Closure $next): Response
    {

        // Excluir la ruta de login
        if ($request->routeIs('api.v1.login')) {
            return $next($request);
        }
        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            $request->validate([
                'data' => ['required', 'array'],
                'data.type' => [
                    'required_without:data.0.type',
                     'string'
                    ],
                'data.attributes' => [
                    Rule::requiredIf(
                        ! Str::of(request()->url())->contains('relationships')
                        && request()->isNotFilled('data.0.type')
                    ),
                    'array',
                ],
            ]);
        }

        if ($request->isMethod('PATCH')) {
            $request->validate([
                'data.id' => [
                    'required_without:data.0.id',
                     'string'],
            ]);
        }

        return $next($request);
    }
}
