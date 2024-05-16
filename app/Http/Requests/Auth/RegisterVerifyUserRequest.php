<?php

namespace App\Http\Requests\Auth;

use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class RegisterVerifyUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'code' => 'required|string',
            'email' => 'sometimes|required_without:mobile|email',
            'mobile' => ['required_without:email|numeric','sometimes',new Mobile],
        ];
    }
}
