<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Agent\AgentController;



// register routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('registrationUser', [RegisterController::class, 'registrationUser'])->name('registrationUser.store');
Route::post('registrationUser', [RegisterController::class, 'registrationUser'])->name('registrationUser');

// login routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
// logout routes
// Route::post('logout', [LogoutController::class, 'logout']);


// page based on role
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('/agent/dashboard', [AgentController::class, 'index'])->name('agent.dashboard');
Route::get('/client/dashboard', [ClientController::class, 'index'])->name('client.dashboard');

