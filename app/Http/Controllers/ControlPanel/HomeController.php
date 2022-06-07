<?php

namespace App\Http\Controllers\ControlPanel;

use Illuminate\Http\Request;
use App\Rules\NaturalNumber;
use App\Http\Requests\PriceRangesUpdateRequest;
use App\Http\Requests\StockTypeUpdateRequest;
use App\Http\Requests\StockTypeInsertRequest;

class HomeController extends \App\Http\Controllers\Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $userData = \DB::table('control_panel_users') -> where('id', \Auth::id()) -> first(); // table name changed
        $notificationData = \DB::table('notification') -> first();

        return \View::make('contents.controlPanel.home.index') -> with([

            'userData' => $userData,
            'notificationData' => $notificationData
        ]);
    }

    // parameters methods

    public function parameters()
    {
      return \View::make('contents.controlPanel.home.parameters') -> with([
          'stockTypes' => \DB::table('stock_types') -> orderBy('id', 'desc') -> get(),
          'conditions' => \DB::table('conditions') -> orderBy('id', 'desc') -> get(),
          'priceRanges' => \DB::table('price_configurations') -> first()
      ]);
    }

    // update routes

    public function updatePriceRanges(PriceRangesUpdateRequest $request)
    {
      $validatedPrices = $request -> validated();

      \DB::table('price_configurations') -> update($validatedPrices);

      return ['success' => true];
    }

    public function updateStockType(StockTypeUpdateRequest $request)
    {
      $validatedData = $request -> validated();

      \DB::table('stock_types') -> where('id', $validatedData['record-id']) -> update(\Arr::except($validatedData, 'record-id'));

      return ['updated' => true];
    }

    public function updateConditionType(Request $request)
    {
       $response['updated'] = false;

       $input = $request -> only(['record-id', 'condition-title']);

       $rules = [ 'record-id' => ['required', new NaturalNumber],
                  'condition-title' => 'required|string|min:1|max:100' ];

       $validator = \Validator::make($input, $rules);

       if(!$validator -> fails())
       {
         $response['updated'] = true;

         \DB::table('conditions') -> where('id', $input['record-id']) -> update([ 'conditionTitle' => $input['condition-title'] ]);
       }

       return $response;
    }

    // destroy routes

    public function destroyStockType($id)
    {
      try{

          \DB::table('stock_types') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    public function destroyConditionType($id)
    {
      try{

          \DB::table('conditions') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    // store routes

    public function storeStockType(StockTypeInsertRequest $request)
    {
      $validatedData = $request -> validated();

      \DB::table('stock_types') -> insert($validatedData, 'record-id');

      return ['success' => true];
    }

    public function storeConditionType(Request $request)
    {
      $response['success'] = false;

      $input = $request -> only(['condition-title']);

      $rules = [ 'condition-title' => 'required|string|min:1|max:100' ];

      $validator = \Validator::make($input, $rules);

      if(!$validator -> fails())
      {
        $response['success'] = true;

        \DB::table('conditions') -> insert([ 'conditionTitle' => $input['condition-title'] ]);
      }

      return $response;
    }
}
