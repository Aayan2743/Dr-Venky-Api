<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class reportsRequest extends FormRequest
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
            'id'=>'required',
           'file_upload' => 'required|file|mimes:pdf|max:2048',

        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator) 
    {
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>'validation errors',
                'errors'=>$validator->errors()->first()
            ])
        );
        
    } 
}
