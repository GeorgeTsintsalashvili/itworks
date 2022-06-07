<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\URL; // added
use Illuminate\Http\Exceptions\HttpResponseException; // added
use Illuminate\Contracts\Validation\Validator; // added

class ContactUpdateRequest extends FormRequest
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
         'companyName' => 'required|string|min:1|max:100',
         'phone' => 'required|string|min:1|max:100',
         'address' => 'required|string:200|min:1|max:200',
         'googleMapLink' => [ 'required', 'max:4096', new URL ],
         'facebookPageLink' => [ 'required', 'max:4096', new URL ],
         'email' => 'required|string|email',
         'delivery' => 'required|string:200|min:1|max:200',
         'schedule' => 'required|string:200|min:1|max:200'
      ];
  }
}
