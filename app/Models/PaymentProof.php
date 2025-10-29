<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/PaymentProof.php
class PaymentProof extends Model
{
    protected $fillable = ['invoice_id', 'file_path', 'verification_status', 'verified_by', 'verification_notes', 'uploaded_at', 'verified_at'];
    protected $casts = ['uploaded_at' => 'datetime', 'verified_at' => 'datetime'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }
}
