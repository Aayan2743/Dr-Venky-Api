<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class petRequest extends FormRequest
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
            'petname'=>'required|string',
            'petgender'=>'required|string|in:M,F',
            'petbread'=>'required|string',
            'category'=>'required|string',
            'dateOfBirth' => 'required|date|before_or_equal:today',
            'petAgeOptions' => 'required|in:Days,Months,Years',
            'age'=>'required|numeric',
        ];
    }

    public function attributes(){
        return [
           'petname'=>'Pet Name', 
           'petgender'=>'Pet Gender', 
           'petbread'=>'Pet Breeds', 
           'category'=>'Pet Category', 
           'age'=>'Pet Age', 
        ];
    }
    
    public function messages()
    {
        return [
            'petAgeOptions.in' => 'Please select Any one of given Days or Months or Years',
           
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) 
    {
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>'validation errors',
                'errors'=>$validator->errors()
            ])
        );
        
    }   
}
