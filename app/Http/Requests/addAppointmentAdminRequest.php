<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;

class addAppointmentAdminRequest extends FormRequest
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
         'phone'=>'required|numeric',
            // 'dateofapp'=>'required|date_format:Y-m-d H:i:s|after_or_equal:today',
            // 'amount'=>'required|numeric',
            // 'payment'=>'required',
        ];
    }
    
    
    
    public function attributes(){
        return [
            // 'serviceName'=>'Service Name',
            // 'subserviceName'=>'Sub Service Name',
            // 'typeofpet'=>'Pet Name',
            // 'dateofapp'=>'Appointment Date & Time',
            // 'amount'=>'Fee',
            // 'payment'=>'Payment type',
            // 'petname'=>'Pet Name',
          
        ];
    }
    
    
     public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>'validation Errors',
                'errors'=>$validator->errors()
            ])
        );
    }
    
    
    
}
