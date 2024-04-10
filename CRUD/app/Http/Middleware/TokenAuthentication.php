<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use App\Models\User; // Załóżmy, że model użytkownika znajduje się w App\Models\User
use Illuminate\Support\Facades\Auth;

class TokenAuthentication
{
    public function handle($request, Closure $next)
    {
        // Sprawdź, czy nagłówek Authorization jest obecny w żądaniu
        if (!$request->header('Authorization')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Pobierz token z nagłówka Authorization
        $token = $request->header('Authorization');

        // Sprawdź poprawność tokena
        try {
            // Spróbuj zweryfikować token
            $decoded = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            
            // Sprawdź, czy w zdekodowanym tokenie jest ID użytkownika
            if (isset($decoded->user_id)) {
                // Znajdź użytkownika na podstawie ID
                $user = User::find($decoded->user_id);
                
                // Sprawdź, czy użytkownik istnieje
                if (!$user) {
                    return response()->json(['error' => 'User not found'], 401);
                }
                
                // Zaloguj użytkownika
                Auth::login($user);

                // Kontynuuj przetwarzanie żądania
                return $next($request);
            }
        } catch (\Exception $e) {
            // W przypadku błędu zwróć błąd autoryzacji
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Jeśli nie udało się zweryfikować tokenu lub uzyskać użytkownika, zwróć błąd autoryzacji
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}