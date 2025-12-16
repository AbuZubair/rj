<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Sara;

class UserRequest extends FormRequest
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
            'first_name' => ['required','min:3', new Sara],
            'last_name' => [new Sara],
            'role' => 'required',
        ];  
        
        if (Request::input('id')=='') {
            $rules['password'] = 'required|min:6|confirmed';
        }else{
            if(strlen(Request::input('password')) > 0){
                $rules['password'] = 'min:6|confirmed';
            }
        }

        if(Request::input('role') == '1'){
            $rules['nip'] = 'required';
        }

        if(Request::input('email') != ''){
            $rules['email'] = 'email:rfc,dns';
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
            'required' => ':attribute required',
            'min' => ':attribute min :min characters',
            'digits_between' => ':attribute digits beetwen 1 and 16',       
            'sara' => ':attribute contains SARA',
            'numeric' => ':attribute must be number',
            'confirmed' => 'Retype password incorrect',
            'email' => 'Format Email salah'
        ];
    }
}
