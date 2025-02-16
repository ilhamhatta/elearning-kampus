<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    // GET /courses -> Semua mata kuliah
    public function index()
    {
        return response()->json(Course::with('lecturer')->get());
    }

    // POST /courses -> Dosen menambahkan mata kuliah
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Hanya dosen yang bisa menambah mata kuliah
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

        // Hanya dosen pemilik mata kuliah yang bisa mengedit
        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->update($request->all());

        return response()->json($course);
    }

    // DELETE /courses/{id} -> Dosen menghapus mata kuliah
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Hanya dosen pemilik mata kuliah yang bisa menghapus
        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted']);
    }

    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();

        // Hanya mahasiswa yang bisa mendaftar
        if ($user->role !== 'mahasiswa') {
            return response()->json(['message' => 'Only students can enroll'], 403);
        }

        // Cek apakah sudah terdaftar
        if ($course->students()->where('student_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already enrolled'], 400);
        }

        // Tambahkan mahasiswa ke mata kuliah
        $course->students()->attach($user->id);

        return response()->json(['message' => 'Enrolled successfully']);
    }
}
