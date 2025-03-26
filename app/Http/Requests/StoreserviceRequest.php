<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreserviceRequest extends FormRequest
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
            'service_name'=>'required|string',
        ];
    }

    public function attributes(){
        return [
            'service_name'=>'Service Name',
        ];

    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(
             response()->json([
                'status'=>false,
                'message'=>'Validation Errors',
                'errors'=>$validator->errors()
             ],422)
        );
    }
}
