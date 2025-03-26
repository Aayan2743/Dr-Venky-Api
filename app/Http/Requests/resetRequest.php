<?php

namespace App\Http\Requests;

use ErrorException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class resetRequest extends FormRequest
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
            'otp'=>'required|exists:users,password_code',
            'password'=>'required|min:6'
            
        ];
    }


    public function messages(){
        return [
            'otp.exists'=>'Invalid Otp'
        ];
    }

 

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException (
            response()->json([
                'status'=>false,
                'message'=>'validation errors',
                'errors'=>$validator->errors()
            ])
        );
    }
    
}
