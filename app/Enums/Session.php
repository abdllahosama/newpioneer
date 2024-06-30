<?php

namespace App\Enums;

enum Session: string
{
    case STORE_LANG = 'STORE_LANG';
    //    public static function getAllValues(): array
    //    {
    //        return array_column(Session::cases(), 'value');
    //    }

    public static function getLangId(): int
    {
        if (session(Session::STORE_LANG->value))
            return session(Session::STORE_LANG->value)->id;
        return -1;
    }
    public static function getLangCode(): string
    {


        if (session(Session::STORE_LANG->value))
            return session(Session::STORE_LANG->value)->code;
        return "";
    }
}
