<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class preceiptionRequest extends FormRequest
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
            'aid'=>'required|unique:prescriptions,aaid',
            'temprature'=>'required|numeric',
            'medicines' => 'required',
            // 'pid'=>'required',
            // 'uid'=>'required',
            // 'dr_id'=>'required',
            'inhouse'=>'nullable',
            'grooming'=>'nullable',
            'lab'=>'nullable',
            'services'=>'nullable',
            'preceiption'=>'nullable',
        ];
    }


    public function messages(){
        return[
            'aid.unique'=>'Prescriptions Already Written....!'
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
