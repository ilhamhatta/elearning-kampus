<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->index(['course_id', 'student_id'], 'cs_course_student');
            $table->index(['student_id', 'course_id'], 'cs_student_course');
            $table->index(['course_id', 'deleted_at'], 'cs_course_deleted'); // bantu activeStudents
        });
    }
    public function down(): void
    {
        Schema::table('course_student', function (Blueprint $table) {
            $table->dropIndex('cs_course_student');
            $table->dropIndex('cs_student_course');
            $table->dropIndex('cs_course_deleted');
        });
    }
};
