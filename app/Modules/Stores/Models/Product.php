<?php

namespace App\Modules\Stores\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'code', 'type', 'image', 'description', 'barcode', 'supplier_id',  'tax_id',  'discount_id', 'unit_id', 'section_id', 'allow_product_options', 'product_option_id', 'cost', 'price', 'track_quantity', 'material', 'factory_product', 'manufacturing_model_id', 'upload_key'];

    public function section () {
        return $this->belongsTo('App\Models\Section');
    }
    public function unit () {
        return $this->belongsTo('App\Models\Unit');
    }
    public function tax () {
        return $this->belongsTo('App\Models\Tax');
    }
    public function discount () {
        return $this->belongsTo('App\Models\Discount');
    }
    public function supplier () {
        return $this->belongsTo('App\Models\Supplier');
    }
    public function productQuantities () {
        return $this->hasMany('App\Models\ProductQuantity')->orderBy('id', 'desc');
    }

     public function storesHistories () {
        return $this->hasMany('App\Models\StoresHistory')->orderBy('id', 'desc');
    }
}
