<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array and JSON forms.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_url',
        'track_name',
        'section_name',
        'gender_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'gender'            => 'integer',
            'grade_level'       => 'integer',
            'track'             => 'integer',
            'section'           => 'integer',
        ];
    }

    /* ================= RELATIONSHIPS ================= */

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function nfcCard()
    {
        return $this->hasOne(NfcCard::class, 'user_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'user_id');
    }

    /* ================= ACCESSORS & MUTATORS ================= */

    /**
     * Get full public URL for the user's uploaded photo.
     * Usage in Blade/JSON: $user->photo_url
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo && Storage::disk('public')->exists($this->photo)) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }

    /**
     * Resolve numeric track into readable string.
     * 1 = Academic Track, 2 = Technical-Professional
     */
    public function getTrackNameAttribute(): string
    {
        return match ((int)($this->track ?? $this->strand ?? 0)) {
            1 => 'Academic Track',
            2 => 'Technical-Professional',
            default => 'General Track',
        };
    }

    /**
     * Resolve numeric section into readable string.
     * 1 = Amber, 2 = Crystal, 3 = Pearl, 4 = Turquoise
     */
    public function getSectionNameAttribute(): ?string
    {
        return match ((int)($this->section ?? 0)) {
            1 => 'Amber',
            2 => 'Crystal',
            3 => 'Pearl',
            4 => 'Turquoise',
            default => $this->section ? 'Section ' . $this->section : null,
        };
    }

    /**
     * Resolve numeric gender into readable string.
     * 1 = Male, 2 = Female
     */
    public function getGenderNameAttribute(): string
    {
        return match ((int)($this->gender ?? 0)) {
            1 => 'Male',
            2 => 'Female',
            default => 'Not Specified',
        };
    }

    /**
     * Get formatted full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}