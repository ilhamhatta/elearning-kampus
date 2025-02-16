<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $course = Course::findOrFail($request->course_id);

        if (Auth::id() !== $course->lecturer_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $assignment = Assignment::create($request->all());

        return response()->json($assignment, 201);
    }
}
