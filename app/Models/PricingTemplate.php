<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/PricingTemplate.php
class PricingTemplate extends Model
{
    protected $fillable = ['service_type', 'labor_cost_per_hour', 'parts_markup_percentage', 'is_active'];
}
