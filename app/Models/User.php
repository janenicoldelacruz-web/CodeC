<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
    'id_number',
    'first_name',
    'last_name',
    'email',
    'role_id',
    'nfc_tag_id',
    'phone_number',
    'parent_phone_number',
    'grade_level',
    'strand',
    'password',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /*
    |--------------------------------------------------------------------------
    | NFC Card
    |--------------------------------------------------------------------------
    */

    public function nfcCard()
    {
        return $this->hasOne(NfcCard::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Academic Sections
    |--------------------------------------------------------------------------
    */

    public function sections()
    {
        return $this->belongsToMany(
            AcademicSection::class,
            'section_student',
            'student_id',
            'section_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */

    public function attendanceLogs()
    {
        return $this->hasMany(
            AttendanceLog::class,
            'student_id'
        );
    }
}