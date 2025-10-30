<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    // FIXED: Added 'job_reference' and 'notes' to fillable
    protected $fillable = [
        'service_request_id',
        'technician_id',
        'job_reference',        // ⬅️ ADDED
        'status',
        'estimated_duration',
        'started_at',
        'completed_at',
        'notes',                // ⬅️ ADDED
        'work_plan'
    ];

    // Cast timestamps
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function statusUpdates()
    {
        return $this->hasMany(JobStatusUpdate::class);
    }

    public function serviceReport()
    {
        return $this->hasOne(ServiceReport::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
