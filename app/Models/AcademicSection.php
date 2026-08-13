<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'section_name',
    ];

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'section_student',
            'section_id',
            'student_id'
        );
    }
}