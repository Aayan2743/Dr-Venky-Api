<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone'=>'required|digits:10|unique:users'
        ];
    }

   


    public function attributes(){
        return [
            'name'=>'Customer Name',
            'email'=>'Customer Email',
            'password'=>'Customer Password',
            'phone'=>'Customer Contact'
        ];
    }

    protected function failedValidation(Validator $validator)
{
    // throw new HttpResponseException(
    //     response()->json([
    //         'success' => false,
    //         'message' => 'Validation errors',
    //         'errors' => $validator->errors()
    //     ], 200)
    // );

            // return response()->json([
            //     'success' => false,
            //             'message' => 'Validation errors',
            //             'errors' => $validator->errors()
            // ]);


            // $response = response()->json([
            //     'success' => false,
            //     'message' => 'Validation errors occurred.',
            //     'errors' => $validator->errors(),
            // ], 200); // Use a status code like 422 or 200 depending on your requirement


            $response = response()->json([
                'errors' => $validator->errors(), // All errors (optional if needed)
                'success' => false,
                'message' => $validator->errors()->first(), // First validation error
            ], 200); // Use 422 for validation errors



            throw new HttpResponseException($response);
            // return $response->getContent();
            // die($response->getContent());


    }



}
