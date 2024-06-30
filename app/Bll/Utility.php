<?php

namespace App\Bll;

use App\Enums\Session;
use App\Models\Language;
use App\Models\Store;
use App\Models\StoreSmtp;
use App\Modules\StoreSettings\Models\StoreSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Utility
{

    private static function GetLangObject()
    {
        $firstLang = Language::where('code', App::getLocale())->first();
     //   dd($firstLang);
        if ($firstLang == null)
            $firstLang = Language::first();
        if ($firstLang != null) {
            session(Session::STORE_LANG->value, $firstLang);
            return $firstLang;
        }
        $firstLang =  Language::create(["code" => App::getLocale(), "title" => App::getLocale()]);
        session(Session::STORE_LANG->value, $firstLang);

        return $firstLang;
    }
    public static function lang_id()
    {

        if (App::getLocale() != Session::getLangCode()) {
            session()->remove(Session::STORE_LANG->value);
            return Utility::GetLangObject()->id;
        }
        if ((session()->input(Session::STORE_LANG->value)))
            return session()->input(Session::STORE_LANG->value)->id;

        return Utility::GetLangObject()->id;
    }
    public static function get_store_id()
    {

        if (Auth::check()) {
            return auth()->id();
        }

        return null;
    }



}
