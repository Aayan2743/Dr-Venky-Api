<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class addDoctorRequest extends FormRequest
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
            'staff_name'=>'required',
            'designation'=>'required',
            'role'=>'required|numeric|in:2,3,4',
             'email' => 'required|email|max:255|unique:users,email',
            'phone'=>'required|numeric|digits:10|unique:users,phone',
            
        ];
    }

    public function attributes(){
        return[
            'staff_name'=>'Employee Name',
            'designation'=>'Employee Designation',
            'role'=>'Employee Role',

          
           
        ];

    }

    public function messages(){
        return[
            'email.exists'=>'Email Already Registered',
            
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
