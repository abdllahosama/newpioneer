<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|max:15',
        ];
    }
}
