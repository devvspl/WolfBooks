<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\GeneratorController;
use App\Http\Controllers\Panel\MasterController;
use App\Http\Controllers\Panel\PageBuilderController;
use App\Http\Controllers\Panel\PageFieldController;
use App\Http\Controllers\Panel\ProfileController;
use App\Http\Controllers\Panel\SettingsController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('privacy');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/verify', [AuthController::class, 'showVerifyRegisterForm'])->name('register.verify.form');
    Route::post('/register/verify', [AuthController::class, 'verifyRegister'])->name('register.verify.submit');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login/verify', [AuthController::class, 'showVerifyLoginForm'])->name('login.verify.form');
    Route::post('/login/verify', [AuthController::class, 'verifyLogin'])->name('login.verify.submit');

    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyForm'])->name('password.reset');
    Route::post('/verify-otp', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/master', [MasterController::class, 'index'])->name('master');

    // Page Builder CRUD
    Route::get('/master/page-builder', [PageBuilderController::class, 'index'])->name('master.page-builder');
    Route::get('/master/page-builder/add', [PageBuilderController::class, 'create'])->name('master.page-builder.create');
    Route::post('/master/page-builder', [PageBuilderController::class, 'store'])->name('master.page-builder.store');
    Route::get('/master/page-builder/{page}/edit', [PageBuilderController::class, 'edit'])->name('master.page-builder.edit');
    Route::put('/master/page-builder/{page}', [PageBuilderController::class, 'update'])->name('master.page-builder.update');
    Route::delete('/master/page-builder/{page}', [PageBuilderController::class, 'destroy'])->name('master.page-builder.destroy');
    Route::get('/master/page-builder/{page}/fields/json', [PageBuilderController::class, 'fields'])->name('master.page-builder.fields.json');
    Route::post('/master/page-builder/{page}/generate', [GeneratorController::class, 'generate'])->name('master.page-builder.generate');

    // Page Fields
    Route::get('/master/page-builder/{page}/fields', [PageFieldController::class, 'index'])->name('master.page-builder.fields');
    Route::post('/master/page-builder/{page}/fields', [PageFieldController::class, 'store'])->name('master.page-builder.fields.store');
    Route::put('/master/page-builder/{page}/fields/{field}', [PageFieldController::class, 'updateSettings'])->name('master.page-builder.fields.settings');
    Route::delete('/master/page-builder/{page}/fields/{field}', [PageFieldController::class, 'destroy'])->name('master.page-builder.fields.destroy');

    Route::get('/master/{tab}', [MasterController::class, 'tab'])->name('master.tab');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // ── Generated CRUD routes (auto-appended by GeneratorController) ──────────
    Route::prefix('generated')->name('generated.')->group(function () {
    });
});
