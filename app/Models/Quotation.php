<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Quotation.php
class Quotation extends Model
{
    protected $fillable = ['service_request_id', 'labor_cost', 'parts_cost', 'total_cost', 'validity_days', 'status', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];

    public function serviceRequest() { return $this->belongsTo(ServiceRequest::class); }
}

