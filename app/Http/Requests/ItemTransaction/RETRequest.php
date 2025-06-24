<?php

namespace App\Http\Requests\ItemTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RETRequest extends FormRequest
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
            'transaction_no' => ['required'],   
            'item' => ['required'],
            'qty' => ['required'],
            'satuan' => ['required'],
            'harga' => ['required'],
            'transDate' => ['required'],  
        ];

        if (Request::input('type') == '2') {
            $rules['reference'] = 'required';
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
