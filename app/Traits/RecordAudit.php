<?php

namespace App\Traits;

images: [];
use App\Models\AuditLog;
use Illuminate\Http\Request;

trait RecordAudit
{
    public function logActivity(
        string $action, 
        string $module, 
        string $status = 'Success', 
        ?string $description = null
    ) {
        $user = auth()->user();
        $request = request();

        // Determine friendly role name
        $roleName = match($user?->role_id) {
            1 => 'Admin',
            2 => 'Faculty',
            3 => 'Student',
            4 => 'Super Admin',
            default => 'System'
        };

        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user ? trim($user->first_name . ' ' . $user->last_name) : 'Guest',
            'role' => $roleName,
            'action' => $action,
            'module' => $module,
            'status' => $status,
            'description' => $description,
            'ip_address' => $request->ip(),
            'device' => $request->header('Sec-Ch-Ua-Platform') ?? 'Unknown OS',
            'browser' => $request->userAgent() ?? 'Unknown Browser',
        ]);
    }
}