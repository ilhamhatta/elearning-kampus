<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    public const DELETED_AT = 'deleted_at';

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

    /**
     * Semua peserta (termasuk yang sudah di-soft-delete di pivot).
     * Pivot: course_student(course_id, student_id, timestamps, deleted_at[nullable])
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withTimestamps()
            ->withPivot('deleted_at');
    }

    /**
     * Hanya peserta aktif (pivot.deleted_at IS NULL)
     */
    public function activeStudents()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
            ->withTimestamps()
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at');
    }
}
