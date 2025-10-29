<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// app/Models/Machine.php
class Machine extends Model
{
    protected $fillable = ['customer_id', 'machine_name', 'machine_type', 'serial_number', 'model', 'description', 'status'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
}

















