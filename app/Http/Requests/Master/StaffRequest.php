<?php

namespace App\Http\Requests\Master;

use App\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Sara;

class StaffRequest extends FormRequest
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
            'avatar' => 'image',
            'jk' => ['required'],
            'fullname' => ['required','min:3', new Sara],
            'tempat_lahir' => ['required'],
            'tanggal_lahir' => ['required'],
            'bank' => ['required'],
            'no_rek' => ['required'],
            'an_rek' => ['required'],
            'nik' => ['required'],
            'join_date' => ['required']
        ];
        
        if (Request::input('id')=='') {
            $rules['nip'] = ['required', 'unique:staff'];
        }else{
            $rules['nip'] = ['required'];
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
            'email' => 'Format Email salah',
            'unique' => ':attribute must be unique'
        ];
    }
}
