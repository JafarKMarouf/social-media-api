<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;




Route::prefix('auth')
    ->as('auth.')
    ->group(function () {
        // Route::get('/', [AuthUserController::class, 'user'])->name('user.userDetails');
        // Route::post('/update', [AuthUserController::class, 'update'])->name('user.updateProfile');

        Route::post('register', [AuthUserController::class, 'register'])
            ->name('register');
        Route::post('login', [AuthUserController::class, 'login'])
            ->name('login');
        Route::get('logout', [AuthUserController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
    });


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('post')->group(function () {
        Route::apiResource('post', PostController::class);
        Route::get('{post_id}/comment', [CommentController::class, 'index'])->name('post.comments');
        Route::post('{post_id}/comment', [CommentController::class, 'store'])->name('post.store_comment');
        Route::post('{post_id}/likeOrUnLike', [LikeController::class, 'likeOrUnLike']);
    });

    Route::get('comment/{comment_id}', [CommentController::class, 'show']);
    Route::put('comment/{comment_id}', [CommentController::class, 'update']);
    Route::delete('comment/{comment_id}', [CommentController::class, 'destroy']);

    Route::apiResource('chat', ChatController::class)
        ->middleware('auth:sanctum');

    Route::apiResource('message', MessageController::class)
        ->middleware('auth:sanctum');
});
