<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class appointmentconfirmedRequest extends FormRequest
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
             'doctor_name'=>'required',
            'aid'=>'required',
            'transaction_id'=>'required',
            // 'payment_mode'=>'required|in cash,upi,card',
            'payment_mode' => 'required|in:Cash,UPI,Card',
        ];
    }
    
    public function messages()
        {
            return [
                'payment_mode.in' => 'Please select a valid payment mode: Cash, UPI, or Card.',
            ];
        }
    
    
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>'validation errors',
                'errors'=>$validator->errors()
            ])
        );
    }
}
