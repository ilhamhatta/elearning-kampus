<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Febby Yanto',
                'email' => 'febbyyanto@gmail.com',
                'password' => Hash::make('febbyyanto'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Muhammad Affandes',
                'email' => 'maffandes@gmail.com',
                'password' => Hash::make('maffandes'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Iwan Iskandar',
                'email' => 'iwaniskandar@gmail.com',
                'password' => Hash::make('iwaniskandar'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jasril Jasril',
                'email' => 'jasriljasril@gmail.com',
                'password' => Hash::make('jasriljasril'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Surya Agustian',
                'email' => 'suryaagustian@gmail.com',
                'password' => Hash::make('suryaagustian'),
                'role' => 'dosen',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'M Ilham Hatta',
                'email' => 'ilhamhatta@gmail.com',
                'password' => Hash::make('ilhamhatta'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Adzra Fakira',
                'email' => 'adzrafakira@gmail.com',
                'password' => Hash::make('adzrafakira'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nanda Jannata',
                'email' => 'nandajannata@gmail.com',
                'password' => Hash::make('nandajannata'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alfitra Salam',
                'email' => 'alfitrasalam@gmail.com',
                'password' => Hash::make('alfitrasalam'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fadil Martias',
                'email' => 'fadilmartias@gmail.com',
                'password' => Hash::make('fadilmartias'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Taufikurrahman',
                'email' => 'taufikurrahman@gmail.com',
                'password' => Hash::make('taufikurrahman'),
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
