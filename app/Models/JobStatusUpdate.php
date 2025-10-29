<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/JobStatusUpdate.php
class JobStatusUpdate extends Model
{
    protected $fillable = ['job_card_id', 'status', 'location_lat', 'location_lng', 'notes'];
    public $timestamps = false;

    public function jobCard() { return $this->belongsTo(JobCard::class); }
}
