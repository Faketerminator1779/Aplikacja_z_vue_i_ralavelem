<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{

    private function getTokenableId($token)
    {
        $tokenRecord = DB::table('personal_access_tokens')
            ->where('token', $token)
            ->first();

        return $tokenRecord ? $tokenRecord->tokenable_id : null;
    }

    public function index()
    {
        $posts = Post::all();
        return response()->json($posts);
    }

    public function delete(Request $request)
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

        // Sprawdź, czy użytkownik podał ID posta do usunięcia w ciele żądania
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Post ID is required'], 400);
        }

        // Sprawdź, czy post istnieje
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Sprawdź, czy użytkownik jest autorem posta
        if ($post->author != $tokenableId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Usuń post z bazy danych
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}