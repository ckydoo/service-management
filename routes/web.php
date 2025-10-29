<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\DataCapturerController;
use App\Http\Controllers\AdminController;

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : view('welcome');
})->name('home');

// ============================================================
// AUTHENTICATION ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// ============================================================
// PROFILE & EMAIL VERIFICATION (All authenticated users)
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::get('profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// ============================================================
// DASHBOARD - Role-based redirect
// ============================================================
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'manager' => redirect()->route('manager.dashboard'),
        'data_capturer' => redirect()->route('data-capturer.dashboard'),
        'technician' => redirect()->route('technician.dashboard'),
        'costing_officer' => redirect()->route('costing-officer.invoices.pending'),
        'customer' => redirect()->route('service-requests.index'),
        default => redirect()->route('login')
    };
})->middleware('auth')->name('dashboard');

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'listUsers'])->name('index');
        Route::get('/{id}', [AdminController::class, 'showUser'])->name('show');
        Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::patch('/{id}', [AdminController::class, 'updateUser'])->name('update');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminController::class, 'reports'])->name('index');
        Route::get('/activity', [AdminController::class, 'activityLog'])->name('activity');
    });

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

// ============================================================
// MANAGER ROUTES
// ============================================================
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'managerDashboard'])->name('dashboard');

    // Service Requests - uses existing views
    Route::prefix('service-requests')->name('service-requests.')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [ServiceRequestController::class, 'show'])->name('show');
        Route::put('/{id}/status', [ServiceRequestController::class, 'updateStatus'])->name('update-status');
    });

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [CustomerController::class, 'update'])->name('update');
    });

    // Technicians
    Route::prefix('technicians')->name('technicians.')->group(function () {
        Route::get('/', [TechnicianController::class, 'index'])->name('index');
        Route::get('/create', [TechnicianController::class, 'create'])->name('create');
        Route::post('/', [TechnicianController::class, 'store'])->name('store');
        Route::get('/{id}', [TechnicianController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TechnicianController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [TechnicianController::class, 'update'])->name('update');
        Route::post('/{id}/assign-job/{jobCardId}', [TechnicianController::class, 'assignJob'])->name('assign-job');
    });

    // Job Cards
    Route::prefix('job-cards')->name('job-cards.')->group(function () {
        Route::get('/', [JobCardController::class, 'index'])->name('index');
        Route::get('/{id}', [JobCardController::class, 'show'])->name('show');
        Route::patch('/{id}', [JobCardController::class, 'update'])->name('update');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/pending', [InvoiceController::class, 'pending'])->name('pending');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/create/{serviceRequestId}', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::patch('/{id}/update-status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::get('/{id}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-paid');
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/{id}', [QuotationController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [QuotationController::class, 'updateStatus'])->name('update-status');
    });
});

// ============================================================
// DATA CAPTURER ROUTES
// ============================================================
Route::middleware(['auth', 'role:data_capturer'])->prefix('data-capturer')->name('data-capturer.')->group(function () {
    Route::get('/dashboard', [DataCapturerController::class, 'dashboard'])->name('dashboard');

    // Service Requests - uses existing views (service-requests.create, service-requests.index, etc.)
    Route::prefix('service-requests')->name('service-requests.')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'capturerIndex'])->name('index');
        Route::get('/create', [ServiceRequestController::class, 'capturerCreate'])->name('create');
        Route::post('/', [ServiceRequestController::class, 'capturerStore'])->name('store');
        Route::get('/{id}', [ServiceRequestController::class, 'capturerShow'])->name('show');
        Route::get('/{id}/edit', [ServiceRequestController::class, 'capturerEdit'])->name('edit');
        Route::patch('/{id}', [ServiceRequestController::class, 'capturerUpdate'])->name('update');
    });

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'capturerIndex'])->name('index');
        Route::get('/{id}', [QuotationController::class, 'capturerShow'])->name('show');
    });

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'capturerIndex'])->name('index');
        Route::get('/{id}', [CustomerController::class, 'capturerShow'])->name('show');
    });

    // Job Cards (View only)
    Route::prefix('job-cards')->name('job-cards.')->group(function () {
        Route::get('/', [JobCardController::class, 'index'])->name('index');
        Route::get('/{id}', [JobCardController::class, 'show'])->name('show');
    });
});

// ============================================================
// TECHNICIAN ROUTES
// ============================================================
Route::middleware(['auth', 'role:technician'])->prefix('technician')->name('technician.')->group(function () {
    Route::get('/dashboard', [JobCardController::class, 'technicianDashboard'])->name('dashboard');
    Route::get('/profile', [TechnicianController::class, 'profile'])->name('profile');

    // Job Cards
    Route::prefix('job-cards')->name('job-cards.')->group(function () {
        Route::get('/{id}', [JobCardController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [JobCardController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/report', [JobCardController::class, 'submitReport'])->name('submit-report');
    });

    // Location & Availability
    Route::post('/location', [TechnicianController::class, 'updateLocation'])->name('update-location');
    Route::post('/availability', [TechnicianController::class, 'updateAvailability'])->name('update-availability');
});

// ============================================================
// CUSTOMER ROUTES - uses existing views
// ============================================================
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Service Requests - reuses service-requests.create, service-requests.customer-index, etc.
    Route::prefix('service-requests')->name('service-requests.')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'customerIndex'])->name('index');
        Route::get('/create', [ServiceRequestController::class, 'create'])->name('create');
        Route::post('/', [ServiceRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [ServiceRequestController::class, 'customerShow'])->name('show');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'customerIndex'])->name('index');
        Route::get('/{id}', [InvoiceController::class, 'customerShow'])->name('show');
        Route::post('/{id}/upload-proof', [InvoiceController::class, 'uploadProofOfPayment'])->name('upload-proof');
    });
});

// ============================================================
// COSTING OFFICER ROUTES
// ============================================================
Route::middleware(['auth', 'role:costing_officer'])->prefix('costing-officer')->name('costing-officer.')->group(function () {
    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/pending', [InvoiceController::class, 'pending'])->name('pending');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::patch('/{id}/cost', [InvoiceController::class, 'updateCost'])->name('update-cost');
        Route::patch('/{id}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{id}/verify-payment', [InvoiceController::class, 'verifyPayment'])->name('verify-payment');
    });

    // Job Cards (View only)
    Route::prefix('job-cards')->name('job-cards.')->group(function () {
        Route::get('/', [JobCardController::class, 'index'])->name('index');
        Route::get('/{id}', [JobCardController::class, 'show'])->name('show');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [InvoiceController::class, 'costingReports'])->name('index');
        Route::get('/analytics', [InvoiceController::class, 'costingAnalytics'])->name('analytics');
    });
});

// ============================================================
// SHARED GLOBAL ROUTES (for common operations across roles)
// ============================================================
Route::middleware('auth')->group(function () {
    // Global quotation actions
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::post('/{id}/approve', [QuotationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [QuotationController::class, 'reject'])->name('reject');
    });
});

require __DIR__.'/auth.php';