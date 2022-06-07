<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BaseDataUpdateRequest;

use App\Http\Requests\UpdateAccessoryRequest;
use App\Http\Requests\StoreAccessoryRequest;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

use \App\Models\ControlPanel\Accessory;
use \App\Traits\BaseDataUpdatable;
use \App\Traits\RecordDeletable;
use App\Traits\MainImageUpdatable;
use App\Traits\MainImageUploadable;
use App\Traits\CarouselImageUploadable;
use App\Traits\Searchable;

class AccessoryController extends Controller
{
    public function index(Request $request) // Display a listing of the resource
    {
      $parameters = $request -> only([ 'type-id', 'list-page' ]);
      $rules = [ 'type-id' => ['required', new PositiveIntegerOrZero ],
                 'list-page' => ['required', new NaturalNumber ] ];

      $searchQueryRule = ['search-query' => 'required|string|min:1|max:200'];
      $searchQueryValidator = \Validator::make($request -> only('search-query'), $searchQueryRule);

      $searchQuery = null;
      $listCurrentPage = 1;
      $selectedTypeId = 0;

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $selectedTypeId = (int) $parameters['type-id'];
        $listCurrentPage = (int) $parameters['list-page'];
      }

      $queryBuilder = \DB::table('accessories');

      if(!$searchQueryValidator -> fails())
      {
        $searchQuery = $_POST['search-query'];

        $trimmedSearchQuery = $request -> input('search-query');

        $columns = ['id', 'mainImage', 'title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility', 'uuid', 'warrantyDuration', 'warrantyId'];

        $indexedColumns = ['title', 'description'];

        $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $trimmedSearchQuery, $indexedColumns);
      }

      if($selectedTypeId != 0) $queryBuilder = $queryBuilder -> where('accessoryTypeId', $selectedTypeId);

      $numOfItemsToView = 9;
      $numOfItems = $queryBuilder -> count();

      $paginator = \Paginator::build($numOfItems, 2, $numOfItemsToView, $listCurrentPage, 2, 2);
      $itemsToSkip = ($paginator -> currentPage - 1) * $numOfItemsToView;

      $items = $queryBuilder -> orderBy('id', 'desc') -> skip($itemsToSkip) -> take($numOfItemsToView) -> get();
      $types = \DB::table('accessories_types') -> get();

      $conditions = \DB::table('conditions') -> get();
      $stockTypes = \DB::table('stock_types') -> get();
      $warranties = \DB::table('warranties') -> get();

      $priceRanges = \DB::table('price_configurations') -> select(['accessoryMinPrice', 'accessoryMaxPrice']) -> first();
      $productMinPrice = $priceRanges -> accessoryMinPrice;
      $productMaxPrice = $priceRanges -> accessoryMaxPrice;

      $types = \DB::table('accessories_types') -> get();

      $warranties = \DB::table('warranties') -> get();

      $items -> each(function($item) use ($warranties){

          $title = $warranties -> where('id', $item -> warrantyId) -> first() -> warrantyPageTitle;

          $item -> warranty = $item -> warrantyDuration . " " . $title;
      });

      return \View::make('contents.controlPanel.accessories.index') -> with([

            'items' => $items,
            'paginationKey' => 'list-page',
            'paginator' => $paginator,
            'conditions' => $conditions,
            'stockTypes' => $stockTypes,
            'warranties' => $warranties,
            'minPrice' => $productMinPrice,
            'maxPrice' => $productMaxPrice,
            'searchQuery' => $searchQuery,
            'typesKey' => 'type-id',
            'types' => $types,
            'selectedTypeId' => $selectedTypeId,
            'types' => $types
      ]);
    }

    public function store(StoreAccessoryRequest $request) // Store a newly created resource in storage
    {
      $data = $request -> validated();

      $response['stored'] = false;

      if($request -> file('mainImage') -> isValid())
      {
        $file = $request -> file('mainImage');

        $fileName = MainImageUploadable::uploadMainImage(Accessory::class, $file);

        if($fileName)
        {
          $data['mainImage'] = $fileName;
          $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);

          $hash = md5(uniqid(mt_rand(), true));
          $data['uuid'] = substr($hash, 0, 8) . substr($hash, 8, 4) . substr($hash, 12, 4) . substr($hash, 16, 4) . substr($hash, 20, 12);

          $object = new Accessory();

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
                CarouselImageUploadable::uploadImage(Accessory::class, $object -> id, $carouselImage);
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

        $product = Accessory::findOrFail($id);

        $warranties = \DB::table('warranties') -> get();
        $images = \DB::table('accessories_images') -> where('accessoryId', $id) -> get();

        $types = \DB::table('accessories_types') -> get();

        return \View::make('contents.controlPanel.accessories.edit') -> with([

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

    public function update(UpdateAccessoryRequest $request) // Update the specified resource in storage
    {
      $data = $request -> validated();

      try{
            $recordId = $data['record-id'];
            $data = \Arr::except($data, ['record-id']);

            $record = Accessory::findOrFail($recordId);

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

        RecordDeletable::deleteRecord(Accessory::class, $id);

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

        BaseDataUpdatable::updateBaseData(Accessory::class, $data);

        return ['updated' => true];
      }

      catch(\Exception $e){

        return ['updated' => false];
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

                $imagesSources = MainImageUpdatable::updateImage(Accessory::class, $recordId, $file);

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
                $className = Accessory::class;
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

      $classBaseName = class_basename(Accessory::class);
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
      $accTypesPageKey = 'acc-types-page';

      $validator = \Validator::make($parameters, [
        $accTypesPageKey => [
           'required',
            new NaturalNumber()
          ]
      ]);

      $accTypesToView = 6;
      $accTypesNum = \DB::table('accessories_types') -> count();
      $accTypesPage = $validator -> fails() ? 1 : (int) $parameters[$accTypesPageKey];
      $accTypesToSkip = ($accTypesPage - 1) * $accTypesToView;

      return \View::make('contents.controlPanel.accessories.parameters') -> with([
         'accessoriesTypes' => \DB::table('accessories_types') -> skip($accTypesToSkip) -> take($accTypesToView) -> orderBy('id', 'desc') -> get(),
         'accTypesPaginator' => \Paginator::build($accTypesNum, 2, $accTypesToView, $accTypesPage, 2, 2),
         'accTypesPageKey' => $accTypesPageKey
      ]);
    }

    public function storeAccessoryType(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['accessory-type-title' => 'required|string|min:1|max:100',
                                                  'type-key' => 'required|string|min:1|max:50',
                                                  'accessory-type-icon' => 'string|nullable|max:100']);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $dataToStore = ['typeTitle' => $parameters['accessory-type-title'],
                         'typeKey' => $parameters['type-key'] ];

         if($request -> filled('accessory-type-icon'))
         {
           $dataToStore['icon'] = $parameters['accessory-type-icon'];
         }

         \DB::table('accessories_types') -> insert($dataToStore);
      }

      return $data;
    }

    public function updateAccessoryType(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, [ 'accessory-type-title' => 'required|string|min:1|max:100',
                                                   'accessory-type-icon' => 'string|nullable|max:100',
                                                   'type-key' => 'required|string|min:1|max:50',
                                                   'record-id' => ['required', new NaturalNumber()] ]);

      if(!$validator -> fails())
      {
         $data['updated'] = true;

         $valuesToPass = [ 'typeTitle' => $parameters['accessory-type-title'],
                           'typeKey' => $parameters['type-key'] ];

         $valuesToPass['icon'] = $request -> filled('accessory-type-icon') ? $parameters['accessory-type-icon'] : 'fas fa-square';

         \DB::table('accessories_types') -> where('id', $parameters['record-id']) -> update($valuesToPass);
      }

      return $data;
    }

    public function destroyAccessoryType($id)
    {
      $data['deleted'] = false;

      $numOfAccessories = \DB::table('accessories') -> where('accessoryTypeId', $id) -> count();

      if($numOfAccessories == 0)
      {
        $data['deleted'] = true;

        \DB::table('accessories_types') -> where('id', $id) -> delete();
      }

      return $data;
    }
}
