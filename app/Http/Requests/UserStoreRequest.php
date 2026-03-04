<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'staffId' => 'required|string',
            'staffName' => 'required|string',
            'email' => 'required',
            'mobile' => 'required',
            'userType' => 'required',
            'status' => 'required',
            'password' => 'required|string|min:6'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
     $errors = $validator->errors();
     throw new HttpResponseException(response()->json([
         'status'=>'error',
         'message'=>$errors->first()
     ],422));
    }
}
