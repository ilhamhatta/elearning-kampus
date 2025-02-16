<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480' // Max 20MB
        ]);

        $assignment = Assignment::findOrFail($request->assignment_id);

        // Hanya mahasiswa yang bisa mengumpulkan tugas
        if (Auth::user()->role !== 'mahasiswa') {
            return response()->json(['message' => 'Only students can submit assignments'], 403);
        }

        // Simpan file tugas
        $path = $request->file('file')->store('submissions');

        $submission = Submission::create([
            'assignment_id' => $request->assignment_id,
            'student_id' => Auth::id(),
            'file_path' => $path,
        ]);

        return response()->json($submission, 201);
    }

    // Dosen memberi nilai tugas
    public function grade(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:0|max:100'
        ]);

        $submission = Submission::findOrFail($id);
        $assignment = $submission->assignment;

        // Hanya dosen pemilik mata kuliah yang bisa memberi nilai
        if (Auth::id() !== $assignment->course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $submission->update(['score' => $request->score]);

        return response()->json(['message' => 'Submission graded successfully']);
    }
}
