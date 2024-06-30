<?php

namespace App\Modules\Auth\Controllers;

use Auth;
use App\Models\Role;
use App\Models\Company;
use App\Models\PointOfSale;
use Illuminate\Http\Request;
use App\Modules\Auth\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login (Request $request, $site)
    {
        $user = User::where('email', $request->email)->where('site_id', $this->site()->id)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($user->admin == 1) {
                    $token = $user->api_token;
                    $company = Company::where('site_id', $this->site()->id)->first()->id;
                } else  {
                    if ($user->company_id != 0) {
                        $token = $user->api_token;
                        $company= $user->company_id;
                    } else {
                        $token = $user->api_token;
                        $company = Company::where('site_id', $this->site()->id)->first()->id;
                    }
                }
            } else {
                $error = "كلمة السر غير صحيحة";
            }
        } else {
            $error = "البريد الألكتروني غير موجود";
        }
        if (isset($token)) {
            return [
                "status" => "success",
                "token" => $token,
                "company" => $company
            ];
        } else {
            return [
                "status" => "error",
                "error" => $error
            ];
        }
    }
    public function loginCasher (Request $request, $site)
    {
        $company = Company::where('site_id', $this->site()->id)->first()->id;
        $casher = PointOfSale::where('company_id' ,$company)->where('password', $request->casher_id)->first();
        if ($casher) {
            $user = User::find($casher->user_id);
            if ($user) {
                $token = $user->api_token;
                $company = $casher->company_id;
            }
        } else {
            $error = "نقطة البيع غير موجودة";
        }
        if (isset($token) && isset($casher)) {
            return [
                "status" => "success",
                "token" => $token,
                "company" => $company,
                "casher"  => $casher->id
            ];
        } else {
            return [
                "status" => "error",
                "error" => $error
            ];
        }

    }
    public function getUser () {
        $site = $this->site();
        $user = $this->user();

        $role = Role::find($user->role_id);


        if ($role) {
            $role->main_reports   = json_decode($role->main_reports);
            $role->available_reports   = json_decode($role->available_reports);
            $role->main_elements  = json_decode($role->main_elements);
            $role->main_actions   = json_decode($role->main_actions);
            $role->orders_allow_status  = json_decode($role->orders_allow_status);
            $user->role = $role;
        } else {
            $user->role = [
                'main_reports' => [],
                'available_reports' => [],
                'main_elements' => []
            ];
        }

        return [
            'user' => $user,
            'site' => $site
            ];
    }
}
