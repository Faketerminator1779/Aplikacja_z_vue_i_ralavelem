<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\LogoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', [PostsController::class, 'index']);
Route::get('/drzwi', function () {
    return view('Test');
});
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/post', [PostController::class, 'create']);
Route::post('/logout', [LogoutController::class, 'logout']);
Route::post('/login', [LoginController::class, 'login']); 
Route::delete('/posts', [PostsController::class, 'delete']);
