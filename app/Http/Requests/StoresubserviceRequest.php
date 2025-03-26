<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoresubserviceRequest extends FormRequest
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
            'subservice_name'=>'required|string',
            'service_id' => 'required|exists:services,id',
        ];
    }

    public function attributes(){
        return [
            'subservice_name'=>'Sub Service Name',
            'service_id'=>'Service id',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>'Validation Errors',
                'errors'=>$validator->errors()
            ])
        );
    }
}
