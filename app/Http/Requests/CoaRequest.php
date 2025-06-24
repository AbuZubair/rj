<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Rules\Sara;

class CoaRequest extends FormRequest
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
            'coa_name' => ['required','min:3'],   
            'coa_level' => ['required'],        
        ];

        if (Request::input('coa_level')>1) {
            $rules['coa_parent'] = 'required';
        }

        if (Request::input('id')=='') {
            $rules['coa_code'] = ['required','min:1','unique:coa,coa_code,'];
        }else{
            $rules['coa_code'] = ['required','min:1','unique:coa,coa_code,' . Request::input('id')];
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
            'unique' => 'Kode sudah digunakan',
        ];
    }
}
