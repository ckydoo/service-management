<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'company_name', 'address', 'city', 'phone', 'email'];

    public function user() { return $this->belongsTo(User::class); }
    public function machines() { return $this->hasMany(Machine::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
