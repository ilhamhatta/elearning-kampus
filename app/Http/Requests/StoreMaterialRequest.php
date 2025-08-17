<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    } // policy di controller

    public function rules(): array
    {
        $maxMb = (int) env('MATERIAL_MAX_UPLOAD_MB', 20);
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'title'     => ['required', 'string', 'max:255'],
            'file'      => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,mp4', 'max:' . ($maxMb * 1024)],
        ];
    }
}
