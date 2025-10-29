<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// app/Models/ServiceRequest.php
class ServiceRequest extends Model
{
    protected $fillable = ['reference_number', 'customer_id', 'machine_id', 'request_description', 'request_type', 'status', 'requires_assessment'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
    public function quotation() { return $this->hasOne(Quotation::class); }
    public function jobCard() { return $this->hasOne(JobCard::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
}

