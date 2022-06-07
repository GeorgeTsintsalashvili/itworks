<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BaseDataUpdateRequest;

use App\Http\Requests\UpdateSolidStateDriveRequest;
use App\Http\Requests\StoreSolidStateDriveRequest;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

use \App\Models\ControlPanel\SolidStateDrive;
use \App\Traits\BaseDataUpdatable;
use \App\Traits\RecordDeletable;
use App\Traits\MainImageUpdatable;
use App\Traits\MainImageUploadable;
use App\Traits\CarouselImageUploadable;
use App\Traits\Searchable;

class SolidStateDriveController extends Controller
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

      $queryBuilder = \DB::table('solid_state_drives');

      if(!$searchQueryValidator -> fails())
      {
        $searchQuery = $_POST['search-query'];

        $trimmedSearchQuery = $request -> input('search-query');

        $columns = ['id', 'mainImage', 'title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility', 'configuratorPart', 'uuid', 'warrantyDuration', 'warrantyId'];

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

      $priceRanges = \DB::table('price_configurations') -> select(['solidStateDriveMinPrice', 'solidStateDriveMaxPrice']) -> first();
      $productMinPrice = $priceRanges -> solidStateDriveMinPrice;
      $productMaxPrice = $priceRanges -> solidStateDriveMaxPrice;

      $formFactors = \DB::table('solid_state_drives_form_factors') -> get();
      $technologies = \DB::table('solid_state_drives_technologies') -> get();

      $warranties = \DB::table('warranties') -> get();

      $items -> each(function($item) use ($warranties){

                $title = $warranties -> where('id', $item -> warrantyId) -> first() -> warrantyPageTitle;

                $item -> warranty = $item -> warrantyDuration . " " . $title;
      });

      return \View::make('contents.controlPanel.solidStateDrives.index') -> with([

            'items' => $items,
            'paginationKey' => 'list-page',
            'paginator' => $paginator,
            'conditions' => $conditions,
            'stockTypes' => $stockTypes,
            'warranties' => $warranties,
            'minPrice' => $productMinPrice,
            'maxPrice' => $productMaxPrice,
            'searchQuery' => $searchQuery,
            'formFactors' => $formFactors,
            'technologies' => $technologies
      ]);
    }

    public function store(StoreSolidStateDriveRequest $request) // Store a newly created resource in storage
    {
      $data = $request -> validated();

      $response['stored'] = false;

      if($request -> file('mainImage') -> isValid())
      {
        $file = $request -> file('mainImage');

        $fileName = MainImageUploadable::uploadMainImage(SolidStateDrive::class, $file);

        if($fileName)
        {
          $data['mainImage'] = $fileName;
          $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);
          $data['configuratorPart'] = $request -> filled('configuratorPart') ? 1 : 0;

          $object = new SolidStateDrive();

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
                CarouselImageUploadable::uploadImage(SolidStateDrive::class, $object -> id, $carouselImage);
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

        $product = SolidStateDrive::findOrFail($id);

        $warranties = \DB::table('warranties') -> get();
        $images = \DB::table('solid_state_drives_images') -> where('solidStateDriveId', $id) -> get();

        $formFactors = \DB::table('solid_state_drives_form_factors') -> get();
        $technologies = \DB::table('solid_state_drives_technologies') -> get();

        return \View::make('contents.controlPanel.solidStateDrives.edit') -> with([

            'product' => $product,
            'warranties' => $warranties,
            'images' => $images,
            'productid' => $id,
            'formFactors' => $formFactors,
            'technologies' => $technologies
        ]);
      }

      catch(\Exception $e){

        return "404 Product Not Found";
      }
    }

    public function update(UpdateSolidStateDriveRequest $request) // Update the specified resource in storage
    {
      $data = $request -> validated();

      try{
            $recordId = $data['record-id'];
            $data = \Arr::except($data, ['record-id']);

            $record = SolidStateDrive::findOrFail($recordId);

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
      try
      {
        RecordDeletable::deleteRecord(SolidStateDrive::class, $id);

        // product unlink logic

        $systems = \DB::table('computer_parts') -> where('ssd_id', $id) -> get();

        if ($systems -> count())
        {
          foreach ($systems as $parts)
          {
            $dataToApply = ['ssd_id' => 0,
                            'ssd_stock_type_id' => 0,
                            'ssd_visibility' => 1,
                            'ssd_visibility_affected' => 0,
                            'ssd_unlinked' => 1,
                            'ssd_old_price' => 0,
                            'ssd_old_stock_type_id' => 0,
                            'ssd_price_affected' => 0,
                            'ssd_stock_type_id_affected' => 0];

            \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update($dataToApply);

            $computerUpdateData = ['affected' => 1];

            $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();

            if ($notInStockRecord)
            {
              $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
            }

            \DB::table('computers') -> where('id', $parts -> computer_id) -> update($computerUpdateData);
          }
        }

        return ['deleted' => true];
      }

      catch(\Exception $e)
      {
        return ['deleted' => false];
      }
    }

    public function updateBaseData(BaseDataUpdateRequest $request)
    {
      $data = $request -> validated();

      try
      {
        BaseDataUpdatable::updateBaseData(SolidStateDrive::class, $data);

        // part changes control

        $systems = \DB::table('computer_parts') -> where('ssd_id', $data['record-id']) -> get();

        if ($systems -> count())
        {
          $partNewPrice = $data['price'] - $data['discount'];
          $partNewStockTypeId = (int) $data['stockTypeId'];
          $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
          $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();
          $partVisibility = (int) $data['visibility'];

          foreach($systems as $parts)
          {
            if ($partVisibility != $parts -> ssd_visibility)
            {
              if (!$partVisibility)
              {
                $dataToApply = ['ssd_visibility' => 0,
                                'ssd_visibility_affected' => 1];

                \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update($dataToApply);

                $computerUpdateData = ['affected' => 1];

                $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();

                if ($notInStockRecord)
                {
                  $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
                }

                \DB::table('computers') -> where('id', $parts -> computer_id) -> update($computerUpdateData);
              }

              else
              {
                $dataToApply = ['ssd_visibility' => 1,
                                'ssd_visibility_affected' => 1];

                $computerUpdateData = ['affected' => 1];

                $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
                $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();

                $allPartsAreVisible = true;

                if ($notInStockRecord && $inStockRecord)
                {
                  $numOfIndicatedParts = 0;
                  $numOfInStockParts = 0;
                  $numOfManuallyIndicatedParts = 0;

                  if ($parts -> processor_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> processor_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if (!$parts -> processor_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> processor_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> motherboard_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> motherboard_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> motherboard_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> motherboard_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> ram_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> ram_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> ram_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> ram_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> video_card_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> video_card_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> video_card_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> video_card_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> hdd_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> hdd_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> hdd_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> hdd_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> ssd_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> ssd_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$partVisibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> ssd_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> power_supply_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> power_supply_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> power_supply_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> power_supply_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> case_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> case_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> case_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> case_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($parts -> cooler_id)
                  {
                    $numOfIndicatedParts++;

                    if ($parts -> cooler_stock_type_id == $inStockRecord -> id)
                    {
                      $numOfInStockParts++;
                    }

                    if ($allPartsAreVisible && !$parts -> cooler_visibility)
                    {
                      $allPartsAreVisible = false;
                    }
                  }

                  else if ($parts -> cooler_price)
                  {
                    $numOfManuallyIndicatedParts++;
                  }

                  if ($numOfIndicatedParts)
                  {
                    $difference = $numOfIndicatedParts - $numOfInStockParts;

                    if ($difference == 0 && $allPartsAreVisible && !$numOfManuallyIndicatedParts)
                    {
                      $computerUpdateData['stockTypeId'] = $inStockRecord -> id;
                    }

                    else
                    {
                      $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
                    }
                  }

                  else if ($numOfManuallyIndicatedParts)
                  {
                    $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
                  }
                }

                \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update($dataToApply);
                \DB::table('computers') -> where('id', $parts -> computer_id) -> update($computerUpdateData);
              }
            }

            if ($parts -> ssd_price != $partNewPrice)
            {
              $computers = \DB::table('computers') -> where('id', $parts -> computer_id) -> get();

              if ($computers -> count())
              {
                foreach($computers as $computer)
                {
                  $computerNewPrice = $computer -> price - $parts -> ssd_price + $partNewPrice;

                  \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update(['ssd_price' => $partNewPrice,
                                                                                                         'ssd_old_price' => $parts -> ssd_price,
                                                                                                         'ssd_price_affected' => 1]);
                  \DB::table('computers') -> where('id', $parts -> computer_id) -> update(['price' => $computerNewPrice, 'affected' => 1]);
                }
              }
            }

            if ($parts -> ssd_stock_type_id != $partNewStockTypeId)
            {
              \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update(['ssd_stock_type_id' => $partNewStockTypeId,
                                                                                                     'ssd_old_stock_type_id' => $parts -> ssd_stock_type_id,
                                                                                                     'ssd_stock_type_id_affected' => 1]);

              $computerUpdateData = ['affected' => 1];
              $allPartsAreVisible = true;
              $numOfManuallyIndicatedParts = 0;

              if ($notInStockRecord && $inStockRecord)
              {
                $numOfIndicatedParts = 0;
                $numOfInStockParts = 0;

                if ($parts -> processor_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> processor_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> processor_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> processor_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> motherboard_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> motherboard_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> motherboard_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> motherboard_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> ram_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> ram_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> ram_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> ram_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> video_card_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> video_card_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> video_card_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> video_card_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> hdd_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> hdd_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> hdd_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> hdd_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> ssd_id)
                {
                  $numOfIndicatedParts++;

                  if ($partNewStockTypeId == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> ssd_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> ssd_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> power_supply_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> power_supply_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> power_supply_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> power_supply_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> case_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> case_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> case_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> case_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($parts -> cooler_id)
                {
                  $numOfIndicatedParts++;

                  if ($parts -> cooler_stock_type_id == $inStockRecord -> id)
                  {
                    $numOfInStockParts++;
                  }

                  if (!$parts -> cooler_visibility)
                  {
                    $allPartsAreVisible = false;
                  }
                }

                else if ($parts -> cooler_price)
                {
                  $numOfManuallyIndicatedParts++;
                }

                if ($numOfIndicatedParts)
                {
                  $difference = $numOfIndicatedParts - $numOfInStockParts;

                  if ($difference == 0 && $allPartsAreVisible && !$numOfManuallyIndicatedParts)
                  {
                    $computerUpdateData['stockTypeId'] = $inStockRecord -> id;
                  }

                  else
                  {
                    $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
                  }
                }

                else if ($numOfManuallyIndicatedParts)
                {
                  $computerUpdateData['stockTypeId'] = $notInStockRecord -> id;
                }
              }

              \DB::table('computers') -> where('id', $parts -> computer_id) -> update($computerUpdateData);
            }
          }
        }

        return ['updated'  => true];
      }

      catch(\Exception $e)
      {
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

                $imagesSources = MainImageUpdatable::updateImage(SolidStateDrive::class, $recordId, $file);

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
                $className = SolidStateDrive::class;
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

      $classBaseName = class_basename(SolidStateDrive::class);
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
      $formFactorsPageKey = 'ssd-ff-page';

      $validator = \Validator::make($parameters, [
        $formFactorsPageKey => [
           'required',
            new NaturalNumber()
          ]
      ]);

      $formFactorsToView = 6;
      $formFactorsNum = \DB::table('solid_state_drives_form_factors') -> count();
      $formFactorsPage = $validator -> fails() ? 1 : (int) $parameters[$formFactorsPageKey];
      $formFactorsToSkip = ($formFactorsPage - 1) * $formFactorsToView;

      return \View::make('contents.controlPanel.solidStateDrives.parameters') -> with([
         'technologies' => \DB::table('solid_state_drives_technologies') -> get(),
         'formFactors' => \DB::table('solid_state_drives_form_factors') -> skip($formFactorsToSkip) -> take($formFactorsToView) -> orderBy('id', 'desc') -> get(),
         'ssdffPaginator' => \Paginator::build($formFactorsNum, 2, $formFactorsToView, $formFactorsPage, 2, 2),
         'ssdffPageKey' => $formFactorsPageKey
      ]);
    }

    public function storeSolidStateDriveFormFactor(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['ssd-ff-title' => 'required|string|min:1|max:100',
                                                  'onboard' => 'string|size:1']);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $parameters['onboard'] = isset($parameters['onboard']) ? 1 : 0;

         $dataToStore = ['formFactorTitle' => $parameters['ssd-ff-title'],
                         'onboard' => $parameters['onboard']];

         \DB::table('solid_state_drives_form_factors') -> insert($dataToStore);
      }

      return $data;
    }

    public function storeSolidStateDriveTechnology(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['ssd-technology' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $dataToStore = ['technologyTitle' => $parameters['ssd-technology']];

         \DB::table('solid_state_drives_technologies') -> insert($dataToStore);
      }

      return $data;
    }

    public function updateSolidStateDriveFormFactor(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['ssd-ff-title' => 'required|string|min:1|max:100',
                                                  'onboard' => 'required|string|size:1']);

      if(!$validator -> fails())
      {
         $data['updated'] = true;

         $onboard = abs((int) $parameters['onboard']);

         $parameters['onboard'] = $onboard ? 1 : 0;

         \DB::table('solid_state_drives_form_factors') -> where('id', $parameters['record-id']) -> update(['formFactorTitle' => $parameters['ssd-ff-title'], 'onboard' => $parameters['onboard']]);
      }

      return $data;
    }

    public function updateSolidStateDriveTechnology(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['ssd-technology' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['updated'] = true;

         \DB::table('solid_state_drives_technologies') -> where('id', $parameters['record-id']) -> update(['technologyTitle' => $parameters['ssd-technology']]);
      }

      return $data;
    }

    public function destroySolidStateDriveFormFactor($id)
    {
        try{

          \DB::table('solid_state_drives_form_factors') -> where('id', $id) -> delete();

          return ['deleted' => true];
        }

        catch(\Exception $e){

           return ['deleted' => false];
        }
    }

    public function destroySolidStateDriveTechnology($id)
    {
       try{

         \DB::table('solid_state_drives_technologies') -> where('id', $id) -> delete();

         return ['deleted' => true];
       }

       catch(\Exception $e){

          return ['deleted' => false];
       }
    }
}
