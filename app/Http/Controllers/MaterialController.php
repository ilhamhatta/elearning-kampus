<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // Upload Materi (Hanya Dosen)
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:20480' // Max 20MB
        ]);

        $course = Course::findOrFail($request->course_id);

        // Hanya dosen yang bisa mengupload materi ke mata kuliah yang dia ajar
        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Simpan file ke storage private
        $path = $request->file('file')->store('materials');

        $material = Material::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'file_path' => $path
        ]);

        return response()->json($material, 201);
    }

    // Download Materi (Mahasiswa & Dosen)
    public function download($id)
    {
        $material = Material::findOrFail($id);

        // Pastikan file ada di storage
        if (!Storage::exists($material->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($material->file_path);
    }
}
