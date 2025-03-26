<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class sendResetRequest extends FormRequest
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
             'email' => 'required|email|exists:users,email'
        ];
    }

    public function messages(){
        return [
            'email.exists' => 'The provided email address is not registered.',
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
