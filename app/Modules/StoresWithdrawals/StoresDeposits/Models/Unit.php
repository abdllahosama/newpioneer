<?php

namespace App\Modules\StoresDeposits\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'unit', 'units_coefficient_id', 'upload_key'];
}
