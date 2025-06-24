<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SignInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'password' => 'min:6',
        ];

        if (filter_var(Request::input('username'), FILTER_VALIDATE_EMAIL)) {
            $rules['username'] = 'exists:user,email';
        }else{
            $rules['username'] = 'exists:user,username';
        }

        return $rules;
    }

    /**
     * Set custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'Silahkan isi :attribute',
            'min' => 'Sandi minimum 6 karakter',
            'exists' => 'Username/Email tidak terdaftar'
        ];
    }
}