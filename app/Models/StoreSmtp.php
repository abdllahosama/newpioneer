<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StoreSmtp extends Model
{
	protected $table = "store_smtp";
	protected $fillable = ['store_id', 'smtp_sender_name', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password'];

	// public function getCreatedAtAttribute($value)
	// {
	// 	return \Carbon\Carbon::parse($value)->timezone(get_settings()->timezone ?? 'Asia/Kuwait')->format('Y-m-d H:i:s');
	// }

	// public function getUpdatedAtAttribute($value)
	// {
	// 	return \Carbon\Carbon::parse($value)->timezone(get_settings()->timezone ?? 'Asia/Kuwait')->format('Y-m-d H:i:s');
	// }
}
