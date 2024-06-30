<?php

namespace App\Modules\StoresDeposits\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoresDepositsItem extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'user_id', 'date', 'stores_deposit_id', 'product_quantity_id', 'product_name', 'product_id', 'unit_id', 'quantity', 'count'];

    public function storesDeposit () {
        return $this->belongsTo(StoresDeposit::class);
    }

    public function unit () {
        return $this->belongsTo(Unit::class);
    }
}
