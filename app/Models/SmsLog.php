<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'attendance_log_id',
        'recipient_phone',
        'message_body',
        'status',
        'gateway_response',
        'sent_at',
    ];

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class);
    }
}