<?php

use App\Models\Invoice;
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

// Public Routes
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
})->name('home');

// ============================================================
// BREEZE AUTHENTICATION ROUTES (Auto-generated, DON'T REMOVE)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);

    Route::put('password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])
        ->name('password.update');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// ============================================================
// DASHBOARD REDIRECT
// ============================================================
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'manager' => redirect('/manager/dashboard'),
        'technician' => redirect('/technician/dashboard'),
        'customer' => redirect('/service-requests'),
        'costing_officer' => redirect('/invoices/pending'),
        default => abort(403, 'Unknown role')
    };
})->middleware('auth')->name('dashboard');

// ============================================================
// AUTHENTICATED ROUTES
// ============================================================
Route::middleware('auth')->group(function () {

    // ---- Manager Dashboard ----
    Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])
        ->middleware('role:manager')
        ->name('manager.dashboard');

    // ---- Technician Dashboard & Profile ----
    Route::prefix('technician')->middleware('role:technician')->group(function () {
        Route::get('/dashboard', [JobCardController::class, 'technicianDashboard'])
            ->name('technician.dashboard');
        Route::get('/profile', [TechnicianController::class, 'profile'])
            ->name('technician.profile');
    });

    // ---- Service Requests ----
    Route::prefix('service-requests')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'index'])
            ->name('service-requests.index');
        Route::post('/', [ServiceRequestController::class, 'store'])
            ->name('service-requests.store');
        Route::get('/{id}', [ServiceRequestController::class, 'show'])
            ->name('service-requests.show');
        Route::put('/{id}/status', [ServiceRequestController::class, 'updateStatus'])
            ->name('service-requests.update-status');
    });

    // ---- Quotations ----
    Route::prefix('quotations')->group(function () {
        Route::post('/{id}/approve', [QuotationController::class, 'approve'])
            ->name('quotations.approve');
        Route::post('/{id}/reject', [QuotationController::class, 'reject'])
            ->name('quotations.reject');
    });

    // ---- Technicians (Manager Only) ----
    Route::prefix('technicians')->middleware('role:manager')->group(function () {
        Route::get('/', [TechnicianController::class, 'index'])
            ->name('technicians.index');
        Route::get('/{id}', [TechnicianController::class, 'show'])
            ->name('technicians.show');
        Route::post('/{id}/assign-job/{jobCardId}', [TechnicianController::class, 'assignJob'])
            ->name('technicians.assign-job');
    });

    // ---- Technician Updates (Any Technician) ----
    Route::prefix('technicians')->middleware('role:technician')->group(function () {
        Route::post('/{id}/location', [TechnicianController::class, 'updateLocation'])
            ->name('technicians.update-location');
        Route::post('/{id}/availability', [TechnicianController::class, 'updateAvailability'])
            ->name('technicians.update-availability');
    });

    // ---- Job Cards ----
    Route::prefix('job-cards')->middleware('role:technician')->group(function () {
        Route::put('/{id}/status', [JobCardController::class, 'updateStatus'])
            ->name('job-cards.update-status');
        Route::post('/{id}/report', [JobCardController::class, 'submitReport'])
            ->name('job-cards.submit-report');
    });

    // ---- Invoices ----
    // ---- Invoices ----
Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'customerInvoices'])
        ->name('invoices.index');

    Route::get('/{id}', [InvoiceController::class, 'show'])
        ->name('invoices.show');

    // ---- Customer: Upload Payment Proof ----
    Route::middleware('role:customer')->group(function () {
        Route::get('/{id}/upload-proof', function($id) {
            $invoice = Invoice::findOrFail($id);
            $user = auth()->user();
            if ($invoice->serviceRequest->customer_id !== $user->customer->id) {
                abort(403);
            }
            return view('invoices.upload-payment-proof', ['invoice' => $invoice]);
        })->name('invoices.upload-proof-form');

        Route::post('/{id}/payment-proof', [InvoiceController::class, 'uploadPaymentProof'])
            ->name('invoices.upload-proof');
    });

    // ---- Costing Officer: Verify Payments ----
    Route::middleware('role:costing_officer')->group(function () {
        Route::get('/pending', [InvoiceController::class, 'pending'])
            ->name('invoices.pending');

        Route::post('/verify-payment/{proofId}', [InvoiceController::class, 'verifyPayment'])
            ->name('invoices.verify-payment');

        Route::get('/proof/{proofId}/download', [InvoiceController::class, 'downloadProof'])
            ->name('invoices.download-proof');
    });

    // ---- Manager Only: Create Invoices ----
    Route::middleware('role:manager')->group(function () {
        Route::get('/create/{serviceRequestId}', [InvoiceController::class, 'create'])
            ->name('invoices.create');

        Route::post('/', [InvoiceController::class, 'store'])
            ->name('invoices.store');

        Route::post('/{id}/status', [InvoiceController::class, 'updateStatus'])
            ->name('invoices.update-status');

        Route::get('/{id}/mark-paid', [InvoiceController::class, 'markAsPaid'])
            ->name('invoices.markAsPaid');

        Route::delete('/{id}', [InvoiceController::class, 'destroy'])
            ->name('invoices.destroy');
    });
});
    // Customers Management - Manager Only
Route::prefix('customers')->middleware('role:manager')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])
        ->name('customers.index');
    Route::get('/{id}', [CustomerController::class, 'show'])
        ->name('customers.show');
    Route::get('/{id}/edit', [CustomerController::class, 'edit'])
        ->name('customers.edit');
    Route::patch('/{id}', [CustomerController::class, 'update'])
        ->name('customers.update');
});

// Technicians Management - Manager Only
Route::prefix('technicians')->middleware('role:manager')->group(function () {
    Route::get('/', [TechnicianController::class, 'index'])
        ->name('technicians.index');
    Route::get('/create', [TechnicianController::class, 'create'])
        ->name('technicians.create');
    Route::post('/', [TechnicianController::class, 'store'])
        ->name('technicians.store');
    Route::get('/{id}', [TechnicianController::class, 'show'])
        ->name('technicians.show');
    Route::get('/{id}/edit', [TechnicianController::class, 'edit'])
        ->name('technicians.edit');
    Route::patch('/{id}', [TechnicianController::class, 'update'])
        ->name('technicians.update');
    Route::post('/{id}/assign-job/{jobCardId}', [TechnicianController::class, 'assignJob'])
        ->name('technicians.assign-job');
});

// Technician Updates (Any Technician)
Route::prefix('technicians')->middleware('role:technician')->group(function () {
    Route::post('/{id}/location', [TechnicianController::class, 'updateLocation'])
        ->name('technicians.update-location');
    Route::post('/{id}/availability', [TechnicianController::class, 'updateAvailability'])
        ->name('technicians.update-availability');
});
});
