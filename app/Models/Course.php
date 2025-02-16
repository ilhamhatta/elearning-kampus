<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    /**
     *
     */
    protected $fillable = ['name', 'description', 'lecturer_id'];

    protected $dates = ['deleted_at'];

    /**
     *
     */
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
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }
}
