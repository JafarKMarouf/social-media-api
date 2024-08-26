<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthUserController::class, 'register'])->name('register');
Route::post('login', [AuthUserController::class, 'login'])->name('login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('logout', [AuthUserController::class, 'logout'])->name('logout');

    Route::resource('post', PostController::class);

    Route::prefix('post')->group(function () {
        Route::get('{post_id}/comment', [CommentController::class, 'index']);
        Route::post('{post_id}/comment', [CommentController::class, 'store']);
    });
    Route::get('comment/{comment_id}', [CommentController::class, 'show']);
    Route::put('comment/{comment_id}', [CommentController::class, 'update']);
    Route::delete('comment/{comment_id}', [CommentController::class, 'destroy']);

    Route::post('post/{post_id}/likeOrUnLike', [LikeController::class, 'likeOrUnLike']);
});
