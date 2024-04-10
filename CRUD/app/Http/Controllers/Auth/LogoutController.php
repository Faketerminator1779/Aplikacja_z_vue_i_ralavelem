<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Dodaj import DB

class LogoutController extends Controller
{
    private function getTokenableId($token)
    {
        $tokenRecord = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->first();

        return $tokenRecord ? $tokenRecord->tokenable_id : null;
    }



    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        echo $token;
        $tokenableId = $this->getTokenableId($token);
        if (!$tokenableId) { 
            return response()->json(['error' => 'Invalid token'], 401);
        }
        echo $tokenableId;

        DB::table('personal_access_tokens')->where('tokenable_id', $tokenableId)->delete();
            
        return response()->json(['message' => 'Wylogowałeś się'], 200);
        
    }
}