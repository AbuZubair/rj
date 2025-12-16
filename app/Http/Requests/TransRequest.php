<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Rules\Sara;

class TransRequest extends FormRequest
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
            'transDate'  => 'required',  
            'tans_desc' => 'required'
        ];

        if (Request::input('id')=='') {
            $rules['coa_code_kredit'] = 'required_without:coa_code_debit';
            $rules['coa_code_debit'] = 'required_without:coa_code_kredit';
        }else{
            $rules['dk'] = 'required';
            $rules['coa_code'] = 'required';
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
            'email' => 'Email format invalid',
        ];
    }
}
