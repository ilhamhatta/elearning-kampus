<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    // GET /materials?course_id=&q=&per_page=
    public function index(Request $request)
    {
        $this->authorize('viewAny', Material::class);

        $user = Auth::user();
        $q = Material::query()->with('course:id,name,lecturer_id');

        if ($request->filled('course_id')) {
            $q->where('course_id', (int)$request->query('course_id'));
        } else {
            if ($user->role === 'dosen') {
                $q->whereHas('course', fn($c) => $c->where('lecturer_id', $user->id));
            } else {
                $q->whereHas('course.activeStudents', fn($s) => $s->where('users.id', $user->id));
            }
        }

        if ($kw = trim((string)$request->query('q'))) {
            // escape % _ \
            $kw = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $kw);
            $q->where(
                fn($qq) =>
                $qq->where('title', 'like', "%{$kw}%")
                    ->orWhere('original_name', 'like', "%{$kw}%")
            );
        }

        $rows = $q->latest()->paginate((int)$request->query('per_page', 15));

        return MaterialResource::collection($rows)->additional([
            'meta' => [
                'page'     => $rows->currentPage(),
                'per_page' => $rows->perPage(),
                'total'    => $rows->total(),
            ],
        ]);
    }

    // POST /materials
    public function store(StoreMaterialRequest $request)
    {
        $course = Course::findOrFail($request->course_id);
        $this->authorize('create', [Material::class, $course]);

        $file = $request->file('file');
        $ext  = $file->getClientOriginalExtension();
        $path = Storage::disk('materials')->putFileAs(
            (string)$course->id,
            $file,
            Str::uuid() . ($ext ? ".{$ext}" : "")
        );

        $material = Material::create([
            'course_id'     => $course->id,
            'uploader_id'   => Auth::id(),
            'title'         => $request->title,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size_bytes'    => $file->getSize(),
        ]);

        Log::info('material.upload', [
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
            'course_id'   => $course->id,
            'size'        => $material->size_bytes,
            'mime'        => $material->mime_type,
        ]);

        return (new MaterialResource($material->load('course:id,name')))
            ->response()
            ->setStatusCode(201);
    }

    // GET /materials/{id}/download
    public function download($id)
    {
        $material = Material::with('course:id,lecturer_id')->findOrFail($id);
        $this->authorize('download', $material);

        if (!Storage::disk('materials')->exists($material->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $ext = pathinfo($material->file_path, PATHINFO_EXTENSION);
        $downloadName = trim($material->title ?: 'material') . ($ext ? ".{$ext}" : '');

        $material->increment('download_count');

        Log::info('material.download', [
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ]);

        $headers = [
            'Content-Type'        => $material->mime_type ?: 'application/octet-stream',
            'X-File-Name'         => $downloadName,
            'Content-Disposition' => "attachment; filename=\"" . addcslashes($downloadName, "\"\\") . "\"; filename*=UTF-8''" . rawurlencode($downloadName),
            'Cache-Control'       => 'private, max-age=0, must-revalidate',
        ];


        $disk = Storage::disk('materials');
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        return $disk->download($material->file_path, $downloadName, $headers);
    }

    // DELETE /materials/{id} (opsional)
    public function destroy($id)
    {
        $material = Material::with('course:id,lecturer_id')->findOrFail($id);
        $this->authorize('delete', $material);

        $material->delete(); // soft delete, file tetap ada sampai force delete (sesuai policy lifecycle)
        Log::info('material.delete', [
            'material_id' => $material->id,
            'user_id'     => Auth::id(),
        ]);

        return response()->json(['message' => 'deleted'], 200);
    }
}
