<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    /**
     *
     */
    protected $fillable = ['course_id', 'title', 'file_path'];

    protected $dates = ['deleted_at'];

    /**
     *
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
