<?php

namespace App\Modules\Stores\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'stat', 'description', 'address1', 'address2', 'entity', 'city', 'zip', 'country', 'upload_key'];
    public function productQuantities () {
        return $this->hasMany('App\Models\ProductQuantity');
    }

    public function storesHistories () {
        return $this->hasMany('App\Models\StoresHistory');
    }
}
