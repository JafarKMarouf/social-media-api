<?php

use App\Http\Controllers\AuthUserController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthUserController::class, 'register'])->name('register');
Route::post('login', [AuthUserController::class, 'login'])->name('login');
