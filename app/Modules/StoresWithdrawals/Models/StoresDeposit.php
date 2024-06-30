<?php

namespace App\Modules\StoresDeposits\Models;

use App\Modules\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoresDeposit extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'user_id', 'store_id', 'code', 'file', 'refrance', 'date', 'status', 'description', 'notes', 'upload_key'];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function company () {
        return $this->belongsTo('App\Models\Company');
    }

    public function store () {
        return $this->belongsTo('App\Models\Store');
    }

}
