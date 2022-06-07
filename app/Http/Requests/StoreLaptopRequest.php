<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException; // added
use Illuminate\Contracts\Validation\Validator; // added

use App\Rules\NaturalNumber; // added
use App\Rules\DecimalNumber; // added
use App\Rules\BinaryValue; // added
use App\Rules\PositiveIntegerOrZero; // added

class StoreLaptopRequest extends FormRequest
{
  /**
  * [failedValidation [Overriding the event validator for custom error response]]
  * @param  Validator $validator [description]
  * @return [object][object of various validation errors]
  */
     public function failedValidation(Validator $validator){ // added

      $errorMessage = ['stored' => false];

      $statusCode = 200;

      throw new HttpResponseException(response() -> json($errorMessage, $statusCode));
    }

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
        'title' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'description' => ['bail', 'required', 'string', 'min:1', 'max:65000'],
        'price' => ['bail', 'required', new NaturalNumber],
        'discount' => ['bail', 'required', new PositiveIntegerOrZero],
        'quantity' => ['bail', 'required', new PositiveIntegerOrZero],
        'seoDescription' => ['string', 'max:500', 'nullable'],
        'seoKeywords' => ['string', 'max:500', 'nullable'],
        'warrantyDuration' => ['bail', 'required', new NaturalNumber],
        'warrantyId' => ['bail', 'required', new NaturalNumber],
        'stockTypeId' => ['bail', 'required', new NaturalNumber],
        'conditionId' => ['bail', 'required', new NaturalNumber],
        'visibility' => ['bail', 'required', new BinaryValue],
        'mainImage' => ['bail', 'required', 'mimes:jpg,jpeg,png', 'max:1024'],
        'images' => ['array', 'max:6'],
        'images.*' => ['mimes:jpg,jpeg,png,bmp', 'max:1024'],
        'laptopSystemId' => ['bail', 'required', new NaturalNumber],
        'diagonal' => ['bail', 'required', new DecimalNumber],
        'memory' => ['bail', 'required', new NaturalNumber]
      ];
    }
}
