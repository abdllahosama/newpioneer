<?php

namespace App\Modules\Suppliers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bill;
use App\Models\PurchaseOrder;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'image', 'name', 'tax_number', 'balance', 'balance_type', 'file', 'debit', 'creditor', 'sections_account_id', 'opening_balance', 'stat', 'telephone', '	fax', 'mobile', 'email', 'website', 'linkedin', 'facebook', 'twitter', 'google_plus', 'address1', 'address2', 'entity', 'city', 'zip', 'country', 'upload_key'];

    public function billsCount () {
        return Bill::where('supplier_id', $this->id)->count();
    }
    public function purchaseOrderCount () {
        return PurchaseOrder::where('supplier_id', $this->id)->count();
    }
}
