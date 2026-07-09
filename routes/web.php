<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\InvoiceController;
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

    // The "create"/"generate" routes must be registered before the
    // "{invoice}" wildcard routes below, otherwise they're matched as an
    // invoice ID.
    Route::middleware('role:admin,accountant')->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/generate', [InvoiceController::class, 'generateForm'])->name('invoices.generate.form');
        Route::post('/invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::post('/invoices/{invoice}/apply-late-fee', [InvoiceController::class, 'applyLateFee'])->name('invoices.apply-late-fee');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
