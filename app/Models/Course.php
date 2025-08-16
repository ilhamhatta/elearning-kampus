<?php

// app/Models/Course.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use SoftDeletes;
    // use HasFactory;

    protected $fillable = ['name', 'description', 'lecturer_id'];

    protected $casts = [
        'lecturer_id' => 'integer',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'course_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'course_id');
    }

    public function students()
    {
        // pastikan tabel pivot: course_student(course_id, student_id)
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withTimestamps(); // kalau pivot punya timestamps
    }

    // app/Models/Course.php
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withTimestamps()
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at'); // hanya peserta aktif
    }
}
