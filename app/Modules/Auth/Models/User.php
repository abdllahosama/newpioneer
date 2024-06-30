<?php

namespace App\Modules\Auth\Models;

use App\Modules\Roles\Models\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id', 'name', 'image', 'email', 'password',  'telephone', 'fax', 'mobile', 'website', 'role_id', 'company_id', 'api_token', 'language', 'safe_id', 'store_id', 'point_of_sale_id', 'price_list_id', 'project_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role () {
        return $this->belongsTo(Role::class);
    }
    public function site () {
        return $this->belongsTo('App\Models\Site');
    }
}
