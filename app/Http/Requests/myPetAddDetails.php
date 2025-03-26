<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class myPetAddDetails extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
            'petname'=>'required',
            'petgender'=>'required',
            'petbread'=>'required',
            'category'=>'required',
        ];
    }

    public function messages(){
        return [
            'petname'=>'Pet Name',
            'petgender'=>'Pet Gender',
            'petbread'=>'Pet Bread',
            'category'=>'Pet Type',
            
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(
                response([

                    'status'=>false,
                    'message'=>'Validation Errors',
                    'errors'=>$validator->errors()
                ]
                    

                )
        );
    }
}
