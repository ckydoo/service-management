<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'service_request_id',
        'invoice_number',
        'total_amount',
        'payment_status',        // IMPORTANT: This is the correct column name
        'payment_verified_at',
        'notes',
        'due_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
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
