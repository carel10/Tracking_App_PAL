<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthVerifyController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Verifikasi password (password_hash)
        if (!Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Ambil roles (hasil dari relasi many-to-many)
        $roles = $user->roles->pluck('name')->toArray();

        return response()->json([
            'message' => 'OK',
            'user' => [
                'id'          => $user->id,
                'full_name'   => $user->full_name,
                'email'       => $user->email,
                'division_id' => $user->division_id,
                'roles'       => $roles
            ]
        ]);
    }
}
