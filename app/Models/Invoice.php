<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * FIX: Added missing fields that are required by the database schema
     */
    protected $fillable = [
        'invoice_number',           // ✓ Already there
        'job_card_id',             // ✓ ADDED - Required by schema
        'service_request_id',       // ✓ Already there
        'customer_id',             // ✓ ADDED - Required by schema
        'subtotal',                // ✓ ADDED - Required by schema
        'tax',                     // ✓ ADDED - Required by schema
        'total_amount',            // ✓ Already there
        'payment_status',          // ✓ Already there
        'issued_at',               // Optional
        'due_at',                  // Optional
        'paid_at',                 // Optional
        'payment_verified_at',     // ✓ Already there
        'notes',                   // ✓ Already there
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'due_date' => 'date',
        'payment_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the service request that this invoice belongs to
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the customer that this invoice belongs to
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the job card that this invoice belongs to
     */
    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    /**
     * Get all payment proofs for this invoice
     */
    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'verified';
    }

    /**
     * Check if invoice is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if proof has been uploaded
     */
    public function hasProofUploaded(): bool
    {
        return $this->payment_status === 'proof_uploaded';
    }

    /**
     * Scope to get pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope to get verified invoices
     */
    public function scopeVerified($query)
    {
        return $query->where('payment_status', 'verified');
    }

    /**
     * Scope to get invoices awaiting verification
     */
    public function scopeAwaitingVerification($query)
    {
        return $query->whereIn('payment_status', ['pending', 'proof_uploaded']);
    }
}
