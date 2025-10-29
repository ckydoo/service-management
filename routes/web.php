<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\JobCardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::middleware('auth')->group(function () {

    // Dashboard Routes (Role-based)
    Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])->middleware('role:manager');
    Route::get('/technician/dashboard', [JobCardController::class, 'technicianDashboard'])->middleware('role:technician');

    // Service Request Routes
    Route::prefix('service-requests')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'index'])->name('service-requests.index');
        Route::post('/', [ServiceRequestController::class, 'store'])->name('service-requests.store');
        Route::get('/{id}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
        Route::put('/{id}/status', [ServiceRequestController::class, 'updateStatus'])->name('service-requests.update-status');
    });

    // Quotation Routes
    Route::prefix('quotations')->group(function () {
        Route::post('/{id}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
        Route::post('/{id}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    });

    // Technician Routes
    Route::prefix('technicians')->group(function () {
        Route::post('/{id}/location', [TechnicianController::class, 'updateLocation'])->name('technicians.update-location');
        Route::post('/{id}/availability', [TechnicianController::class, 'updateAvailability'])->name('technicians.update-availability');
        Route::post('/assign-job/{jobCardId}', [TechnicianController::class, 'assignJob'])->name('technicians.assign-job');
    });

    // Job Card Routes
    Route::prefix('job-cards')->group(function () {
        Route::put('/{id}/status', [JobCardController::class, 'updateStatus'])->name('job-cards.update-status');
        Route::post('/{id}/report', [JobCardController::class, 'submitReport'])->name('job-cards.submit-report');
    });

    // Invoice & Payment Routes
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'customerInvoices'])->name('invoices.index');
        Route::post('/{id}/payment-proof', [InvoiceController::class, 'uploadPaymentProof'])->name('invoices.upload-proof');
        Route::post('/verify-payment/{proofId}', [InvoiceController::class, 'verifyPayment'])->name('invoices.verify-payment')->middleware('role:costing_officer');
    });
});

// Guest Routes
Route::post('/service-requests/create', [ServiceRequestController::class, 'store'])->name('service-requests.guest-store');
