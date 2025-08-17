<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    use SoftDeletes;

    public const DELETED_AT = 'deleted_at';
    /**
     *
     */
    protected $fillable = [
        'course_id',
        'uploader_id',
        'title',
        'original_name',
        'file_path',
        'mime_type',
        'size_bytes',
    ];

    protected $hidden = ['file_path'];
    protected $dates = ['deleted_at'];

    /**
     *
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id')->withTrashed();
    }
    protected static function booted()
    {
        static::forceDeleted(function (Material $m) {
            if ($m->file_path) {
                Storage::disk('materials')->delete($m->file_path);
            }
        });
    }
}
