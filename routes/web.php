<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialsController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomAllocationController;
use App\Http\Controllers\StudentDirectoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,warden')->group(function () {
        Route::get('/room-allocation', [RoomAllocationController::class, 'index'])->name('room-allocation');
    });

    Route::middleware('role:admin,warden,accountant')->group(function () {
        Route::get('/student-directory', [StudentDirectoryController::class, 'index'])->name('student-directory');
    });

    Route::middleware('role:admin,warden,student')->group(function () {
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance');
    });

    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/financials', [FinancialsController::class, 'index'])->name('financials');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
