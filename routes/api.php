<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthUserController::class, 'register'])->name('auth.register');
Route::post('login', [AuthUserController::class, 'login'])->name('auth.login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [AuthUserController::class, 'user'])->name('user.userDetails');
        Route::post('/update', [AuthUserController::class, 'update'])->name('user.updateProfile');
        Route::get('/logout', [AuthUserController::class, 'logout'])->name('user.logout');
    });



    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('post.index');
        Route::post('/', [PostController::class, 'store'])->name('post.store');
        Route::post('/{post_id}', [PostController::class, 'update'])->name('post.update');
        Route::delete('/{post_id}', [PostController::class, 'destroy'])->name('post.destroy');
        Route::get('{post_id}/comment', [CommentController::class, 'index'])->name('post.comments');
        Route::post('{post_id}/comment', [CommentController::class, 'store'])->name('post.store_comment');
    });

    Route::get('comment/{comment_id}', [CommentController::class, 'show']);
    Route::put('comment/{comment_id}', [CommentController::class, 'update']);
    Route::delete('comment/{comment_id}', [CommentController::class, 'destroy']);

    Route::post('post/{post_id}/likeOrUnLike', [LikeController::class, 'likeOrUnLike']);
});
