<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;
use App\Models\Course;

class MaterialPolicy
{
    public function viewAny(User $user): bool
    {
        // semua user terotentikasi boleh "melihat" daftar terbatas (difilter di controller)
        return true;
    }

    public function create(User $user, Course $course): bool
    {
        return $user->role === 'dosen' && (int)$course->lecturer_id === (int)$user->id;
    }

    public function download(User $user, Material $material): bool
    {
        if ($user->role === 'dosen' && (int)$material->course->lecturer_id === (int)$user->id) {
            return true;
        }
        if ($user->role === 'mahasiswa') {
            return $material->course
                ->activeStudents()
                ->where('users.id', $user->id)
                ->exists();
        }
        return false;
    }

    public function delete(User $user, Material $material): bool
    {
        return $user->role === 'dosen' && (int)$material->course->lecturer_id === (int)$user->id;
    }
}
