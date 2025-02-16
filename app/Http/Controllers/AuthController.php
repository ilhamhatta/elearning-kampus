<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,dosen',
        ]);

        // Buat pengguna baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Kembalikan respons sukses
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Cari pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Periksa apakah pengguna ada dan password benar
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Buat token API untuk pengguna
        $token = $user->createToken('auth-token')->plainTextToken;

        // Kembalikan token
        return response()->json(['token' => $token], 200);
    }

    /**
     * Logout pengguna dan mencabut token API.
     */
    public function logout(Request $request)
    {
        // Cabut token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        // Kembalikan respons sukses
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
