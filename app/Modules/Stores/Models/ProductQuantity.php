<?php

namespace App\Modules\Stores\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuantity extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'product_id', 'store_id', 'quantity', 'opening_quantity', 'min_quantity', 'track_quantity'];

    public function store () {
        return $this->belongsTo(Store::class);
    }

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }
}
