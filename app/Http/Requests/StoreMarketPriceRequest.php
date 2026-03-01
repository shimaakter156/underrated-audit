<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMarketPriceRequest extends FormRequest
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
            'LocationCode'=>'required',
            'ProductCode'=>'required',
            'CompanyPrice'=>'required',
            'MarketPrice'=>'required',
            'EntryDate'=>'required',
            'EntryAddress'=>'required',
            'Lat'=>'required',
            'Long'=>'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
     $errors = $validator->errors();
     throw new HttpResponseException(response()->json([
         'status'=>0,
         'message' => 'Validation failed',
         'errors' => $errors,
     ],422));

    }
}
