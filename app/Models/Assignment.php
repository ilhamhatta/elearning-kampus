<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    /**
     *
     */
    protected $fillable = ['course_id', 'title', 'description', 'deadline'];

    protected $dates = ['deleted_at'];

    /**
     *
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'assignment_id');
    }
}
