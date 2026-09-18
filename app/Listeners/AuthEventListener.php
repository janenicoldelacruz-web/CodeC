<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class AuthEventListener
{
    public function handleLogin(Login $event): void
    {
        $user = $event->user;
        $request = request();

        $roleName = match((int)$user->role_id) {
            1 => 'Admin',
            2 => 'Faculty',
            3 => 'Student',
            4 => 'Super Admin',
            default => 'User'
        };

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => trim($user->first_name . ' ' . $user->last_name),
            'role' => $roleName,
            'action' => 'Login',
            'module' => 'Authentication',
            'status' => 'Success',
            'description' => 'User logged into the SIATRACK system successfully.',
            'ip_address' => $request->ip(),
            'device' => $request->header('Sec-Ch-Ua-Platform') ?? 'Windows',
            'browser' => $request->userAgent() ?? 'Browser',
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        $user = $event->user;
        if (!$user) return;
        $request = request();

        $roleName = match((int)$user->role_id) {
            1 => 'Admin',
            2 => 'Faculty',
            3 => 'Student',
            4 => 'Super Admin',
            default => 'User'
        };

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => trim($user->first_name . ' ' . $user->last_name),
            'role' => $roleName,
            'action' => 'Logout',
            'module' => 'Authentication',
            'status' => 'Success',
            'description' => 'User logged out of the system.',
            'ip_address' => $request->ip(),
            'device' => $request->header('Sec-Ch-Ua-Platform') ?? 'Windows',
            'browser' => $request->userAgent() ?? 'Browser',
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        $request = request();
        $credentials = $event->credentials;

        AuditLog::create([
            'user_id' => null,
            'user_name' => $credentials['email'] ?? 'Unknown User',
            'role' => 'Guest',
            'action' => 'Failed Login',
            'module' => 'Authentication',
            'status' => 'Failed',
            'description' => 'Failed login attempt using email/credentials.',
            'ip_address' => $request->ip(),
            'device' => $request->header('Sec-Ch-Ua-Platform') ?? 'Windows',
            'browser' => $request->userAgent() ?? 'Browser',
        ]);
    }

    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }
}