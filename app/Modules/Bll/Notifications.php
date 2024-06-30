<?php

namespace App\Modules\Bll;


use App\Mail\MyEmail;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Modules\Notifications\Models\Notifications as NotificationsModel;


class Notifications
{

    /**
     * @param $title
     * @param $content
     * @param $type
     * @param $type_id
     * @param $user_id
     * @return string
     */


    public static function storeNotification($title, $content, $type, $type_id, $user_id)
    {
        try {
            NotificationsModel::create([
                'title'    => $title,
                'content'  => $content,
                'type'     => $type,
                'type_id'  => $type_id,
                'user_id'  => $user_id,
            ]);
            $user = User::where('id', $user_id)->first();
            if($user){
                Mail::to($user->email)->send(new MyEmail($title, $content, $type, $type_id));
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
