<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\NaturalNumber; // added
use Illuminate\Http\Exceptions\HttpResponseException; // added
use Illuminate\Contracts\Validation\Validator; // added

class PriceRangesUpdateRequest extends FormRequest
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
        return true; // modified
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'processorMinPrice' => [ 'required', new NaturalNumber ],
          'processorMaxPrice' => [ 'required', new NaturalNumber ],
          'memoryModuleMinPrice' => [ 'required', new NaturalNumber ],
          'memoryModuleMaxPrice' => [ 'required', new NaturalNumber ],
          'motherboardMinPrice' => [ 'required', new NaturalNumber ],
          'motherboardMaxPrice' => [ 'required', new NaturalNumber ],
          'videoCardMinPrice' => [ 'required', new NaturalNumber ],
          'videoCardMaxPrice' => [ 'required', new NaturalNumber ],
          'hardDiskDriveMinPrice' => [ 'required', new NaturalNumber ],
          'hardDiskDriveMaxPrice' => [ 'required', new NaturalNumber ],
          'solidStateDriveMinPrice' => [ 'required', new NaturalNumber ],
          'solidStateDriveMaxPrice' => [ 'required', new NaturalNumber ],
          'powerSupplyMinPrice' => [ 'required', new NaturalNumber ],
          'powerSupplyMaxPrice' => [ 'required', new NaturalNumber ],
          'processorCoolerMinPrice' => [ 'required', new NaturalNumber ],
          'processorCoolerMaxPrice' => [ 'required', new NaturalNumber ],
          'caseCoolerMinPrice' => [ 'required', new NaturalNumber ],
          'caseCoolerMaxPrice' => [ 'required', new NaturalNumber ],
          'opticalDiscDriveMinPrice' => [ 'required', new NaturalNumber ],
          'opticalDiscDriveMaxPrice' => [ 'required', new NaturalNumber ],
          'computerCaseMinPrice' => [ 'required', new NaturalNumber ],
          'computerCaseMaxPrice' => [ 'required', new NaturalNumber ],
          'uninterruptiblePowerSupplyMinPrice' => [ 'required', new NaturalNumber ],
          'uninterruptiblePowerSupplyMaxPrice' => [ 'required', new NaturalNumber ],
          'monitorMinPrice' => [ 'required', new NaturalNumber ],
          'monitorMaxPrice' => [ 'required', new NaturalNumber ],
          'peripheralMinPrice' => [ 'required', new NaturalNumber ],
          'peripheralMaxPrice' => [ 'required', new NaturalNumber ],
          'notebookChargerMinPrice' => [ 'required', new NaturalNumber ],
          'notebookChargerMaxPrice' => [ 'required', new NaturalNumber ],
          'computerMinPrice' => [ 'required', new NaturalNumber ],
          'computerMaxPrice' => [ 'required', new NaturalNumber ],
          'accessoryMinPrice' => [ 'required', new NaturalNumber ],
          'accessoryMaxPrice' => [ 'required', new NaturalNumber ],
          'networkDeviceMinPrice' => [ 'required', new NaturalNumber ],
          'networkDeviceMaxPrice' => [ 'required', new NaturalNumber ],
          'laptopMinPrice' => [ 'required', new NaturalNumber ],
          'laptopMaxPrice' => [ 'required', new NaturalNumber ]
        ];
    }
}
