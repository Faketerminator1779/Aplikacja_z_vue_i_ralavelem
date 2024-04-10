<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class PostController extends Controller
{
    private function getTokenableId($token)
    {
        $tokenRecord = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->first();

        return $tokenRecord ? $tokenRecord->tokenable_id : null;
    }

    public function create(Request $request)
    {
        // Pobierz wartość nagłówka Authorization
        $token = $request->header('Authorization');
        
        // Sprawdź, czy nagłówek Authorization jest ustawiony
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Sprawdź poprawność tokenu JWT
        $tokenableId = $this->getTokenableId($token);
        if (!$tokenableId) { 
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Walidacja danych wejściowych
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Utwórz nowy post
        $post = new Post();
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        
        // Ustaw autora posta jako tokenable_id
        $post->author = $tokenableId;

        // Zapisz post do bazy danych
        $post->save();

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    
}