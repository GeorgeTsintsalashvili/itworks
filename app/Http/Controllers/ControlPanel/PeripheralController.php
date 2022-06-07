<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BaseDataUpdateRequest;

use App\Http\Requests\UpdatePeripheralRequest;
use App\Http\Requests\StorePeripheralRequest;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

use \App\Models\ControlPanel\Peripheral;
use \App\Traits\BaseDataUpdatable;
use \App\Traits\RecordDeletable;
use App\Traits\MainImageUpdatable;
use App\Traits\MainImageUploadable;
use App\Traits\CarouselImageUploadable;
use App\Traits\Searchable;

class PeripheralController extends Controller
{
    public function index(Request $request) // Display a listing of the resource
    {
      $parameters = $request -> only([ 'list-page' ]);
      $rules = [ 'list-page' => ['required', new NaturalNumber ] ];

      $searchQueryRule = ['search-query' => 'required|string|min:1|max:200'];
      $searchQueryValidator = \Validator::make($request -> only('search-query'), $searchQueryRule);

      $searchQuery = null;
      $listCurrentPage = 1;

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $listCurrentPage = (int) $parameters['list-page'];
      }

      $queryBuilder = \DB::table('peripherals');

      if(!$searchQueryValidator -> fails())
      {
        $searchQuery = $_POST['search-query'];

        $trimmedSearchQuery = $request -> input('search-query');

        $columns = ['id', 'mainImage', 'title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility', 'uuid', 'warrantyDuration', 'warrantyId'];

        $indexedColumns = ['title', 'description'];

        $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $trimmedSearchQuery, $indexedColumns);
      }

      $numOfItemsToView = 9;
      $numOfItems = $queryBuilder -> count();

      $paginator = \Paginator::build($numOfItems, 2, $numOfItemsToView, $listCurrentPage, 2, 2);
      $itemsToSkip = ($paginator -> currentPage - 1) * $numOfItemsToView;

      $items = $queryBuilder -> orderBy('id', 'desc') -> skip($itemsToSkip) -> take($numOfItemsToView) -> get();

      $conditions = \DB::table('conditions') -> get();
      $stockTypes = \DB::table('stock_types') -> get();
      $warranties = \DB::table('warranties') -> get();

      $priceRanges = \DB::table('price_configurations') -> select(['peripheralMinPrice', 'peripheralMaxPrice']) -> first();
      $productMinPrice = $priceRanges -> peripheralMinPrice;
      $productMaxPrice = $priceRanges -> peripheralMaxPrice;

      $types = \DB::table('peripherals_types') -> get();

      $warranties = \DB::table('warranties') -> get();

      $items -> each(function($item) use ($warranties){

                $title = $warranties -> where('id', $item -> warrantyId) -> first() -> warrantyPageTitle;

                $item -> warranty = $item -> warrantyDuration . " " . $title;
      });

      return \View::make('contents.controlPanel.peripherals.index') -> with([

            'items' => $items,
            'paginationKey' => 'list-page',
            'paginator' => $paginator,
            'conditions' => $conditions,
            'stockTypes' => $stockTypes,
            'warranties' => $warranties,
            'minPrice' => $productMinPrice,
            'maxPrice' => $productMaxPrice,
            'searchQuery' => $searchQuery,
            'types' => $types
      ]);
    }

    public function store(StorePeripheralRequest $request) // Store a newly created resource in storage
    {
      $data = $request -> validated();

      $response['stored'] = false;

      if($request -> file('mainImage') -> isValid())
      {
        $file = $request -> file('mainImage');

        $fileName = MainImageUploadable::uploadMainImage(Peripheral::class, $file);

        if($fileName)
        {
          $data['mainImage'] = $fileName;
          $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);

          $object = new Peripheral();

          $data = \Arr::except($data, ['images']);

          foreach($data as $key => $value)
          {
            $object -> $key = $data[$key];
          }

          $object -> save();

          if($request -> has('images'))
          {
            $carouselImages = $request -> file('images');

            foreach($carouselImages as $carouselImage)
            {
              if($carouselImage -> isValid())
              {
                CarouselImageUploadable::uploadImage(Peripheral::class, $object -> id, $carouselImage);
              }
            }
          }

          return ['stored' => true];
        }
      }

      return $response;
    }

    public function edit($id) // Show the form for editing the specified resource
    {
      try{

        $product = Peripheral::findOrFail($id);

        $warranties = \DB::table('warranties') -> get();
        $images = \DB::table('peripherals_images') -> where('peripheralId', $id) -> get();

        $types = \DB::table('peripherals_types') -> get();

        return \View::make('contents.controlPanel.peripherals.edit') -> with([

            'product' => $product,
            'warranties' => $warranties,
            'images' => $images,
            'productid' => $id,
            'types' => $types
        ]);
      }

      catch(\Exception $e){

        return "404 Product Not Found";
      }
    }

    public function update(UpdatePeripheralRequest $request) // Update the specified resource in storage
    {
      $data = $request -> validated();

      try{
            $recordId = $data['record-id'];
            $data = \Arr::except($data, ['record-id']);

            $record = Peripheral::findOrFail($recordId);

            $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);

            foreach($data as $key => $value)
            {
              $record -> $key = $data[$key];
            }

            $record -> save();

            return ['updated' => true];
      }

      catch(\Exception $e){

        return ['updated' => false];
      }
    }

    public function destroy($id) // Remove the specified resource from storage
    {
      try{

        RecordDeletable::deleteRecord(Peripheral::class, $id);

        return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    public function updateBaseData(BaseDataUpdateRequest $request)
    {
      $data = $request -> validated();

      try{

        BaseDataUpdatable::updateBaseData(Peripheral::class, $data);

        return ['updated'  => true];
      }

      catch(\Exception $e){

        return ['updated'  => false];
      }
    }

    // images control

    public function updateImage(Request $request)
    {
      $response = [ 'uploaded' => false ];

      if($request -> hasFile('image')) // if file is moved into temporary location by web server
      {
        $response['uploaded'] = true;
        $response['updated'] = false;

        try{
              $rules = [ 'image' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
                         'record-id' => ['required', new NaturalNumber] ];

              $validator = \Validator::make($request -> all(), $rules);

              if(!$validator -> fails())
              {
                $file = $request -> file('image');
                $recordId = $request -> input('record-id');

                $imagesSources = MainImageUpdatable::updateImage(Peripheral::class, $recordId, $file);

                if($imagesSources)
                {
                  $response['updated'] = true;

                  $response = array_merge($response, $imagesSources);
                }
              }

              else throw new \Exception;
        }

        catch(\Exception $e){

          $response['updated'] = false;
        }
      }

      return $response;
    }

    public function uploadImage(Request $request)
    {
      $response = [ 'uploaded' => false ];

      if($request -> hasFile('image')) // if file is moved into temporary location by web server
      {
        $response['uploaded'] = true;

        try{
              $rules = [ 'image' => ['required', 'mimes:jpg,jpeg,png', 'max:1024'],
                         'record-id' => ['required', new NaturalNumber] ];

              $validator = \Validator::make($request -> all(), $rules);

              if(!$validator -> fails())
              {
                $response['testPassed'] = true;

                $file = $request -> file('image');
                $className = Peripheral::class;
                $recordId = $request -> input('record-id');

                $controlData = CarouselImageUploadable::uploadImage($className, $recordId, $file);

                $response = array_merge($response, $controlData);
              }

              else throw new \Exception;
        }

        catch(\Exception $e){

          $response['testPassed'] = false;
        }
      }

      return $response;
    }

    public function destroyImage($id)
    {
      $response['destroyed'] = false;

      $classBaseName = class_basename(Peripheral::class);
      $imagesDirectoryName = lcfirst(\Str::plural($classBaseName));
      $imagesTableName = \Str::snake($imagesDirectoryName) . '_images';

      $recordQuery = \DB::table($imagesTableName) -> where('id', $id);

      if($recordQuery -> count() != 0)
      {
         $record = $recordQuery -> first();
         $fileName = $record -> image;

         $recordQuery -> delete();

         $slidesPath = realpath('./images/' . $imagesDirectoryName . '/slides');

         $originalImagesPath = $slidesPath . '/original/';
         $resizedImagesPath = $slidesPath . '/preview/';

         $originalImageFullName = $originalImagesPath . $fileName;
         $resizeImageFullName = $resizedImagesPath . $fileName;

         if(file_exists($originalImageFullName) && file_exists($resizeImageFullName))
         {
           \File::delete($originalImageFullName);
           \File::delete($resizeImageFullName);

           $response['destroyed'] = true;
         }
      }

      return $response;
    }

    // parameters routes

    public function parameters(Request $request)
    {
      $parameters = $request -> all();
      $peripheralsTypesPageKey = 'peripherals-types-page';

      $validator = \Validator::make($parameters, [
        $peripheralsTypesPageKey => [
           'required',
            new NaturalNumber()
          ]
      ]);

      $peripheralsTypesToView = 6;
      $peripheralsTypesNum = \DB::table('peripherals_types') -> count();
      $peripheralsTypesPage = $validator -> fails() ? 1 : (int) $parameters[$peripheralsTypesPageKey];
      $peripheralsTypesToSkip = ($peripheralsTypesPage - 1) * $peripheralsTypesToView;

      return \View::make('contents.controlPanel.peripherals.parameters') -> with([
         'peripheralsTypes' => \DB::table('peripherals_types') -> skip($peripheralsTypesToSkip) -> take($peripheralsTypesToView) -> orderBy('id', 'desc') -> get(),
         'peripheralsTypesPaginator' => \Paginator::build($peripheralsTypesNum, 2, $peripheralsTypesToView, $peripheralsTypesPage, 2, 2),
         'peripheralsTypesPageKey' => $peripheralsTypesPageKey
      ]);
    }

    public function storePeripheralType(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, [ 'peripheral-type-title' => 'required|string|min:1|max:100',
                                                   'peripheral-type-icon' => 'string|nullable|max:100' ]);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $dataToStore = ['typeTitle' => $parameters['peripheral-type-title']];

         if($request -> filled('peripheral-type-icon'))
         {
           $dataToStore['icon'] = $parameters['peripheral-type-icon'];
         }

         \DB::table('peripherals_types') -> insert($dataToStore);
      }

      return $data;
    }

    public function updatePeripheralType(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, [ 'peripheral-type-title' => 'required|string|min:1|max:100',
                                                   'peripheral-type-icon' => 'string|nullable|max:100',
                                                   'record-id' => ['required', new NaturalNumber] ]);
      if(!$validator -> fails())
      {
         $data['updated'] = true;

         $valuesToPass = [ 'typeTitle' => $parameters['peripheral-type-title'] ];

         $valuesToPass['icon'] = $request -> filled('peripheral-type-icon') ? $parameters['peripheral-type-icon'] : 'fas fa-square';

         \DB::table('peripherals_types') -> where('id', $parameters['record-id']) -> update($valuesToPass);
      }

      return $data;
    }

    public function destroyPeripheralType($id)
    {
      $data['deleted'] = false;

      $numOfAccessories = \DB::table('peripherals') -> where('typeId', $id) -> count();

      if($numOfAccessories == 0)
      {
        $data['deleted'] = true;

        \DB::table('peripherals_types') -> where('id', $id) -> delete();
      }

      return $data;
    }
}
