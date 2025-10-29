<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/ServiceReport.php
class ServiceReport extends Model
{
    protected $fillable = ['job_card_id', 'technician_id', 'work_completed', 'parts_used', 'labor_hours', 'additional_notes'];
    protected $casts = ['parts_used' => 'array', 'submitted_at' => 'datetime'];

    public function jobCard() { return $this->belongsTo(JobCard::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
}
