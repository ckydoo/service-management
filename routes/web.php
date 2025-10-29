<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
})->name('home');

// ============================================================
// AUTHENTICATION ROUTES (Breeze)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ============================================================
// ROLE-BASED DASHBOARD REDIRECT
// ============================================================
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'manager' => redirect('/manager/dashboard'),
        'technician' => redirect('/technician/dashboard'),
        'data_capturer' => redirect('/data-capturer/dashboard'),
        'costing_officer' => redirect('/costing-officer/dashboard'),
        'customer' => redirect('/customer/service-requests'),
        default => redirect('/customer/service-requests')
    };
})->middleware('auth')->name('dashboard');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {

    // ============================================================
    // CUSTOMER ROUTES
    // ============================================================
    Route::middleware('role:customer')->group(function () {
        // Service Requests
        Route::get('/service-requests', [ServiceRequestController::class, 'customerIndex'])
            ->name('service-requests.index');
        Route::get('/service-requests/create', [ServiceRequestController::class, 'create'])
            ->name('service-requests.create');
        Route::post('/service-requests', [ServiceRequestController::class, 'store'])
            ->name('service-requests.store');
        Route::get('/service-requests/{id}', [ServiceRequestController::class, 'customerShow'])
            ->name('service-requests.show');
        Route::get('/service-requests/{id}/edit', [ServiceRequestController::class, 'edit'])
            ->name('service-requests.edit');
        Route::patch('/service-requests/{id}', [ServiceRequestController::class, 'update'])
            ->name('service-requests.update');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'customerIndex'])
            ->name('invoices.index');
        Route::get('/invoices/{id}', [InvoiceController::class, 'customerShow'])
            ->name('invoices.show');
        Route::post('/invoices/{id}/upload-proof', [InvoiceController::class, 'uploadPaymentProof'])
            ->name('invoices.upload-proof');

        // Quotations
        Route::get('/quotations', [QuotationController::class, 'customerIndex'])
            ->name('quotations.index');
        Route::get('/quotations/{id}', [QuotationController::class, 'customerShow'])
            ->name('quotations.show');
        Route::post('/quotations/{id}/approve', [QuotationController::class, 'approve'])
            ->name('quotations.approve');
        Route::post('/quotations/{id}/reject', [QuotationController::class, 'reject'])
            ->name('quotations.reject');
    });

    // ============================================================
    // MANAGER ROUTES
    // ============================================================
    Route::middleware('role:manager')->group(function () {
        // Dashboard
        Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])
            ->name('manager.dashboard');

        // Service Requests
        Route::get('/service-requests', [ServiceRequestController::class, 'index'])
            ->name('service-requests.index');
        Route::get('/service-requests/{id}', [ServiceRequestController::class, 'show'])
            ->name('service-requests.show');
        Route::get('/service-requests/{id}/edit', [ServiceRequestController::class, 'edit'])
            ->name('service-requests.edit');
        Route::patch('/service-requests/{id}', [ServiceRequestController::class, 'update'])
            ->name('service-requests.update');
        Route::put('/service-requests/{id}/status', [ServiceRequestController::class, 'updateStatus'])
            ->name('service-requests.update-status');

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])
            ->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])
            ->name('customers.store');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])
            ->name('customers.show');
        Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])
            ->name('customers.edit');
        Route::patch('/customers/{id}', [CustomerController::class, 'update'])
            ->name('customers.update');
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])
            ->name('customers.destroy');

        // Technicians
        Route::get('/technicians', [TechnicianController::class, 'index'])
            ->name('technicians.index');
        Route::get('/technicians/create', [TechnicianController::class, 'create'])
            ->name('technicians.create');
        Route::post('/technicians', [TechnicianController::class, 'store'])
            ->name('technicians.store');
        Route::get('/technicians/{id}', [TechnicianController::class, 'show'])
            ->name('technicians.show');
        Route::get('/technicians/{id}/edit', [TechnicianController::class, 'edit'])
            ->name('technicians.edit');
        Route::patch('/technicians/{id}', [TechnicianController::class, 'update'])
            ->name('technicians.update');
        Route::delete('/technicians/{id}', [TechnicianController::class, 'destroy'])
            ->name('technicians.destroy');
        Route::post('/technicians/{id}/assign-job/{jobCardId}', [TechnicianController::class, 'assignJob'])
            ->name('technicians.assign-job');

        // Job Cards
        Route::get('/job-cards', [JobCardController::class, 'index'])
            ->name('job-cards.index');
        Route::get('/job-cards/{id}', [JobCardController::class, 'show'])
            ->name('job-cards.show');
        Route::patch('/job-cards/{id}', [JobCardController::class, 'update'])
            ->name('job-cards.update');
        Route::patch('/job-cards/{id}/status', [JobCardController::class, 'updateStatus'])
            ->name('job-cards.update-status');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])
            ->name('invoices.index');
        Route::get('/invoices/pending', [InvoiceController::class, 'pending'])
            ->name('invoices.pending');
        Route::get('/invoices/{id}', [InvoiceController::class, 'show'])
            ->name('invoices.show');
        Route::get('/invoices/create/{serviceRequestId}', [InvoiceController::class, 'create'])
            ->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])
            ->name('invoices.store');
        Route::patch('/invoices/{id}/update-status', [InvoiceController::class, 'updateStatus'])
            ->name('invoices.update-status');
        Route::patch('/invoices/{id}/verify-payment', [InvoiceController::class, 'verifyPayment'])
            ->name('invoices.verify-payment');
        Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])
            ->name('invoices.destroy');

        // Quotations
        Route::get('/quotations', [QuotationController::class, 'index'])
            ->name('quotations.index');
        Route::get('/quotations/{id}', [QuotationController::class, 'show'])
            ->name('quotations.show');
        Route::post('/quotations', [QuotationController::class, 'store'])
            ->name('quotations.store');
        Route::patch('/quotations/{id}', [QuotationController::class, 'update'])
            ->name('quotations.update');
        Route::delete('/quotations/{id}', [QuotationController::class, 'destroy'])
            ->name('quotations.destroy');
    });

    // ============================================================
    // TECHNICIAN ROUTES
    // ============================================================
    Route::middleware('role:technician')->group(function () {
        // Dashboard
        Route::get('/technician/dashboard', [JobCardController::class, 'technicianDashboard'])
            ->name('technician.dashboard');
        Route::get('/technician/profile', [TechnicianController::class, 'profile'])
            ->name('technician.profile');

        // Job Cards
        Route::get('/job-cards', [JobCardController::class, 'index'])
            ->name('job-cards.index');
        Route::get('/job-cards/{id}', [JobCardController::class, 'show'])
            ->name('job-cards.show');
        Route::patch('/job-cards/{id}/status', [JobCardController::class, 'updateStatus'])
            ->name('job-cards.update-status');
        Route::post('/job-cards/{id}/report', [JobCardController::class, 'submitReport'])
            ->name('job-cards.submit-report');

        // Location & Availability
        Route::post('/technician/location', [TechnicianController::class, 'updateLocation'])
            ->name('technician.update-location');
        Route::post('/technician/availability', [TechnicianController::class, 'updateAvailability'])
            ->name('technician.update-availability');
    });

    // ============================================================
    // DATA CAPTURER ROUTES
    // ============================================================
    Route::middleware('role:data_capturer')->group(function () {
        Route::get('/data-capturer/dashboard', function () {
            return view('data-capturer.dashboard');
        })->name('data-capturer.dashboard');

        // Service Requests
        Route::get('/service-requests', [ServiceRequestController::class, 'index'])
            ->name('service-requests.index');
        Route::get('/service-requests/{id}', [ServiceRequestController::class, 'show'])
            ->name('service-requests.show');
        Route::patch('/service-requests/{id}', [ServiceRequestController::class, 'update'])
            ->name('service-requests.update');
    });

    // ============================================================
    // COSTING OFFICER ROUTES
    // ============================================================
    Route::middleware('role:costing_officer')->group(function () {
        Route::get('/costing-officer/dashboard', function () {
            return redirect()->route('invoices.pending');
        })->name('costing-officer.dashboard');

        // Invoices
        Route::get('/invoices/pending', [InvoiceController::class, 'pending'])
            ->name('invoices.pending');
        Route::get('/invoices/{id}', [InvoiceController::class, 'show'])
            ->name('invoices.show');
        Route::patch('/invoices/{id}/verify-payment', [InvoiceController::class, 'verifyPayment'])
            ->name('invoices.verify-payment');
    });
});

require __DIR__.'/auth.php';
