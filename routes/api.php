<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/materials', [MaterialController::class, 'store']); // Dosen upload materi
    Route::get('/materials/{id}/download', [MaterialController::class, 'download']); // Download materi
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/assignments', [AssignmentController::class, 'store']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::post('/submissions/{id}/grade', [SubmissionController::class, 'grade']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/discussions', [DiscussionController::class, 'store']);
    Route::post('/discussions/{id}/replies', [DiscussionController::class, 'reply']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reports/courses', [ReportController::class, 'courseStats']); // Statistik jumlah mahasiswa per mata kuliah
    Route::get('/reports/assignments', [ReportController::class, 'assignmentStats']); // Statistik tugas yang sudah/belum dinilai
    Route::get('/reports/students/{id}', [ReportController::class, 'studentStats']); // Statistik tugas dan nilai mahasiswa
});
