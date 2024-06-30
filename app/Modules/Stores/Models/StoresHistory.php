<?php

namespace App\Modules\Stores\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoresHistory extends Model
{
    use HasFactory;
    protected $fillable = ['store_id', 'product_id', 'product_quantity_id', 'invoice_id', 'bill_id', 'sale_return_id', 'purchase_return_id', 'from_store_id', 'date', 'type', 'quantity', 'notes'];

    public function store () {
        return $this->belongsTo(Store::class);
    }
    public function product () {
        return $this->belongsTo(Product::class);
    }
    public function invoice () {
        return $this->belongsTo('App\Models\Invoice');
    }
    public function bill () {
        return $this->belongsTo('App\Models\Bill');
    }
    public function saleReturn () {
        return $this->belongsTo('App\Models\SaleReturn');
    }
    public function purchaseReturn () {
        return $this->belongsTo('App\Models\PurchaseReturn');
    }

    public function storesDeposit () {
        return $this->belongsTo('App\Models\StoresDeposit');
    }
    public function storesWithdrawal () {
        return $this->belongsTo('App\Models\StoresWithdrawal');
    }
    public function storesTransfer () {
        return $this->belongsTo('App\Models\StoresTransfer');
    }

    public function fromStore () {
        return $this->belongsTo('App\Models\Store', 'from_store_id');
    }
    public function productQuantity () {
        return $this->belongsTo('App\Models\ProductQuantity');
    }
}
