<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceCommentController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VisitorController;
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

    // The "create" route must be registered before the "{ticket}" wildcard
    // routes below, otherwise "create" is matched as a ticket ID.
    Route::middleware('role:admin,warden,student')->group(function () {
        Route::get('/maintenance/create', [MaintenanceRequestController::class, 'create'])->name('maintenance.create');
        Route::post('/maintenance', [MaintenanceRequestController::class, 'store'])->name('maintenance.store');

        Route::get('/maintenance', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
        Route::get('/maintenance/{ticket}', [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
        Route::get('/maintenance/{ticket}/edit', [MaintenanceRequestController::class, 'edit'])->name('maintenance.edit');
        Route::put('/maintenance/{ticket}', [MaintenanceRequestController::class, 'update'])->name('maintenance.update');
        Route::post('/maintenance/{ticket}/status', [MaintenanceRequestController::class, 'updateStatus'])->name('maintenance.status');
        Route::post('/maintenance/{ticket}/comments', [MaintenanceCommentController::class, 'store'])->name('maintenance.comments.store');
    });

    Route::middleware('role:admin,warden')->group(function () {
        Route::post('/maintenance/{ticket}/assign', [MaintenanceRequestController::class, 'assign'])->name('maintenance.assign');
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
        Route::post('/invoices/{invoice}/apply-late-fee', [InvoiceController::class, 'applyLateFee'])->name('invoices.apply-late-fee');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    });

    // Reports Center — each report page/export additionally checks the
    // current role against ReportController::REPORTS_BY_ROLE.
    Route::middleware('role:admin,warden,accountant')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('/reports/billing', [ReportController::class, 'billing'])->name('reports.billing');
        Route::get('/reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
        Route::get('/reports/students', [ReportController::class, 'students'])->name('reports.students');
        Route::get('/reports/hostels', [ReportController::class, 'hostels'])->name('reports.hostels');

        Route::get('/reports/{type}/pdf', [ReportController::class, 'exportPdf'])
            ->where('type', 'occupancy|billing|payments|students|hostels')
            ->name('reports.pdf');
        Route::get('/reports/{type}/excel', [ReportController::class, 'exportExcel'])
            ->where('type', 'occupancy|billing|payments|students|hostels')
            ->name('reports.excel');
    });

    // The "create" route must be registered before the "{visitor}" wildcard
    // routes below, otherwise "create" is matched as a visitor ID.
    Route::middleware('role:admin,warden,student')->group(function () {
        Route::get('/visitors/create', [VisitorController::class, 'create'])->name('visitors.create');
        Route::post('/visitors', [VisitorController::class, 'store'])->name('visitors.store');

        Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
        Route::get('/visitors/{visitor}', [VisitorController::class, 'show'])->name('visitors.show');
        Route::get('/visitors/{visitor}/edit', [VisitorController::class, 'edit'])->name('visitors.edit');
        Route::put('/visitors/{visitor}', [VisitorController::class, 'update'])->name('visitors.update');
    });

    Route::middleware('role:admin,warden')->group(function () {
        Route::post('/visitors/{visitor}/approve', [VisitorController::class, 'approve'])->name('visitors.approve');
        Route::post('/visitors/{visitor}/reject', [VisitorController::class, 'reject'])->name('visitors.reject');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
