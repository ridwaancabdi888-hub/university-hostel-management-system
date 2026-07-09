<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialsController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,warden')->group(function () {
        Route::resource('rooms', RoomController::class);
        Route::resource('hostels', HostelController::class)->except('show');
        Route::resource('blocks', BlockController::class)->except('show');
        Route::resource('floors', FloorController::class)->except('show');
        Route::resource('room-types', RoomTypeController::class)->except('show');

        Route::get('/allocations/create', [AllocationController::class, 'create'])->name('allocations.create');
        Route::post('/allocations', [AllocationController::class, 'store'])->name('allocations.store');
        Route::get('/allocations/{allocation}/transfer', [AllocationController::class, 'transferForm'])->name('allocations.transfer.form');
        Route::post('/allocations/{allocation}/transfer', [AllocationController::class, 'transfer'])->name('allocations.transfer');
        Route::post('/allocations/{allocation}/vacate', [AllocationController::class, 'vacate'])->name('allocations.vacate');
    });

    Route::middleware('role:admin,warden,accountant')->group(function () {
        Route::get('/allocations', [AllocationController::class, 'index'])->name('allocations.index');
    });

    // The "create" route must be registered before the "{student}" wildcard
    // routes below, otherwise "create" is matched as a student ID.
    Route::middleware('role:admin,warden')->group(function () {
        Route::resource('students', StudentController::class)->except(['index', 'show']);
    });

    Route::middleware('role:admin,warden,accountant')->group(function () {
        Route::resource('students', StudentController::class)->only(['index', 'show']);
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
