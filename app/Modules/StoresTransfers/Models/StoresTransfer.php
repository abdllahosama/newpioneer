<?php

namespace App\Modules\StoresTransfers\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Stores\Models\Store;
use App\Modules\Company\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoresTransfer extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'user_id', 'from_store_id', 'to_store_id', 'code', 'file', 'refrance', 'date', 'status', 'description', 'notes', 'upload_key'];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function company () {
        return $this->belongsTo(Company::class);
    }

    public function store () {
        return $this->belongsTo(Store::class);
    }

    public function fromStore () {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore () {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
}
