<?php

namespace App\Http\Requests\Auth;

use App\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class ResendVerificationCodeRequest extends FormRequest
{
    use GetRegisterFieldAndValueTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'mobile' => ['required_without:email|numeric', 'nullable', 'sometimes',new Mobile],
            'email' => ['required_without:mobile|email', 'sometimes', 'nullable'],
        ];
    }
}
