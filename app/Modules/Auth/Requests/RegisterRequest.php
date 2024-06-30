<?php

namespace App\Modules\Auth\Requests;

use App\Bll\Utility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{

    public function rules()
    {
        return [
            'user_name'    => 'required|string|max:125|min:3',
            'email'        => 'required|email|unique:users,email|max:255',
            'dialing_code' => 'required|max:5',
            'phone'        => 'required|digits_between:7,20',
            'password'     => ['required', 'confirmed' , 'max:15' , 'min:8'],
            'image'        => 'nullable',
            'national_id'  => 'nullable|unique:users,national_id|digits_between:5,25',

        ];
    }
}
