<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class updateProfileRequest extends FormRequest
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

        //dd($this->user()->id);
        return [
            //

            'name'=>'required',
            'phone' => [
            'required',
            'digits:10',
            'numeric',
          
            Rule::unique('users', 'phone')->ignore($this->user()->id), // assuming you have the user ID here
              ],
            // 'email'=>'required|exists:users,email',
              'email' => [
            'required',
         
            Rule::unique('users', 'email')->ignore($this->user()->id), // Use this to ignore the current user's email as well
        ],
            'state'=>'required',
            'city'=>'required',
            'password'=>'nullable|min:6',
        ];
    }

    public function attributes(){
        
        return [
            'name'=>'Customer Name',
            'phone'=>'Customer phone',
            'email'=>'Customer Email',
            'state'=>'Customer State',
            'city'=>'Customer City',
            'password'=>'Customer Login Password',
        ];
      

    }


    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){

        throw new HttpResponseException(
            response()->json([
                'status'=>false,
                'message'=>$validator->errors()->first(),
                
            ])
        );
    }



}
