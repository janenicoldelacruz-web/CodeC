<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 1. Safely extract numeric role_id
        $roleId = null;
        if (isset($user->role_id) && is_numeric($user->role_id)) {
            $roleId = (int) $user->role_id;
        } elseif (isset($user->role) && is_object($user->role) && isset($user->role->id)) {
            $roleId = (int) $user->role->id;
        } elseif (is_numeric($user->role ?? null)) {
            $roleId = (int) $user->role;
        }

        // 2. Safely extract string role_name
        $roleName = '';
        if (isset($user->role)) {
            if (is_string($user->role)) {
                $roleName = strtolower(trim($user->role));
            } elseif (is_object($user->role) && isset($user->role->name)) {
                $roleName = strtolower(trim($user->role->name));
            }
        }

        $target = strtolower(trim($role));

        // Map allowed role IDs and aliases (IDINAGDAG NATIN YUNG 4 AT SUPERADMIN DITO)
        $targetMap = [
            'admin'   => [1, 4, 'admin', 'superadmin_viewer'], 
            'teacher' => [2, 'teacher', 'faculty'],
            'faculty' => [2, 'teacher', 'faculty'],
            'student' => [3, 'student'],
        ];

        $allowedItems = $targetMap[$target] ?? [$target];

        // 3. Authorize user by ID or Name
        if (
            ($roleId !== null && in_array($roleId, array_filter($allowedItems, 'is_int'), true)) ||
            ($roleName !== '' && in_array($roleName, array_filter($allowedItems, 'is_string'), true))
        ) {
            return $next($request);
        }

        // 4. Redirect unauthorized access to respective dashboard (ISINAMA RIN NATIN YUNG 4 DITO)
        return match ($roleId) {
            1, 4 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('teacher.schedules'),
            3 => redirect()->route('student.dashboard'),
            default => redirect()->route('login'),
        };
    }
}