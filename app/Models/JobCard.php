<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/JobCard.php
class JobCard extends Model
{
    protected $fillable = ['service_request_id', 'technician_id', 'work_plan', 'estimated_duration', 'status'];

    public function serviceRequest() { return $this->belongsTo(ServiceRequest::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
    public function statusUpdates() { return $this->hasMany(JobStatusUpdate::class); }
    public function serviceReport() { return $this->hasOne(ServiceReport::class); }
    public function invoice() { return $this->hasOne(Invoice::class); }
}
