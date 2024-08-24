<?php

use App\Http\Controllers\AuthUserController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthUserController::class, 'register'])->name('register');
Route::post('login', [AuthUserController::class, 'login'])->name('login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('logout', [AuthUserController::class, 'logout'])->name('logout');
});
