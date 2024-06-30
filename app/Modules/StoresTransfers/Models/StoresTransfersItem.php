<?php

namespace App\Modules\StoresTransfers\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\StoresDeposits\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoresTransfersItem extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'user_id', 'date', 'stores_transfer_id', 'product_quantity_id', 'product_name', 'product_id', 'unit_id', 'quantity', 'count'];

    public function StoresTransfer () {
        return $this->belongsTo(StoresTransfer::class);
    }

    public function unit () {
        return $this->belongsTo(Unit::class);
    }
}
