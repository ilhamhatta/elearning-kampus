<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // 📌 1️⃣ Statistik jumlah mahasiswa per mata kuliah
    public function courseStats()
    {
        $courses = Course::withCount('students')->get(['id', 'name']);
        return response()->json($courses);
    }

    // 📌 2️⃣ Statistik tugas yang sudah/belum dinilai
    public function assignmentStats()
    {
        $assignments = Assignment::withCount([
            'submissions as graded_count' => function ($query) {
                $query->whereNotNull('score');
            },
            'submissions as ungraded_count' => function ($query) {
                $query->whereNull('score');
            }
        ])->get(['id', 'title', 'course_id']);

        return response()->json($assignments);
    }

    public function studentStats($id)
    {
        // Cari mahasiswa berdasarkan ID
        $student = User::where('id', $id)->where('role', 'mahasiswa')->first();

        if (!$student) {
            return response()->json(['message' => 'Mahasiswa tidak ditemukan'], 404);
        }

        $stats = Submission::where('student_id', $id)
            ->join('assignments', 'submissions.assignment_id', '=', 'assignments.id')
            ->selectRaw('assignments.title, assignments.course_id, COUNT(submissions.id) as total_submissions, AVG(submissions.score) as avg_score')
            ->groupBy('assignments.id', 'assignments.title', 'assignments.course_id')
            ->get();

        return response()->json([
            'student' => $student->name,
            'stats' => $stats
        ]);
    }
}
