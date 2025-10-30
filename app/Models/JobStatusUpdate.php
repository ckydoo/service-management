<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatusUpdate extends Model
{
    protected $fillable = [
        'job_card_id', 
        'status', 
        'location_lat', 
        'location_lng', 
        'notes'
    ];

    // FIXED: Enable timestamps (was set to false)
    public $timestamps = true;  // ✅ Changed from false to true

    // Cast created_at and updated_at as datetime
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function jobCard() 
    { 
        return $this->belongsTo(JobCard::class); 
    }
}