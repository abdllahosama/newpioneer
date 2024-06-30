<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests , DispatchesJobs;

    protected $service;
    protected $UpdateRequest;
    protected $StoreRequest;
    protected function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'result' => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    protected function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'result' => null
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    public function site () {
        $subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
        $site = Site::where('link', $subdomain)->first();
        if ($site){
            return $site;
        }

    }

    public function user () {
        $user = User::where('api_token', $_GET['token'])->first();
        if ($user){
            $user->role = Role::find($user->role_id);
            $user->employee = Employee::where('user_id', $user->id)->first();
            return $user;
        }
    }

    public function company () {
        $company = $_GET['company'];
        if ($company){
            return $company;
        }
    }

}
