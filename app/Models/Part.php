<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Part.php
class Part extends Model
{
    protected $fillable = ['part_name', 'part_code', 'unit_cost', 'stock_quantity', 'reorder_level', 'description'];
}
