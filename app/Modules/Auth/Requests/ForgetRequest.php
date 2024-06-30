<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ForgetRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email'    => 'required|email|unique:users',
        ];
    }
}
