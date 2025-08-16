<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // LISTING: /materials?course_id=ID&q=keyword
    public function index(Request $request)
    {
        $q = Material::query()->with('course:id,name,lecturer_id');

        if ($request->filled('course_id')) {
            $q->where('course_id', (int) $request->query('course_id'));
        }

        if ($request->filled('q')) {
            $kw = $request->query('q');
            $q->where(function ($qq) use ($kw) {
                $qq->where('title', 'like', "%{$kw}%")
                    ->orWhere('file_path', 'like', "%{$kw}%");
            });
        }

        $rows = $q->latest()->get();

        // Bentuk respons diselaraskan dengan frontend (id, name, filename, size, uploaded_at)
        $materials = $rows->map(function (Material $m) {
            $filename = $m->file_path ? basename($m->file_path) : null;
            $size = null;
            if ($m->file_path && Storage::exists($m->file_path)) {
                $size = Storage::size($m->file_path);
            }
            return [
                'id'          => (int) $m->id,
                'name'        => $m->title,
                'filename'    => $filename,
                'size'        => $size,
                'uploaded_at' => optional($m->created_at)->toIso8601String(),
            ];
        });

        return response()->json(['materials' => $materials], 200);
    }

    // Upload Materi (Hanya Dosen)
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'file'      => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:20480', // 20MB
        ]);

        $course = Course::findOrFail($request->course_id);

        // Hanya dosen pengampu course yang boleh upload
        if ((int) Auth::id() !== (int) $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Simpan file (nama file disanitasi agar rapi & unik)
        $file = $request->file('file');
        $storedName = uniqid('m_') . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('materials', $storedName);

        $material = Material::create([
            'course_id' => $course->id,
            'title'     => $request->title,
            'file_path' => $path,
        ]);

        return response()->json([
            'id'          => (int) $material->id,
            'name'        => $material->title,
            'filename'    => basename($material->file_path),
            'size'        => Storage::exists($path) ? Storage::size($path) : null,
            'uploaded_at' => optional($material->created_at)->toIso8601String(),
        ], 201);
    }

    // Download Materi (Mahasiswa terdaftar & Dosen pengampu)
    public function download($id)
    {
        $material = Material::with('course:id,lecturer_id')->findOrFail($id);

        // Akses: dosen pengampu atau mahasiswa yang terdaftar di course (jika relasi students ada)
        $userId = (int) Auth::id();
        $canAccess = $userId === (int) $material->course->lecturer_id;

        if (method_exists($material->course, 'students') && !$canAccess) {
            $canAccess = $material->course
                ->students()
                ->where('users.id', $userId)
                ->exists();
        }

        if (!$canAccess) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!Storage::exists($material->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Nama file unduhan: gunakan judul + ekstensi asli
        $ext = pathinfo($material->file_path, PATHINFO_EXTENSION);
        $downloadName = trim($material->title ?: 'material') . ($ext ? ".{$ext}" : '');

        return Storage::download($material->file_path, $downloadName);
    }
}
