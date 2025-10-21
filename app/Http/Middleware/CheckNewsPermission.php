<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNewsPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = 'manage'): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        // Check if user has required role for news management
        $allowedRoles = [];
        
        switch ($permission) {
            case 'manage':
                // Can create, update, delete news
                $allowedRoles = ['Administrador', 'Editor'];
                break;
            case 'admin':
                // Admin-only operations (toggle publish, get all news, send notifications)
                $allowedRoles = ['Administrador'];
                break;
            case 'publish':
                // Can publish/unpublish news
                $allowedRoles = ['Administrador', 'Editor'];
                break;
            default:
                $allowedRoles = ['Administrador'];
        }

        $userRoles = $user->roles->pluck('name')->toArray();
        $hasPermission = !empty(array_intersect($userRoles, $allowedRoles));

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.',
                'required_roles' => $allowedRoles,
                'user_roles' => $userRoles
            ], 403);
        }

        return $next($request);
    }
}
