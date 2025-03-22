<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserController;

// register routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('registrationUser', [RegisterController::class, 'registrationUser'])->name('registrationUser.store');
// login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
// logout routes
Route::post('logout', [LogoutController::class, 'logout']);

// users pages
Route::get('/userProfil', [UserController::class, 'showProfile'])->name('userProfil');
Route::get('/dashboard', [UserController::class, 'showDashboard'])->name('dashboard');
