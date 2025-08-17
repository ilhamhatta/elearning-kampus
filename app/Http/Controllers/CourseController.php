<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // GET /courses -> Semua mata kuliah
    // app/Http/Controllers/CourseController.php (hanya index() yang berubah)
    public function index()
    {
        $user = Auth::user();

        $q = Course::query()
            ->with(['lecturer:id,name'])
            // hitung peserta aktif (pivot non-deleted) via relasi activeStudents
            ->withCount(['activeStudents as students_count']);

        if ($user->role === 'dosen') {
            // Dosen hanya melihat kursus miliknya
            $q->where('lecturer_id', $user->id);
            // Catatan: untuk dosen, field is_enrolled tidak relevan → tidak perlu ditambahkan
        } else {
            // Mahasiswa melihat semua + flag apakah dia terdaftar
            $q->withExists([
                'activeStudents as is_enrolled' => fn($sub) => $sub->where('users.id', $user->id),
            ]);
        }

        return response()->json($q->get());
    }

    // POST /courses -> Dosen menambahkan mata kuliah
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if (Auth::user()->role !== 'dosen') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'lecturer_id' => Auth::id(),
        ]);

        return response()->json($course, 201);
    }

    // PUT /courses/{id} -> Dosen mengedit mata kuliah
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);

        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->update($request->only('name', 'description'));

        return response()->json($course);
    }

    // DELETE /courses/{id} -> Dosen menghapus mata kuliah
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted']);
    }

    // POST /courses/{id}/enroll -> Mahasiswa mendaftar (support restore pivot soft-deleted)
    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Only students can enroll'], 403);
        }

        // Sudah aktif?
        $isActive = $course->students()
            ->wherePivot('student_id', $user->id)
            ->wherePivotNull('deleted_at')
            ->exists();

        if ($isActive) {
            return response()->json(['message' => 'Already enrolled'], 400);
        }

        // Pernah terdaftar tapi di-soft delete? → restore
        $wasSoftDeleted = $course->students()
            ->wherePivot('student_id', $user->id)
            ->whereNotNull('course_student.deleted_at')
            ->exists();

        if ($wasSoftDeleted) {
            $course->students()->updateExistingPivot($user->id, ['deleted_at' => null]);
        } else {
            $course->students()->attach($user->id);
        }

        return response()->json(['message' => 'Enrolled successfully']);
    }

    // (Opsional) DELETE /courses/{id}/enroll -> Mahasiswa batal/keluar (soft delete pivot)
    public function unenroll($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Only students can unenroll'], 403);
        }

        $isActive = $course->students()
            ->wherePivot('student_id', $user->id)
            ->wherePivotNull('deleted_at')
            ->exists();

        if (! $isActive) {
            return response()->json(['message' => 'Not enrolled'], 400);
        }

        // soft delete baris pivot
        $course->students()->updateExistingPivot($user->id, ['deleted_at' => now()]);

        return response()->json(['message' => 'Unenrolled successfully']);
    }
}
