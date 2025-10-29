<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function customer() { return $this->hasOne(Customer::class); }
    public function technician() { return $this->hasOne(Technician::class); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }
}


