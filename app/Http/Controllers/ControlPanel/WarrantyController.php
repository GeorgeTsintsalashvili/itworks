<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $managers = \DB::table('managers') -> get();

      return \View::make('contents.controlPanel.warranty.index') -> with(['managers' => $managers]);
    }

    public function displayWarranty(Request $request)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $fields =['title', 'price', 'quantity', 'warranty', 'manager', 'clientId', 'systemPart', 'incrp'];

      $parameters = $request -> only($fields);

      $rules = ['title' => ['required', 'array'],
                'price' => ['required', 'array'],
                'quantity' => ['required', 'array'],
                'warranty' => ['required', 'array'],
                'systemPart' => ['required', 'array'],
                'title.*' => ['required', 'string', 'min:1', 'max:300'],
                'warranty.*' => ['required', 'string', 'min:1', 'max:300'],
                'price.*' => ['required', new NaturalNumber],
                'quantity.*' => ['required', new NaturalNumber],
                'systemPart.*' => ['required', new BinaryValue],
                'manager' => ['required', 'string', 'min:1', 'max:200'],
                'clientId' => ['required', 'string', 'min:1', 'max:200'],
                'incrp' => ['required', 'string', 'regex:/^\-?(0|[1-9]\d*)$/']];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $titles = $parameters['title'];
        $prices = $parameters['price'];
        $quantities = $parameters['quantity'];
        $warranties = $parameters['warranty'];
        $systemParts = $parameters['systemPart'];
        $incrp = (int) $parameters['incrp'];

        $allowedSize = count($titles);
        $sizesAreTheSame = count($prices) == $allowedSize && count($quantities) == $allowedSize && count($warranties) == $allowedSize && count($systemParts) == $allowedSize;

        $partsPrice = 0;
        $systemPrice = 0;

        $products = [];
        $systemComponents = [];

        $clientId = null;
        $manager = null;

        if($sizesAreTheSame)
        {
          for($i = 0; $i < $allowedSize; $i++)
          {
            $price = $prices[$i] * $quantities[$i];
            $systemPart = (int) $systemParts[$i];

            $record = ['title' => $titles[$i],
                       'price' => $price,
                       'quantity' => $quantities[$i],
                       'warranty' => $warranties[$i]];

            if($systemPart)
            {
              $systemPrice += $price;

              $systemComponents[] = $record;
            }

            else
            {
              $partsPrice += $price;

              $products[] = $record;
            }
          }
        }

        $systemPrice = abs($systemPrice + $incrp);

        return \View::make('contents.controlPanel.warranty.partsWarranty') -> with([
          'products' => $products,
          'systemComponents' => $systemComponents,
          'partsPrice' => $partsPrice,
          'totalPrice' => $partsPrice + $systemPrice,
          'numOfSystemComponents' => count($systemComponents),
          'systemPrice' => $systemPrice,
          'contact' => \DB::table('contacts') -> first(),
          'manager' => $parameters['manager'],
          'clientId' => $parameters['clientId']
        ]);
      }
    }
}
