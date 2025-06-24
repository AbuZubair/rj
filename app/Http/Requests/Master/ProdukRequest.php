<?php

namespace App\Http\Requests\Master;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Sara;

class ProdukRequest extends FormRequest
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
            'item_name' => ['required','min:3', new Sara],
            'hpp' => 'required',
            'harga_beli' => 'required',
            'harga_jual' => 'required',
            'satuan_jual' => 'required',
            'satuan_beli' => 'required',
            'konversi' => 'required',
            'is_active' => 'required',
        ];  
        
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
