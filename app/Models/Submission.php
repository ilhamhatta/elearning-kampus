<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    /**
     *
     */
    protected $fillable = ['assignment_id', 'student_id', 'file_path', 'score'];

    protected $dates = ['deleted_at'];

    /**
     *
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    // Relasi: Setiap Submission dimiliki oleh satu Mahasiswa
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
