<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Rules\Sara;

class MurabahahRequest extends FormRequest
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
            'no_murabahah' => ['required'],   
            'date' => ['required'],
            'date_trans' => ['required'],
            'nilai_awal' => ['required'],
            'margin' => ['required']
        ];

        if (Request::input('id')=='') {
            $rules['no_anggota'] =['required'];
            $rules['type'] =['required'];
        }

        if (Request::input('type')==1) {
            $rules['nilai_total_jasa'] =['required'];
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
            'nilai_awal.required' => "Harga Item required",
            'min' => ':attribute min :min characters',
            'digits_between' => ':attribute digits beetwen 1 and 16',       
            'sara' => ':attribute contains SARA',
            'numeric' => ':attribute must be number',
            'unique' => 'Kode sudah digunakan',
        ];
    }
}
