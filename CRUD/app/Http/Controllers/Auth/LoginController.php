<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Walidacja danych logowania
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Próba uwierzytelnienia użytkownika
        if (Auth::attempt($credentials)) {
            // Uwierzytelnienie zakończone sukcesem
            $user = Auth::user();
            
            // Sprawdzenie, czy istnieje token w bazie danych dla tego użytkownika
            $existingToken = DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->latest('created_at')
                ->pluck('token')
                ->first();
            
            if (!$existingToken) {
                // Jeżeli token nie istnieje, utwórz nowy
                $user->createToken('AuthToken')->plainTextToken;
                $token = DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->latest('created_at')
                ->pluck('token')
                ->first();
            } else {
                $token = $existingToken;
                DB::table('personal_access_tokens')
                    ->where('token', $token)
                    ->update(['updated_at' => now()]);
            }
            
            return response()->json([
                'message' => 'Authentication successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        } else {
            // Nieudane uwierzytelnienie
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}