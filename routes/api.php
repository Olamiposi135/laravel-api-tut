<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/all/posts', [PostController::class, 'getAllPosts']);
Route::get('/single/post/{post_id}', [PostController::class, 'getPost']);


//middleware function

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/add/post', [PostController::class, 'addNewPost']);

    Route::put('/edit/post', [PostController::class, 'editPost']);

    Route::put('/edit/post/{post_id}', [PostController::class, 'editPost2']);

    Route::delete('/delete/post/{post_id}', [PostController::class, 'deletePost']);
});
