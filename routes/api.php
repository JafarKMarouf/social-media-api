<?php

use App\Http\Controllers\AuthUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthUserController::class, 'register'])->name('register');
