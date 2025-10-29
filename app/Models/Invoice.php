<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Invoice.php
class Invoice extends Model
{
    protected $fillable = ['job_card_id', 'service_request_id', 'customer_id', 'subtotal', 'tax', 'total_amount', 'payment_status', 'generated_at', 'payment_verified_at'];
    protected $casts = ['generated_at' => 'datetime', 'payment_verified_at' => 'datetime'];

    public function jobCard() { return $this->belongsTo(JobCard::class); }
    public function serviceRequest() { return $this->belongsTo(ServiceRequest::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function paymentProofs() { return $this->hasMany(PaymentProof::class); }
}


