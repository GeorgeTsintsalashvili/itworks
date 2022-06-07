<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BaseDataUpdateRequest;

use App\Http\Requests\UpdateMotherboardRequest;
use App\Http\Requests\StoreMotherboardRequest;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

use \App\Models\ControlPanel\Motherboard;
use \App\Traits\BaseDataUpdatable;
use \App\Traits\RecordDeletable;
use App\Traits\MainImageUpdatable;
use App\Traits\MainImageUploadable;
use App\Traits\CarouselImageUploadable;
use App\Traits\Searchable;

class MotherboardController extends Controller
{
    public function index(Request $request) // Display a listing of the resource
    {
      $parameters = $request -> only([ 'manufacturer-id', 'list-page' ]);
      $rules = [ 'manufacturer-id' => ['required', new PositiveIntegerOrZero ],
                 'list-page' => ['required', new NaturalNumber ] ];

      $searchQueryRule = ['search-query' => 'required|string|min:1|max:200'];
      $searchQueryValidator = \Validator::make($request -> only('search-query'), $searchQueryRule);

      $searchQuery = null;
      $listCurrentPage = 1;
      $selectedManufacturerId = 0;

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $selectedManufacturerId = (int) $parameters['manufacturer-id'];
        $listCurrentPage = (int) $parameters['list-page'];
      }

      $queryBuilder = \DB::table('motherboards');

      if(!$searchQueryValidator -> fails())
      {
        $searchQuery = $_POST['search-query'];

        $trimmedSearchQuery = $request -> input('search-query');

        $columns = ['id', 'mainImage', 'title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility', 'configuratorPart', 'uuid', 'warrantyDuration', 'warrantyId'];

        $indexedColumns = ['title', 'description'];

        $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $trimmedSearchQuery, $indexedColumns);
      }

      if($selectedManufacturerId != 0) $queryBuilder = $queryBuilder -> where('manufacturerId', $selectedManufacturerId);

      $numOfItemsToView = 9;
      $numOfItems = $queryBuilder -> count();

      $paginator = \Paginator::build($numOfItems, 2, $numOfItemsToView, $listCurrentPage, 2, 2);
      $itemsToSkip = ($paginator -> currentPage - 1) * $numOfItemsToView;

      $items = $queryBuilder -> orderBy('id', 'desc') -> skip($itemsToSkip) -> take($numOfItemsToView) -> get();
      $manufacturers = \DB::table('motherboards_manufacturers') -> get();

      $conditions = \DB::table('conditions') -> get();
      $stockTypes = \DB::table('stock_types') -> get();
      $warranties = \DB::table('warranties') -> get();

      $priceRanges = \DB::table('price_configurations') -> select(['motherboardMinPrice', 'motherboardMaxPrice']) -> first();
      $productMinPrice = $priceRanges -> motherboardMinPrice;
      $productMaxPrice = $priceRanges -> motherboardMaxPrice;

      $sockets = \DB::table('cpu_sockets') -> get();
      $formFactors = \DB::table('case_form_factors') -> get();
      $memoryTypes = \DB::table('memory_modules_types') -> get();
      $manufacturers = \DB::table('motherboards_manufacturers') -> get();
      $ssdTypes = \DB::table('solid_state_drives_form_factors') -> get();
      $chipsets = \DB::table('chipsets') -> get();

      $warranties = \DB::table('warranties') -> get();

      $items -> each(function($item) use ($warranties){

                $title = $warranties -> where('id', $item -> warrantyId) -> first() -> warrantyPageTitle;

                $item -> warranty = $item -> warrantyDuration . " " . $title;
      });

      return \View::make('contents.controlPanel.motherboards.index') -> with([

            'items' => $items,
            'paginationKey' => 'list-page',
            'paginator' => $paginator,
            'conditions' => $conditions,
            'stockTypes' => $stockTypes,
            'warranties' => $warranties,
            'minPrice' => $productMinPrice,
            'maxPrice' => $productMaxPrice,
            'searchQuery' => $searchQuery,
            'manufacturersKey' => 'manufacturer-id',
            'manufacturers' => $manufacturers,
            'selectedManufacturerId' => $selectedManufacturerId,
            'sockets' => $sockets,
            'formFactors' => $formFactors,
            'memoryTypes' => $memoryTypes,
            'manufacturers' => $manufacturers,
            'ssdTypes' => $ssdTypes,
            'chipsets' => $chipsets
      ]);
    }

    public function store(StoreMotherboardRequest $request) // Store a newly created resource in storage
    {
      $data = $request -> validated();

      $response['stored'] = false;

      if($request -> file('mainImage') -> isValid())
      {
        $file = $request -> file('mainImage');

        $fileName = MainImageUploadable::uploadMainImage(Motherboard::class, $file);

        if($fileName)
        {
          $data['mainImage'] = $fileName;
          $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);
          $data['configuratorPart'] = $request -> filled('configuratorPart') ? 1 : 0;

          $hash = md5(uniqid(mt_rand(), true));
          $data['uuid'] = substr($hash, 0, 8) . substr($hash, 8, 4) . substr($hash, 12, 4) . substr($hash, 16, 4) . substr($hash, 20, 12);

          $solidStateDriveTypeId = $data['solidStateDriveTypeId'];
          $inputSolidStateDrivesTypes = explode(',', $solidStateDriveTypeId);

          $object = new Motherboard();

          $data = \Arr::except($data, ['images', 'solidStateDriveTypeId']);

          foreach($data as $key => $value)
          {
            $object -> $key = $data[$key];
          }

          $object -> save();

          foreach($inputSolidStateDrivesTypes as $solidStateDrive)
          {
            \DB::table('solid_state_drives_and_motherboards') -> insert(['motherboardId' => $object -> id, 'solidStateDriveTypeId' => $solidStateDrive]);
          }

          if($request -> has('images'))
          {
            $carouselImages = $request -> file('images');

            foreach($carouselImages as $carouselImage)
            {
              if($carouselImage -> isValid())
              {
                CarouselImageUploadable::uploadImage(Motherboard::class, $object -> id, $carouselImage);
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

        $product = Motherboard::findOrFail($id);

        $warranties = \DB::table('warranties') -> get();
        $images = \DB::table('motherboards_images') -> where('motherboardId', $id) -> get();

        $chipsets = \DB::table('chipsets') -> get();
        $sockets = \DB::table('cpu_sockets') -> get();
        $formFactors = \DB::table('case_form_factors') -> get();
        $memoryTypes = \DB::table('memory_modules_types') -> get();
        $manufacturers = \DB::table('motherboards_manufacturers') -> get();

        $supportedSolidStateDriveTypes = \DB::table('solid_state_drives_and_motherboards') -> select(['motherboardId', 'solidStateDriveTypeId', 'formFactorTitle'])
                                                                                           -> join('solid_state_drives_form_factors', 'solid_state_drives_form_factors.id', 'solidStateDriveTypeId')
                                                                                           -> where('motherboardId', $product -> id)
                                                                                           -> get();

        $solidStateDriveTypesRemaining = \DB::table('solid_state_drives_form_factors') -> whereNotIn('id', $supportedSolidStateDriveTypes -> pluck('solidStateDriveTypeId')) -> get();

        return \View::make('contents.controlPanel.motherboards.edit') -> with([

            'product' => $product,
            'warranties' => $warranties,
            'images' => $images,
            'productid' => $id,
            'chipsets' => $chipsets,
            'sockets' => $sockets,
            'formFactors' => $formFactors,
            'memoryTypes' => $memoryTypes,
            'manufacturers' => $manufacturers,
            'supportedSolidStateDriveTypes' => $supportedSolidStateDriveTypes,
            'solidStateDriveTypesRemaining' => $solidStateDriveTypesRemaining
        ]);
      }

      catch(\Exception $e){

        return "404 Product Not Found";
      }
    }

    public function update(UpdateMotherboardRequest $request) // Update the specified resource in storage
    {
      $data = $request -> validated();

      try{
            $recordId = $data['record-id'];
            $record = Motherboard::findOrFail($recordId);

            $solidStateDriveTypeId = $data['solidStateDriveTypeId'];
            $inputSolidStateDrivesTypes = explode(',', $solidStateDriveTypeId);
            $existentSolidStateDrivesTypes = \DB::table('solid_state_drives_and_motherboards') -> select(['solidStateDriveTypeId'])
                                                                                               -> where('motherboardId', $recordId)
                                                                                               -> get()
                                                                                               -> pluck('solidStateDriveTypeId')
                                                                                               -> toArray();

            $solidStateDrivesTypesToAdd = array_diff($inputSolidStateDrivesTypes, $existentSolidStateDrivesTypes);
            $solidStateDrivesTypesToDelete = array_diff($existentSolidStateDrivesTypes, $inputSolidStateDrivesTypes);

            if(count($solidStateDrivesTypesToAdd))
            {
              foreach($solidStateDrivesTypesToAdd as $type)

              \DB::table('solid_state_drives_and_motherboards') -> where('motherboardId', $recordId) -> insert(['motherboardId' => $recordId, 'solidStateDriveTypeId' => $type]);
            }

            if(count($solidStateDrivesTypesToDelete))
            {
              foreach($solidStateDrivesTypesToDelete as $type)

              \DB::table('solid_state_drives_and_motherboards') -> where('motherboardId', $recordId) -> where('solidStateDriveTypeId', $type) -> delete();
            }

            $data = \Arr::except($data, ['record-id', 'solidStateDriveTypeId']);

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
        RecordDeletable::deleteRecord(Motherboard::class, $id);

        // product unlink logic

        $systems = \DB::table('computer_parts') -> where('motherboard_id', $id) -> get();

        if ($systems -> count())
        {
          foreach ($systems as $parts)
          {
            $dataToApply = ['motherboard_id' => 0,
                            'motherboard_stock_type_id' => 0,
                            'motherboard_visibility' => 1,
                            'motherboard_visibility_affected' => 0,
                            'motherboard_unlinked' => 1,
                            'motherboard_old_price' => 0,
                            'motherboard_old_stock_type_id' => 0,
                            'motherboard_price_affected' => 0,
                            'motherboard_stock_type_id_affected' => 0];

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
        BaseDataUpdatable::updateBaseData(Motherboard::class, $data);

        // part changes control

        $systems = \DB::table('computer_parts') -> where('motherboard_id', $data['record-id']) -> get();

        if ($systems -> count())
        {
          $partNewPrice = $data['price'] - $data['discount'];
          $partNewStockTypeId = (int) $data['stockTypeId'];
          $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
          $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();
          $partVisibility = (int) $data['visibility'];

          foreach($systems as $parts)
          {
            if ($partVisibility != $parts -> motherboard_visibility)
            {
              if (!$partVisibility)
              {
                $dataToApply = ['motherboard_visibility' => 0,
                                'motherboard_visibility_affected' => 1];

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
                $dataToApply = ['motherboard_visibility' => 1,
                                'motherboard_visibility_affected' => 1];

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

                    if ($allPartsAreVisible && !$partVisibility)
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

                    if ($allPartsAreVisible && !$parts -> ssd_visibility)
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

            if ($parts -> motherboard_price != $partNewPrice)
            {
              $computers = \DB::table('computers') -> where('id', $parts -> computer_id) -> get();

              if ($computers -> count())
              {
                foreach($computers as $computer)
                {
                  $computerNewPrice = $computer -> price - $parts -> motherboard_price + $partNewPrice;

                  \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update(['motherboard_price' => $partNewPrice,
                                                                                                         'motherboard_old_price' => $parts -> motherboard_price,
                                                                                                         'motherboard_price_affected' => 1]);
                  \DB::table('computers') -> where('id', $parts -> computer_id) -> update(['price' => $computerNewPrice, 'affected' => 1]);
                }
              }
            }

            if ($parts -> motherboard_stock_type_id != $partNewStockTypeId)
            {
              \DB::table('computer_parts') -> where('computer_id', $parts -> computer_id) -> update(['motherboard_stock_type_id' => $partNewStockTypeId,
                                                                                                     'motherboard_old_stock_type_id' => $parts -> motherboard_stock_type_id,
                                                                                                     'motherboard_stock_type_id_affected' => 1]);

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

                  if ($partNewStockTypeId == $inStockRecord -> id)
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

                  if ($parts -> ssd_stock_type_id == $inStockRecord -> id)
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

                $imagesSources = MainImageUpdatable::updateImage(Motherboard::class, $recordId, $file);

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
                $className = Motherboard::class;
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

      $classBaseName = class_basename(Motherboard::class);
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

    // parameters methods

    public function parameters(Request $request)
    {
       $parameters = $request -> all();

       $formFactorsToView = 6;
       $formFactorsPage = 1;
       $formFactorsPageKey = 'form-factor-page';
       $formFactorsNum = \DB::table('case_form_factors') -> count();

       $manufacturersToView = 6;
       $manufacturersPage = 1;
       $manufacturersPageKey = 'manufacturers-page';
       $manufacturersNum = \DB::table('motherboards_manufacturers') -> count();

       $pageValidator = \Validator::make($parameters, [
         $manufacturersPageKey => [
            'required',
             new NaturalNumber()
           ],
         $formFactorsPageKey => [
            'required',
               new NaturalNumber()
           ]
       ]);

       if(!$pageValidator -> fails())
       {
         $formFactorsPage = (int) $parameters[$formFactorsPageKey];
         $manufacturersPage = (int) $parameters[$manufacturersPageKey];
       }

       $manufacturersToSkip = ($manufacturersPage - 1) * $manufacturersToView;
       $formFactorsToSkip = ($formFactorsPage - 1) * $formFactorsToView;

       return \View::make('contents.controlPanel.motherboards.parameters') -> with([
          'formFactors' => \DB::table('case_form_factors') -> orderBy('id', 'desc') -> skip($formFactorsToSkip) -> take($formFactorsToView) -> get(),
          'formFactorsPaginator' => \Paginator::build($formFactorsNum, 2, $formFactorsToView, $formFactorsPage, 2, 2),
          'formFactorsPageKey' => $formFactorsPageKey,
          'manufacturers' => \DB::table('motherboards_manufacturers') -> orderBy('id', 'desc') -> skip($manufacturersToSkip) -> take($manufacturersToView) -> get(),
          'manufacturersPaginator' => \Paginator::build($manufacturersNum, 2, $manufacturersToView, $manufacturersPage, 2, 2),
          'manufacturersPageKey' => $manufacturersPageKey,
       ]);
    }

    // manufacturer routes

    public function storeMotherboardManufacturer(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['manufacturer-title' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $dataToStore = ['manufacturerTitle' => $parameters['manufacturer-title']];

         \DB::table('motherboards_manufacturers') -> insert($dataToStore);
      }

      return $data;
    }

    public function updateMotherboardManufacturer(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['manufacturer-title' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['updated'] = true;

         \DB::table('motherboards_manufacturers') -> where('id', $parameters['record-id']) -> update(['manufacturerTitle' => $parameters['manufacturer-title']]);
      }

      return $data;
    }

    public function destroyMotherboardManufacturer($id)
    {
      $data['deleted'] = false;

      $numOfMoherboards = \DB::table('motherboards') -> where('manufacturerId', $id) -> count();

      if($numOfMoherboards == 0)
      {
        $data['deleted'] = true;

        \DB::table('motherboards_manufacturers') -> where('id', $id) -> delete();
      }

      return $data;
    }

    // form factors routes

    public function storeMotherboardFormFactor(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['form-factor-title' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['success'] = true;

         $dataToStore = ['formFactorTitle' => $parameters['form-factor-title']];

         \DB::table('case_form_factors') -> insert($dataToStore);
      }

      return $data;
    }

    public function updateMotherboardFormFactor(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['form-factor-title' => 'required|string|min:1|max:100']);

      if(!$validator -> fails())
      {
         $data['updated'] = true;

         \DB::table('case_form_factors') -> where('id', $parameters['record-id']) -> update(['formFactorTitle' => $parameters['form-factor-title']]);
      }

      return $data;
    }

    public function destroyMotherboardFormFactor($id)
    {
      $data['deleted'] = false;

      $numOfMoherboards = \DB::table('motherboards') -> where('formFactorId', $id) -> count();

      if($numOfMoherboards == 0)
      {
        $data['deleted'] = true;

        \DB::table('case_form_factors') -> where('id', $id) -> delete();
      }

      return $data;
    }
}
