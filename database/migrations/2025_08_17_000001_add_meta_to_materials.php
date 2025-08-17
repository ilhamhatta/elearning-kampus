<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('uploader_id')->after('course_id')->constrained('users');
            $table->string('original_name')->after('title');
            $table->string('mime_type')->after('file_path')->nullable();
            $table->unsignedBigInteger('size_bytes')->after('mime_type')->default(0);
            $table->unsignedBigInteger('download_count')->after('size_bytes')->default(0);
            $table->index(['course_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploader_id');
            $table->dropColumn(['original_name', 'mime_type', 'size_bytes', 'download_count']);
            $table->dropIndex(['course_id', 'created_at']);
        });
    }
};
