<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    // FIXED: Added all necessary fields including 'skills', 'license_number', 'specialization'
    protected $fillable = [
        'user_id',
        'specialization',           // ⬅️ ADDED
        'license_number',           // ⬅️ ADDED
        'skills',                   // ⬅️ ADDED (was 'skill_set')
        'skill_set',                // Keep for backward compatibility
        'current_location_lat',
        'current_location_lng',
        'availability_status',
        'current_workload',
        'rating'
    ];

    // Cast arrays
    protected $casts = [
        'skill_set' => 'array',
    ];

    // Default values
    protected $attributes = [
        'availability_status' => 'available',
        'current_workload' => 0,
        'rating' => 0,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobCards()
    {
        return $this->hasMany(JobCard::class);
    }

    public function serviceReports()
    {
        return $this->hasMany(ServiceReport::class);
    }
}
