<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Technician.php
class Technician extends Model
{
    protected $fillable = ['user_id', 'skill_set', 'current_location_lat', 'current_location_lng', 'availability_status', 'current_workload', 'rating'];
    protected $casts = ['skill_set' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function jobCards() { return $this->hasMany(JobCard::class); }
    public function serviceReports() { return $this->hasMany(ServiceReport::class); }
}
