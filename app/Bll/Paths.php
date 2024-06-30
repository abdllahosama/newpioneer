<?php

namespace App\Bll;

use App\Models\Language;
use App\Models\StoreSmtp;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Paths
{
    public static function get_public_path($store_id,$name)
    {
        return 'uploads/'.$store_id.'/'.$name.'/';
    }

    public static function get_storage_path($store_id,$name)
    {
        return 'uploads/'.$store_id.'/'.$name.'/';
    }
}
