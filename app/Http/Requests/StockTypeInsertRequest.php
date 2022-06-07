<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException; // added
use Illuminate\Contracts\Validation\Validator; // added

use App\Rules\NaturalNumber; // added
use App\Rules\BinaryValue; // added
use App\Rules\HexCode; // added

class StockTypeInsertRequest extends FormRequest
{
  /**
  * [failedValidation [Overriding the event validator for custom error response]]
  * @param  Validator $validator [description]
  * @return [object][object of various validation errors]
  */
    public function failedValidation(Validator $validator){ // added

    $errorMessage = ['success' => false];

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
        'stockTitle' => 'required|string|min:1|max:100',
        'statusColor' => [ 'required', new HexCode ],
        'configuratorPart' => [ new BinaryValue ],
        'enableAddToCartButton' => [ new BinaryValue ],
        'check' => [ new BinaryValue ]
      ];
  }
}
