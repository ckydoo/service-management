<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/ActivityLog.php
class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'subject', 'subject_id', 'description', 'ip_address'];
    public $timestamps = false;

    public function user() { return $this->belongsTo(User::class); }
}
