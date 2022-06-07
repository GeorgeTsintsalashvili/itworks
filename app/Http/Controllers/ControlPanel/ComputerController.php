<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use App\Models\ControlPanel\Computer;

use Illuminate\Http\Request;
use App\Http\Requests\BaseDataUpdateRequest;

use App\Http\Requests\UpdateComputerRequest;
use App\Http\Requests\StoreComputerRequest;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\PositiveIntegerOrZero;

use App\Traits\BaseDataUpdatable;
use App\Traits\RecordDeletable;
use App\Traits\MainImageUpdatable;
use App\Traits\MainImageUploadable;
use App\Traits\CarouselImageUploadable;
use App\Traits\Searchable;

class ComputerController extends Controller
{
    public function index(Request $request) // Display a listing of the resource
    {
      $parameters = $request -> only([ 'system-id', 'list-page', 'sort-by' ]);
      $rules = [ 'system-id' => ['required', new PositiveIntegerOrZero ],
                 'list-page' => ['required', new NaturalNumber ],
                 'sort-by' => ['required', new PositiveIntegerOrZero ] ];

      $searchQueryRule = ['search-query' => 'required|string|min:1|max:200'];
      $searchQueryValidator = \Validator::make($request -> only('search-query'), $searchQueryRule);

      $searchQuery = null;
      $listCurrentPage = 1;
      $selectedSystemId = 0;
      $selectedSortOrder = 0;
      $sortData = [
        0 => 'ცვლილების მიხედვით',
        1 => 'დამატების კლებადობით',
        2 => 'დამატების ზრდადობით',
        3 => 'ფასის კლებადობით',
        4 => 'ფასის ზრდადობით'
      ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $selectedSystemId = (int) $parameters['system-id'];
        $listCurrentPage = (int) $parameters['list-page'];
        $selectedSortOrder = (int) $parameters['sort-by'];
      }

      $queryBuilder = \DB::table('computers');

      if(!$searchQueryValidator -> fails())
      {
        $searchQuery = $_POST['search-query'];

        $trimmedSearchQuery = $request -> input('search-query');

        $columns = ['id', 'mainImage', 'title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility', 'isOffer', 'uuid', 'warrantyDuration', 'warrantyId', 'affected'];

        $indexedColumns = ['title', 'description'];

        $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $trimmedSearchQuery, $indexedColumns);
      }

      if($selectedSystemId != 0) $queryBuilder = $queryBuilder -> where('seriesId', $selectedSystemId);

      $numOfItemsToView = 9;
      $numOfItems = $queryBuilder -> count();

      $paginator = \Paginator::build($numOfItems, 2, $numOfItemsToView, $listCurrentPage, 2, 2);
      $itemsToSkip = ($paginator -> currentPage - 1) * $numOfItemsToView;

      if ($selectedSortOrder == 0)
      {
        $queryBuilder = $queryBuilder -> orderBy('affected', 'desc');
      }

      else if ($selectedSortOrder == 1)
      {
        $queryBuilder = $queryBuilder -> orderBy('id', 'desc');
      }

      else if ($selectedSortOrder == 2)
      {
        $queryBuilder = $queryBuilder -> orderBy('id', 'asc');
      }

      else if ($selectedSortOrder == 3)
      {
        $queryBuilder = $queryBuilder -> orderBy('price', 'desc');
      }

      else if ($selectedSortOrder == 4)
      {
        $queryBuilder = $queryBuilder -> orderBy('price', 'asc');
      }

      $items = $queryBuilder -> skip($itemsToSkip) -> take($numOfItemsToView) -> get();
      $systems = \DB::table('cpu_series') -> get();
      $computerGraphics = \DB::table('computer_graphics') -> get();

      $conditions = \DB::table('conditions') -> get();
      $stockTypes = \DB::table('stock_types') -> get();
      $warranties = \DB::table('warranties') -> get();

      $priceRanges = \DB::table('price_configurations') -> select(['computerMinPrice', 'computerMaxPrice']) -> first();
      $productMinPrice = $priceRanges -> computerMinPrice;
      $productMaxPrice = $priceRanges -> computerMaxPrice;

      $warranties = \DB::table('warranties') -> get();

      $items -> each(function($item) use ($warranties){

          $title = $warranties -> where('id', $item -> warrantyId) -> first() -> warrantyPageTitle;

          $item -> warranty = $item -> warrantyDuration . " " . $title;
      });

      $headerTemplates = \DB::table('computer_header_description_templates') -> get();
      $footerTemplates = \DB::table('computer_footer_description_templates') -> get();
      $stockCheckParts = [];

      foreach ($stockTypes as $record)
      {
        $stockCheckParts[] = $record -> id . '-' . $record -> check;
      }

      $stockCheckStr = implode(':', $stockCheckParts);

      return \View::make('contents.controlPanel.computers.index') -> with([

            'items' => $items,
            'paginationKey' => 'list-page',
            'paginator' => $paginator,
            'conditions' => $conditions,
            'stockTypes' => $stockTypes,
            'warranties' => $warranties,
            'searchQuery' => $searchQuery,
            'minPrice' => $productMinPrice,
            'maxPrice' => $productMaxPrice,
            'systemsKey' => 'system-id',
            'systems' => $systems,
            'computerGraphics' => $computerGraphics,
            'selectedSystemId' => $selectedSystemId,
            'headerTemplates' => $headerTemplates,
            'footerTemplates' => $footerTemplates,
            'stockCheckStr' => $stockCheckStr,
            'sortData' => $sortData,
            'sortKey' => 'sort-by',
            'selectedSortOrder' => $selectedSortOrder
      ]);
    }

    public function store(Request $request) // Store a newly created resource in storage
    {
      $rules = [
        'title' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'description' => ['bail', 'required', 'string', 'min:1', 'max:2000000'],
        'price' => ['bail', 'required', new NaturalNumber],
        'discount' => ['bail', 'required', new PositiveIntegerOrZero],
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
        'isOffer' => ['bail', 'string', 'nullable'],
        'cpu' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'gpuTitle' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'memory' => ['bail', 'required', new NaturalNumber],
        'videoMemory' => ['bail', 'required', new PositiveIntegerOrZero],
        'seriesId' => ['bail', 'required', new NaturalNumber],
        'computerGraphicsId' => ['bail', 'required', new NaturalNumber],
        'solidStateDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'hardDiscDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'quantity' => ['bail', 'required', new PositiveIntegerOrZero]
      ];

      $data = $request -> only(['title', 'description', 'price', 'price', 'discount', 'seoDescription', 'seoKeywords', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'visibility', 'mainImage', 'images', 'isOffer', 'cpu', 'gpuTitle', 'memory', 'videoMemory', 'seriesId', 'computerGraphicsId', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'quantity']);

      $validator = \Validator::make($data, $rules);

      $response['stored'] = false;

      if(!$validator -> fails())
      {
        $file = $request -> file('mainImage');

        $fileName = MainImageUploadable::uploadMainImage(Computer::class, $file);

        if($fileName)
        {
          $data['mainImage'] = $fileName;
          $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);
          $data['isOffer'] = $request -> filled('isOffer') ? 1 : 0;

          $hash = md5(uniqid(mt_rand(), true));
          $data['uuid'] = substr($hash, 0, 8) . substr($hash, 8, 4) . substr($hash, 12, 4) . substr($hash, 16, 4) . substr($hash, 20, 12);

          $object = new Computer();

          $data = \Arr::except($data, ['images']);

          foreach($data as $key => $value)
          {
            $object -> $key = $data[$key];
          }

          $object -> save();

          $partsRules = [ 'header-template' => 'nullable|string|max:2000000',
                          'footer-template' => 'nullable|string|max:2000000',
                          'part-processor-title' => 'nullable|string|max:300',
                          'part-processor-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-processor-id' => ['required', new PositiveIntegerOrZero],
                          'part-processor-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-motherboard-title' => 'nullable|string|max:300',
                          'part-motherboard-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-motherboard-id' => ['required', new PositiveIntegerOrZero],
                          'part-motherboard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-ram-title' => 'nullable|string|max:300',
                          'part-ram-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-ram-id' => ['required', new PositiveIntegerOrZero],
                          'part-ram-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-videocard-title' => 'nullable|string|max:300',
                          'part-videocard-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-videocard-id' => ['required', new PositiveIntegerOrZero],
                          'part-videocard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-hdd-title' => 'nullable|string|max:300',
                          'part-hdd-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-hdd-id' => ['required', new PositiveIntegerOrZero],
                          'part-hdd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-ssd-title' => 'nullable|string|max:300',
                          'part-ssd-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-ssd-id' => ['required', new PositiveIntegerOrZero],
                          'part-ssd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-power-supply-title' => 'nullable|string|max:300',
                          'part-power-supply-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-power-supply-id' => ['required', new PositiveIntegerOrZero],
                          'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-case-title' => 'nullable|string|max:300',
                          'part-case-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-case-id' => ['required', new PositiveIntegerOrZero],
                          'part-case-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                          'part-cooler-title' => 'nullable|string|max:300',
                          'part-cooler-price' => ['nullable', new PositiveIntegerOrZero],
                          'part-cooler-id' => ['required', new PositiveIntegerOrZero],
                          'part-cooler-stock-type-id' => ['required', new PositiveIntegerOrZero] ];

          $partsParameters = $request -> only(['header-template', 'footer-template',
                                               'part-processor-title', 'part-processor-price', 'part-processor-id', 'part-processor-stock-type-id',
                                               'part-motherboard-title', 'part-motherboard-price', 'part-motherboard-id', 'part-motherboard-stock-type-id',
                                               'part-ram-title', 'part-ram-price', 'part-ram-id', 'part-ram-stock-type-id',
                                               'part-videocard-title', 'part-videocard-price', 'part-videocard-id', 'part-videocard-stock-type-id',
                                               'part-hdd-title', 'part-hdd-price', 'part-hdd-id', 'part-hdd-stock-type-id',
                                               'part-ssd-title', 'part-ssd-price', 'part-ssd-id', 'part-ssd-stock-type-id',
                                               'part-power-supply-title', 'part-power-supply-price', 'part-power-supply-id', 'part-power-supply-stock-type-id',
                                               'part-case-title', 'part-case-price', 'part-case-id', 'part-case-stock-type-id',
                                               'part-cooler-title', 'part-cooler-price', 'part-cooler-id', 'part-cooler-stock-type-id']);

          $partsValidator = \Validator::make($partsParameters, $partsRules);

          if (!$partsValidator -> fails())
          {
            $partsDataToInsert = [
              'computer_id' => $object -> id,
              'header_template' => $partsParameters['header-template'],
              'footer_template' => $partsParameters['footer-template'],
              'processor_id' => $partsParameters['part-processor-id'],
              'motherboard_id' => $partsParameters['part-motherboard-id'],
              'ram_id' => $partsParameters['part-ram-id'],
              'video_card_id' => $partsParameters['part-videocard-id'],
              'hdd_id' => $partsParameters['part-hdd-id'],
              'ssd_id' => $partsParameters['part-ssd-id'],
              'power_supply_id' => $partsParameters['part-power-supply-id'],
              'case_id' => $partsParameters['part-case-id'],
              'cooler_id' => $partsParameters['part-cooler-id'],
              'processor_stock_type_id' => $partsParameters['part-processor-stock-type-id'],
              'motherboard_stock_type_id' => $partsParameters['part-motherboard-stock-type-id'],
              'ram_stock_type_id' => $partsParameters['part-ram-stock-type-id'],
              'video_card_stock_type_id' => $partsParameters['part-videocard-stock-type-id'],
              'hdd_stock_type_id' => $partsParameters['part-hdd-stock-type-id'],
              'ssd_stock_type_id' => $partsParameters['part-ssd-stock-type-id'],
              'power_supply_stock_type_id' => $partsParameters['part-power-supply-stock-type-id'],
              'case_stock_type_id' => $partsParameters['part-case-stock-type-id'],
              'cooler_stock_type_id' => $partsParameters['part-cooler-stock-type-id']
            ];

            $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
            $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();
            $numOfFilledParts = 0;
            $notInStockPartsNum = 0;

            if ($partsParameters['part-processor-title'] && $partsParameters['part-processor-price'])
            {
              $partsDataToInsert['processor_title'] = $partsParameters['part-processor-title'];
              $partsDataToInsert['processor_price'] = $partsParameters['part-processor-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-processor-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-motherboard-title'] && $partsParameters['part-motherboard-price'])
            {
              $partsDataToInsert['motherboard_title'] = $partsParameters['part-motherboard-title'];
              $partsDataToInsert['motherboard_price'] = $partsParameters['part-motherboard-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-motherboard-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-ram-title'] && $partsParameters['part-ram-price'])
            {
              $partsDataToInsert['ram_title'] = $partsParameters['part-ram-title'];
              $partsDataToInsert['ram_price'] = $partsParameters['part-ram-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-ram-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-videocard-title'] && $partsParameters['part-videocard-price'])
            {
              $partsDataToInsert['video_card_title'] = $partsParameters['part-videocard-title'];
              $partsDataToInsert['video_card_price'] = $partsParameters['part-videocard-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-videocard-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-hdd-title'] && $partsParameters['part-hdd-price'])
            {
              $partsDataToInsert['hdd_title'] = $partsParameters['part-hdd-title'];
              $partsDataToInsert['hdd_price'] = $partsParameters['part-hdd-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-hdd-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-ssd-title'] && $partsParameters['part-ssd-price'])
            {
              $partsDataToInsert['ssd_title'] = $partsParameters['part-ssd-title'];
              $partsDataToInsert['ssd_price'] = $partsParameters['part-ssd-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-ssd-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-power-supply-title'] && $partsParameters['part-power-supply-price'])
            {
              $partsDataToInsert['power_supply_title'] = $partsParameters['part-power-supply-title'];
              $partsDataToInsert['power_supply_price'] = $partsParameters['part-power-supply-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-power-supply-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-case-title'] && $partsParameters['part-case-price'])
            {
              $partsDataToInsert['case_title'] = $partsParameters['part-case-title'];
              $partsDataToInsert['case_price'] = $partsParameters['part-case-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-case-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($partsParameters['part-cooler-title'] && $partsParameters['part-cooler-price'])
            {
              $partsDataToInsert['cooler_title'] = $partsParameters['part-cooler-title'];
              $partsDataToInsert['cooler_price'] = $partsParameters['part-cooler-price'];

              $numOfFilledParts++;

              $partStockTypeId = (int) $partsParameters['part-cooler-stock-type-id'];

              if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
              {
                $notInStockPartsNum++;
              }
            }

            if ($inStockRecord && $notInStockRecord)
            {
              if ($numOfFilledParts)
              {
                if ($notInStockPartsNum)
                {
                  $object -> stockTypeId = $notInStockRecord -> id;
                }

                else
                {
                  $object -> stockTypeId = $inStockRecord -> id;
                }

                $object -> save();
              }
            }

            \DB::table('computer_parts') -> insert($partsDataToInsert);
          }

          if($request -> has('images'))
          {
            $carouselImages = $request -> file('images');

            foreach($carouselImages as $carouselImage)
            {
              if($carouselImage -> isValid())
              {
                CarouselImageUploadable::uploadImage(Computer::class, $object -> id, $carouselImage);
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

        $product = Computer::findOrFail($id);

        $warranties = \DB::table('warranties') -> get();
        $stockTypes = \DB::table('stock_types') -> get();

        $series = \DB::table('cpu_series') -> get();
        $graphics = \DB::table('computer_graphics') -> get();
        $images = \DB::table('computers_images') -> where('computerId', $id) -> get();

        $headerTemplates = \DB::table('computer_header_description_templates') -> get();
        $footerTemplates = \DB::table('computer_footer_description_templates') -> get();
        $stockCheckParts = [];
        $stockKeyValueParts = [];

        foreach ($stockTypes as $record)
        {
          $stockCheckParts[] = $record -> id . '-' . $record -> check;
          $stockKeyValueParts[$record -> id] = ['title' => $record -> stockTitle, 'requiresUpdate' => $record -> check];
        }

        $stockCheckStr = implode(':', $stockCheckParts);
        $computerParts = \DB::table('computer_parts') -> where('computer_id', $id) -> first();

        return \View::make('contents.controlPanel.computers.edit') -> with([
            'product' => $product,
            'warranties' => $warranties,
            'images' => $images,
            'series' => $series,
            'graphics' => $graphics,
            'productid' => $id,
            'headerTemplates' => $headerTemplates,
            'footerTemplates' => $footerTemplates,
            'stockCheckStr' => $stockCheckStr,
            'computerParts' => $computerParts,
            'stockKeyValueParts' => $stockKeyValueParts
        ]);
      }

      catch(\Exception $e){

        return "404 Product Not Found";
      }
    }

    public function update(Request $request) // Update the specified resource in storage
    {
      $rules = [
        'record-id' => ['bail', 'required', new NaturalNumber],
        'description' => ['bail', 'required', 'string', 'min:1', 'max:2000000'],
        'seoDescription' => ['string', 'max:500', 'nullable'],
        'seoKeywords' => ['string', 'max:500', 'nullable'],
        'cpu' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'gpuTitle' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'memory' => ['bail', 'required', new NaturalNumber],
        'videoMemory' => ['bail', 'required', new PositiveIntegerOrZero],
        'seriesId' => ['bail', 'required', new NaturalNumber],
        'computerGraphicsId' => ['bail', 'required', new NaturalNumber],
        'solidStateDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'hardDiscDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'warrantyDuration' => ['bail', 'required', new NaturalNumber],
        'warrantyId' => ['bail', 'required', new NaturalNumber],
        'price' => ['bail', 'required', new NaturalNumber],
        'discount' => ['bail', 'required', new PositiveIntegerOrZero],
        'quantity' => ['bail', 'required', new PositiveIntegerOrZero]
      ];

      $data = $request -> only(['record-id',
                                'description',
                                'seoDescription',
                                'seoKeywords',
                                'cpu',
                                'gpuTitle',
                                'memory',
                                'videoMemory',
                                'seriesId',
                                'computerGraphicsId',
                                'solidStateDriveCapacity',
                                'hardDiscDriveCapacity',
                                'warrantyDuration',
                                'warrantyId',
                                'price',
                                'discount',
                                'quantity']);

      $validator = \Validator::make($data, $rules);

      if (!$validator -> fails())
      {
        try
        {
          $ssdCapacity = (int) $data['solidStateDriveCapacity'];
          $hddCapacity = (int) $data['hardDiscDriveCapacity'];

          $atLeastOneIsNotZero = $hddCapacity || $ssdCapacity;

          if ($atLeastOneIsNotZero)
          {
            $recordId = $data['record-id'];
            $data = \Arr::except($data, ['record-id']);

            $record = Computer::findOrFail($recordId);

            $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);

            foreach($data as $key => $value)
            {
              $record -> $key = $data[$key];
            }

            $record -> save();

            $partsRules = [ 'header-template' => 'nullable|string|max:2000000',
                            'footer-template' => 'nullable|string|max:2000000',
                            'part-processor-title' => 'nullable|string|max:300',
                            'part-processor-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-processor-id' => ['required', new PositiveIntegerOrZero],
                            'part-processor-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-motherboard-title' => 'nullable|string|max:300',
                            'part-motherboard-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-motherboard-id' => ['required', new PositiveIntegerOrZero],
                            'part-motherboard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-ram-title' => 'nullable|string|max:300',
                            'part-ram-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-ram-id' => ['required', new PositiveIntegerOrZero],
                            'part-ram-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-videocard-title' => 'nullable|string|max:300',
                            'part-videocard-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-videocard-id' => ['required', new PositiveIntegerOrZero],
                            'part-videocard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-hdd-title' => 'nullable|string|max:300',
                            'part-hdd-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-hdd-id' => ['required', new PositiveIntegerOrZero],
                            'part-hdd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-ssd-title' => 'nullable|string|max:300',
                            'part-ssd-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-ssd-id' => ['required', new PositiveIntegerOrZero],
                            'part-ssd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-power-supply-title' => 'nullable|string|max:300',
                            'part-power-supply-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-power-supply-id' => ['required', new PositiveIntegerOrZero],
                            'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-case-title' => 'nullable|string|max:300',
                            'part-case-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-case-id' => ['required', new PositiveIntegerOrZero],
                            'part-case-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                            'part-cooler-title' => 'nullable|string|max:300',
                            'part-cooler-price' => ['nullable', new PositiveIntegerOrZero],
                            'part-cooler-id' => ['required', new PositiveIntegerOrZero],
                            'part-cooler-stock-type-id' => ['required', new PositiveIntegerOrZero] ];

            $partsParameters = $request -> only(['header-template', 'footer-template',
                                                 'part-processor-title', 'part-processor-price', 'part-processor-id', 'part-processor-stock-type-id',
                                                 'part-motherboard-title', 'part-motherboard-price', 'part-motherboard-id', 'part-motherboard-stock-type-id',
                                                 'part-ram-title', 'part-ram-price', 'part-ram-id', 'part-ram-stock-type-id',
                                                 'part-videocard-title', 'part-videocard-price', 'part-videocard-id', 'part-videocard-stock-type-id',
                                                 'part-hdd-title', 'part-hdd-price', 'part-hdd-id', 'part-hdd-stock-type-id',
                                                 'part-ssd-title', 'part-ssd-price', 'part-ssd-id', 'part-ssd-stock-type-id',
                                                 'part-power-supply-title', 'part-power-supply-price', 'part-power-supply-id', 'part-power-supply-stock-type-id',
                                                 'part-case-title', 'part-case-price', 'part-case-id', 'part-case-stock-type-id',
                                                 'part-cooler-title', 'part-cooler-price', 'part-cooler-id', 'part-cooler-stock-type-id']);

            $partsValidator = \Validator::make($partsParameters, $partsRules);

            if (!$partsValidator -> fails())
            {
              $systemParts = \DB::table('computer_parts') -> where('computer_id', $recordId) -> first();

              $partsDataToUpdate = [
                'processor_title' => null,
                'processor_price' => 0,
                'motherboard_title' => null,
                'motherboard_price' => 0,
                'ram_title' => null,
                'ram_price' => 0,
                'video_card_title' => null,
                'video_card_price' => 0,
                'hdd_title' => null,
                'hdd_price' => 0,
                'ssd_title' => null,
                'ssd_price' => 0,
                'power_supply_title' => null,
                'power_supply_price' => 0,
                'case_title' => null,
                'case_price' => 0,
                'cooler_title' => null,
                'cooler_price' => 0,
                'header_template' => $partsParameters['header-template'],
                'footer_template' => $partsParameters['footer-template'],
                'processor_id' => $partsParameters['part-processor-id'],
                'motherboard_id' => $partsParameters['part-motherboard-id'],
                'ram_id' => $partsParameters['part-ram-id'],
                'video_card_id' => $partsParameters['part-videocard-id'],
                'hdd_id' => $partsParameters['part-hdd-id'],
                'ssd_id' => $partsParameters['part-ssd-id'],
                'power_supply_id' => $partsParameters['part-power-supply-id'],
                'case_id' => $partsParameters['part-case-id'],
                'cooler_id' => $partsParameters['part-cooler-id'],
                'processor_stock_type_id' => $partsParameters['part-processor-stock-type-id'],
                'motherboard_stock_type_id' => $partsParameters['part-motherboard-stock-type-id'],
                'ram_stock_type_id' => $partsParameters['part-ram-stock-type-id'],
                'video_card_stock_type_id' => $partsParameters['part-videocard-stock-type-id'],
                'hdd_stock_type_id' => $partsParameters['part-hdd-stock-type-id'],
                'ssd_stock_type_id' => $partsParameters['part-ssd-stock-type-id'],
                'power_supply_stock_type_id' => $partsParameters['part-power-supply-stock-type-id'],
                'case_stock_type_id' => $partsParameters['part-case-stock-type-id'],
                'cooler_stock_type_id' => $partsParameters['part-cooler-stock-type-id'],
                // drop old stock types
                'processor_old_stock_type_id' => 0,
	              'motherboard_old_stock_type_id' => 0,
	              'ram_old_stock_type_id' => 0,
	              'video_card_old_stock_type_id' => 0,
	              'hdd_old_stock_type_id' => 0,
	              'ssd_old_stock_type_id' => 0,
	              'power_supply_old_stock_type_id' => 0,
	              'case_old_stock_type_id' => 0,
	              'cooler_old_stock_type_id' => 0,
                // drop old prices
                'processor_old_price' => 0,
                'motherboard_old_price' => 0,
                'ram_old_price' => 0,
                'video_card_old_price' => 0,
                'hdd_old_price' => 0,
                'ssd_old_price' => 0,
                'power_supply_old_price' => 0,
                'case_old_price' => 0,
                'cooler_old_price' => 0,
                // drop price change flags
                'processor_price_affected' => 0,
                'motherboard_price_affected' => 0,
                'ram_price_affected' => 0,
                'video_card_price_affected' => 0,
                'hdd_price_affected' => 0,
                'ssd_price_affected' => 0,
                'power_supply_price_affected' => 0,
                'case_price_affected' => 0,
                'cooler_price_affected' => 0,
                // drop stock type change flags
                'processor_stock_type_id_affected' => 0,
                'motherboard_stock_type_id_affected' => 0,
                'ram_stock_type_id_affected' => 0,
                'video_card_stock_type_id_affected' => 0,
                'hdd_stock_type_id_affected' => 0,
                'ssd_stock_type_id_affected' => 0,
                'power_supply_stock_type_id_affected' => 0,
                'case_stock_type_id_affected' => 0,
                'cooler_stock_type_id_affected' => 0,
                // drop part unlink flags
                'processor_unlinked' => 0,
                'motherboard_unlinked' => 0,
                'ram_unlinked' => 0,
                'video_card_unlinked' => 0,
                'hdd_unlinked' => 0,
                'ssd_unlinked' => 0,
                'power_supply_unlinked' => 0,
                'case_unlinked' => 0,
                'cooler_unlinked' => 0
              ];

              $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
              $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();
              $numOfFilledParts = 0;
              $notInStockPartsNum = 0;
              $allPartsAreVisible = true;
              $numOfManuallyIndicatedParts = 0;

              if ($partsParameters['part-processor-title'] && $partsParameters['part-processor-price'])
              {
                $partsDataToUpdate['processor_title'] = $partsParameters['part-processor-title'];
                $partsDataToUpdate['processor_price'] = $partsParameters['part-processor-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-processor-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-processor-id'];

                if ($systemParts -> processor_id && $paramPartId && $paramPartId != $systemParts -> processor_id)
                {
                  $partsDataToUpdate['processor_visibility'] = 1;
                  $partsDataToUpdate['processor_visibility_affected'] = 0;
                }

                else if (!$systemParts -> processor_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-motherboard-title'] && $partsParameters['part-motherboard-price'])
              {
                $partsDataToUpdate['motherboard_title'] = $partsParameters['part-motherboard-title'];
                $partsDataToUpdate['motherboard_price'] = $partsParameters['part-motherboard-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-motherboard-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-motherboard-id'];

                if ($systemParts -> motherboard_id && $paramPartId && $paramPartId != $systemParts -> motherboard_id)
                {
                  $partsDataToUpdate['motherboard_visibility'] = 1;
                  $partsDataToUpdate['motherboard_visibility_affected'] = 0;
                }

                else if (!$systemParts -> motherboard_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-ram-title'] && $partsParameters['part-ram-price'])
              {
                $partsDataToUpdate['ram_title'] = $partsParameters['part-ram-title'];
                $partsDataToUpdate['ram_price'] = $partsParameters['part-ram-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-ram-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-ram-id'];

                if ($systemParts -> ram_id && $paramPartId && $paramPartId != $systemParts -> ram_id)
                {
                  $partsDataToUpdate['ram_visibility'] = 1;
                  $partsDataToUpdate['ram_visibility_affected'] = 0;
                }

                else if (!$systemParts -> ram_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-videocard-title'] && $partsParameters['part-videocard-price'])
              {
                $partsDataToUpdate['video_card_title'] = $partsParameters['part-videocard-title'];
                $partsDataToUpdate['video_card_price'] = $partsParameters['part-videocard-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-videocard-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-videocard-id'];

                if ($systemParts -> video_card_id && $paramPartId && $paramPartId != $systemParts -> video_card_id)
                {
                  $partsDataToUpdate['video_card_visibility'] = 1;
                  $partsDataToUpdate['video_card_visibility_affected'] = 0;
                }

                else if (!$systemParts -> video_card_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-hdd-title'] && $partsParameters['part-hdd-price'])
              {
                $partsDataToUpdate['hdd_title'] = $partsParameters['part-hdd-title'];
                $partsDataToUpdate['hdd_price'] = $partsParameters['part-hdd-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-hdd-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-hdd-id'];

                if ($systemParts -> hdd_id && $paramPartId && $paramPartId != $systemParts -> hdd_id)
                {
                  $partsDataToUpdate['hdd_visibility'] = 1;
                  $partsDataToUpdate['hdd_visibility_affected'] = 0;
                }

                else if (!$systemParts -> hdd_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-ssd-title'] && $partsParameters['part-ssd-price'])
              {
                $partsDataToUpdate['ssd_title'] = $partsParameters['part-ssd-title'];
                $partsDataToUpdate['ssd_price'] = $partsParameters['part-ssd-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-ssd-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-ssd-id'];

                if ($systemParts -> ssd_id && $paramPartId && $paramPartId != $systemParts -> ssd_id)
                {
                  $partsDataToUpdate['ssd_visibility'] = 1;
                  $partsDataToUpdate['ssd_visibility_affected'] = 0;
                }

                else if (!$systemParts -> ssd_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-power-supply-title'] && $partsParameters['part-power-supply-price'])
              {
                $partsDataToUpdate['power_supply_title'] = $partsParameters['part-power-supply-title'];
                $partsDataToUpdate['power_supply_price'] = $partsParameters['part-power-supply-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-power-supply-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-power-supply-id'];

                if ($systemParts -> power_supply_id && $paramPartId && $paramPartId != $systemParts -> power_supply_id)
                {
                  $partsDataToUpdate['power_supply_visibility'] = 1;
                  $partsDataToUpdate['power_supply_visibility_affected'] = 0;
                }

                else if (!$systemParts -> power_supply_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-case-title'] && $partsParameters['part-case-price'])
              {
                $partsDataToUpdate['case_title'] = $partsParameters['part-case-title'];
                $partsDataToUpdate['case_price'] = $partsParameters['part-case-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-case-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-case-id'];

                if ($systemParts -> case_id && $paramPartId && $paramPartId != $systemParts -> case_id)
                {
                  $partsDataToUpdate['case_visibility'] = 1;
                  $partsDataToUpdate['case_visibility_affected'] = 0;
                }

                else if (!$systemParts -> case_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-cooler-title'] && $partsParameters['part-cooler-price'])
              {
                $partsDataToUpdate['cooler_title'] = $partsParameters['part-cooler-title'];
                $partsDataToUpdate['cooler_price'] = $partsParameters['part-cooler-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-cooler-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-cooler-id'];

                if ($systemParts -> cooler_id && $paramPartId && $paramPartId != $systemParts -> cooler_id)
                {
                  $partsDataToUpdate['cooler_visibility'] = 1;
                  $partsDataToUpdate['cooler_visibility_affected'] = 0;
                }

                else if (!$systemParts -> cooler_visibility)
                {
                  $allPartsAreVisible = false;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($inStockRecord && $notInStockRecord)
              {
                if ($numOfFilledParts)
                {
                  if ($notInStockPartsNum || $numOfManuallyIndicatedParts || !$allPartsAreVisible)
                  {
                    $record -> stockTypeId = $notInStockRecord -> id;
                  }

                  else
                  {
                    $record -> stockTypeId = $inStockRecord -> id;
                  }

                  $record -> save();
                }
              }

              \DB::table('computer_parts') -> where('computer_id', $recordId) -> update($partsDataToUpdate);
              \DB::table('computers') -> where('id', $recordId) -> update(['affected' => 0]);
            }

            return ['updated' => true];
          }

          else throw new \Exception;
        }

        catch(\Exception $e)
        {
          return ['updated' => false];
        }
      }

      return ['updated' => false];
    }

    public function createSystemCopy(Request $request)
    {
      $rules = [
        'record-id' => ['bail', 'required', new NaturalNumber],
        'title' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'description' => ['bail', 'required', 'string', 'min:1', 'max:2000000'],
        'seoDescription' => ['string', 'max:500', 'nullable'],
        'seoKeywords' => ['string', 'max:500', 'nullable'],
        'cpu' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'gpuTitle' => ['bail', 'required', 'string', 'min:1', 'max:200'],
        'memory' => ['bail', 'required', new NaturalNumber],
        'videoMemory' => ['bail', 'required', new PositiveIntegerOrZero],
        'seriesId' => ['bail', 'required', new NaturalNumber],
        'computerGraphicsId' => ['bail', 'required', new NaturalNumber],
        'solidStateDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'hardDiscDriveCapacity' => ['bail', 'required', new PositiveIntegerOrZero],
        'warrantyDuration' => ['bail', 'required', new NaturalNumber],
        'warrantyId' => ['bail', 'required', new NaturalNumber],
        'price' => ['bail', 'required', new NaturalNumber],
        'discount' => ['bail', 'required', new PositiveIntegerOrZero],
        'quantity' => ['bail', 'required', new PositiveIntegerOrZero]
      ];

      $data = $request -> only(['record-id',
                                'title',
                                'description',
                                'seoDescription',
                                'seoKeywords',
                                'cpu',
                                'gpuTitle',
                                'memory',
                                'videoMemory',
                                'seriesId',
                                'computerGraphicsId',
                                'solidStateDriveCapacity',
                                'hardDiscDriveCapacity',
                                'warrantyDuration',
                                'warrantyId',
                                'price',
                                'discount',
                                'quantity']);

      $validator = \Validator::make($data, $rules);

      if (!$validator -> fails())
      {
        $partsRules = [ 'header-template' => 'nullable|string|max:2000000',
                        'footer-template' => 'nullable|string|max:2000000',
                        'part-processor-title' => 'nullable|string|max:300',
                        'part-processor-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-processor-id' => ['required', new PositiveIntegerOrZero],
                        'part-processor-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-motherboard-title' => 'nullable|string|max:300',
                        'part-motherboard-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-motherboard-id' => ['required', new PositiveIntegerOrZero],
                        'part-motherboard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-ram-title' => 'nullable|string|max:300',
                        'part-ram-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-ram-id' => ['required', new PositiveIntegerOrZero],
                        'part-ram-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-videocard-title' => 'nullable|string|max:300',
                        'part-videocard-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-videocard-id' => ['required', new PositiveIntegerOrZero],
                        'part-videocard-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-hdd-title' => 'nullable|string|max:300',
                        'part-hdd-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-hdd-id' => ['required', new PositiveIntegerOrZero],
                        'part-hdd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-ssd-title' => 'nullable|string|max:300',
                        'part-ssd-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-ssd-id' => ['required', new PositiveIntegerOrZero],
                        'part-ssd-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-power-supply-title' => 'nullable|string|max:300',
                        'part-power-supply-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-power-supply-id' => ['required', new PositiveIntegerOrZero],
                        'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-case-title' => 'nullable|string|max:300',
                        'part-case-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-case-id' => ['required', new PositiveIntegerOrZero],
                        'part-case-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-power-supply-stock-type-id' => ['required', new PositiveIntegerOrZero],
                        'part-cooler-title' => 'nullable|string|max:300',
                        'part-cooler-price' => ['nullable', new PositiveIntegerOrZero],
                        'part-cooler-id' => ['required', new PositiveIntegerOrZero],
                        'part-cooler-stock-type-id' => ['required', new PositiveIntegerOrZero] ];

        $partsParameters = $request -> only(['header-template', 'footer-template',
                                             'part-processor-title', 'part-processor-price', 'part-processor-id', 'part-processor-stock-type-id',
                                             'part-motherboard-title', 'part-motherboard-price', 'part-motherboard-id', 'part-motherboard-stock-type-id',
                                             'part-ram-title', 'part-ram-price', 'part-ram-id', 'part-ram-stock-type-id',
                                             'part-videocard-title', 'part-videocard-price', 'part-videocard-id', 'part-videocard-stock-type-id',
                                             'part-hdd-title', 'part-hdd-price', 'part-hdd-id', 'part-hdd-stock-type-id',
                                             'part-ssd-title', 'part-ssd-price', 'part-ssd-id', 'part-ssd-stock-type-id',
                                             'part-power-supply-title', 'part-power-supply-price', 'part-power-supply-id', 'part-power-supply-stock-type-id',
                                             'part-case-title', 'part-case-price', 'part-case-id', 'part-case-stock-type-id',
                                             'part-cooler-title', 'part-cooler-price', 'part-cooler-id', 'part-cooler-stock-type-id']);

        $partsValidator = \Validator::make($partsParameters, $partsRules);

        if (!$partsValidator -> fails())
        {
          try
          {
            $partsDataToInsert = [
              'processor_title' => null,
              'processor_price' => 0,
              'motherboard_title' => null,
              'motherboard_price' => 0,
              'ram_title' => null,
              'ram_price' => 0,
              'video_card_title' => null,
              'video_card_price' => 0,
              'hdd_title' => null,
              'hdd_price' => 0,
              'ssd_title' => null,
              'ssd_price' => 0,
              'power_supply_title' => null,
              'power_supply_price' => 0,
              'case_title' => null,
              'case_price' => 0,
              'cooler_title' => null,
              'cooler_price' => 0,
              'header_template' => $partsParameters['header-template'],
              'footer_template' => $partsParameters['footer-template'],
              'processor_id' => $partsParameters['part-processor-id'],
              'motherboard_id' => $partsParameters['part-motherboard-id'],
              'ram_id' => $partsParameters['part-ram-id'],
              'video_card_id' => $partsParameters['part-videocard-id'],
              'hdd_id' => $partsParameters['part-hdd-id'],
              'ssd_id' => $partsParameters['part-ssd-id'],
              'power_supply_id' => $partsParameters['part-power-supply-id'],
              'case_id' => $partsParameters['part-case-id'],
              'cooler_id' => $partsParameters['part-cooler-id'],
              'processor_stock_type_id' => $partsParameters['part-processor-stock-type-id'],
              'motherboard_stock_type_id' => $partsParameters['part-motherboard-stock-type-id'],
              'ram_stock_type_id' => $partsParameters['part-ram-stock-type-id'],
              'video_card_stock_type_id' => $partsParameters['part-videocard-stock-type-id'],
              'hdd_stock_type_id' => $partsParameters['part-hdd-stock-type-id'],
              'ssd_stock_type_id' => $partsParameters['part-ssd-stock-type-id'],
              'power_supply_stock_type_id' => $partsParameters['part-power-supply-stock-type-id'],
              'case_stock_type_id' => $partsParameters['part-case-stock-type-id'],
              'cooler_stock_type_id' => $partsParameters['part-cooler-stock-type-id'],
              // drop old stock types
              'processor_old_stock_type_id' => 0,
              'motherboard_old_stock_type_id' => 0,
              'ram_old_stock_type_id' => 0,
              'video_card_old_stock_type_id' => 0,
              'hdd_old_stock_type_id' => 0,
              'ssd_old_stock_type_id' => 0,
              'power_supply_old_stock_type_id' => 0,
              'case_old_stock_type_id' => 0,
              'cooler_old_stock_type_id' => 0,
              // drop old prices
              'processor_old_price' => 0,
              'motherboard_old_price' => 0,
              'ram_old_price' => 0,
              'video_card_old_price' => 0,
              'hdd_old_price' => 0,
              'ssd_old_price' => 0,
              'power_supply_old_price' => 0,
              'case_old_price' => 0,
              'cooler_old_price' => 0,
              // drop price change flags
              'processor_price_affected' => 0,
              'motherboard_price_affected' => 0,
              'ram_price_affected' => 0,
              'video_card_price_affected' => 0,
              'hdd_price_affected' => 0,
              'ssd_price_affected' => 0,
              'power_supply_price_affected' => 0,
              'case_price_affected' => 0,
              'cooler_price_affected' => 0,
              // drop stock type change flags
              'processor_stock_type_id_affected' => 0,
              'motherboard_stock_type_id_affected' => 0,
              'ram_stock_type_id_affected' => 0,
              'video_card_stock_type_id_affected' => 0,
              'hdd_stock_type_id_affected' => 0,
              'ssd_stock_type_id_affected' => 0,
              'power_supply_stock_type_id_affected' => 0,
              'case_stock_type_id_affected' => 0,
              'cooler_stock_type_id_affected' => 0,
              // drop part unlink flags
              'processor_unlinked' => 0,
              'motherboard_unlinked' => 0,
              'ram_unlinked' => 0,
              'video_card_unlinked' => 0,
              'hdd_unlinked' => 0,
              'ssd_unlinked' => 0,
              'power_supply_unlinked' => 0,
              'case_unlinked' => 0,
              'cooler_unlinked' => 0,
              // drop visibility flags
              'processor_visibility' => 1,
              'motherboard_visibility' => 1,
              'ram_visibility' => 1,
              'video_card_visibility' => 1,
              'hdd_visibility' => 1,
              'ssd_visibility' => 1,
              'power_supply_visibility' => 1,
              'case_visibility' => 1,
              'cooler_visibility' => 1
            ];

            $ssdCapacity = (int) $data['solidStateDriveCapacity'];
            $hddCapacity = (int) $data['hardDiscDriveCapacity'];

            if ($hddCapacity || $ssdCapacity)
            {
              $recordId = $data['record-id'];
              $data = \Arr::except($data, ['record-id']);

              $record = Computer::findOrFail($recordId);
              $computerCopy = new Computer();

              $data['description'] = preg_replace('/(\<script(.|\s)*\>(.|\s)*<\/script\>)/si', '', $data['description']);

              foreach($data as $key => $value)
              {
                $computerCopy -> $key = $data[$key];
              }

              $hash = md5(uniqid(mt_rand(), true));
              $uuid = substr($hash, 0, 8) . substr($hash, 8, 4) . substr($hash, 12, 4) . substr($hash, 16, 4) . substr($hash, 20, 12);

              $mainImageNameParts = explode('.', $record -> mainImage);
              $copiedMainImageName = md5(microtime()) . mt_rand(1, 10000) . '.' . $mainImageNameParts[1];
              $copiedImageNameExists = \DB::table('computers') -> where('mainImage', $copiedMainImageName) -> count();

              while($copiedImageNameExists)
              {
                $copiedMainImageName = md5(microtime()) . mt_rand(1, 10000) . '.' . $mainImageNameParts[1];
                $copiedImageNameExists = \DB::table('computers') -> where('mainImage', $copiedMainImageName) -> count();
              }

              $originalMainImagesPath = realpath('./images/computers/main/original');
              $previewMainImagesPath = realpath('./images/computers/main/preview');

              $originalMainImageFullName = $originalMainImagesPath . '/' . $record -> mainImage;
              $previewMainImageFullName = $previewMainImagesPath . '/' . $record -> mainImage;

              $copiedOriginalMainImageFullName = $originalMainImagesPath . '/' . $copiedMainImageName;
              $copiedPreviewMainImageFullName = $previewMainImagesPath . '/' . $copiedMainImageName;

              $computerDataToCopy = [
                'mainImage' => $copiedMainImageName,
                'uuid' => $uuid,
                'conditionId' => $record -> conditionId,
                'isOffer' => $record -> isOffer,
                'visibility' => $record -> visibility,
                'seoDescription' => $record -> seoDescription,
                'seoKeywords' => $record -> seoKeywords,
              ];

              foreach($computerDataToCopy as $key => $value)
              {
                $computerCopy -> $key = $computerDataToCopy[$key];
              }

              $computerCopy -> save();

              $descriptionTemplates = \DB::table('computer_parts') -> select(['header_template', 'footer_template']) -> where('computer_id', $recordId) -> first();

              $inStockRecord = \DB::table('stock_types') -> where('check', 0) -> first();
              $notInStockRecord = \DB::table('stock_types') -> where('check', 1) -> first();
              $numOfFilledParts = 0;
              $notInStockPartsNum = 0;
              $numOfManuallyIndicatedParts = 0;
              $allPartsAreVisible = true;

              $systemParts = \DB::table('computer_parts') -> where('computer_id', $record -> id) -> first(); // get an exsistent system parts

              $partsDataToInsert['header_template'] = $descriptionTemplates -> header_template;
              $partsDataToInsert['footer_template'] = $descriptionTemplates -> footer_template;

              if ($partsParameters['part-processor-title'] && $partsParameters['part-processor-price'])
              {
                $partsDataToInsert['processor_title'] = $partsParameters['part-processor-title'];
                $partsDataToInsert['processor_price'] = $partsParameters['part-processor-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-processor-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-processor-id'];

                if ($systemParts -> processor_id && $paramPartId && $paramPartId != $systemParts -> processor_id)
                {
                  $partsDataToInsert['processor_visibility'] = 1;
                  $partsDataToInsert['processor_visibility_affected'] = 0;
                }

                else if (!$systemParts -> processor_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['processor_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-motherboard-title'] && $partsParameters['part-motherboard-price'])
              {
                $partsDataToInsert['motherboard_title'] = $partsParameters['part-motherboard-title'];
                $partsDataToInsert['motherboard_price'] = $partsParameters['part-motherboard-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-motherboard-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-motherboard-id'];

                if ($systemParts -> motherboard_id && $paramPartId && $paramPartId != $systemParts -> motherboard_id)
                {
                  $partsDataToInsert['motherboard_visibility'] = 1;
                  $partsDataToInsert['motherboard_visibility_affected'] = 0;
                }

                else if (!$systemParts -> motherboard_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['motherboard_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-ram-title'] && $partsParameters['part-ram-price'])
              {
                $partsDataToInsert['ram_title'] = $partsParameters['part-ram-title'];
                $partsDataToInsert['ram_price'] = $partsParameters['part-ram-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-ram-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-ram-id'];

                if ($systemParts -> ram_id && $paramPartId && $paramPartId != $systemParts -> ram_id)
                {
                  $partsDataToInsert['ram_visibility'] = 1;
                  $partsDataToInsert['ram_visibility_affected'] = 0;
                }

                else if (!$systemParts -> ram_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['ram_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-videocard-title'] && $partsParameters['part-videocard-price'])
              {
                $partsDataToInsert['video_card_title'] = $partsParameters['part-videocard-title'];
                $partsDataToInsert['video_card_price'] = $partsParameters['part-videocard-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-videocard-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-videocard-id'];

                if ($systemParts -> video_card_id && $paramPartId && $paramPartId != $systemParts -> video_card_id)
                {
                  $partsDataToInsert['video_card_visibility'] = 1;
                  $partsDataToInsert['video_card_visibility_affected'] = 0;
                }

                else if (!$systemParts -> video_card_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['video_card_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-hdd-title'] && $partsParameters['part-hdd-price'])
              {
                $partsDataToInsert['hdd_title'] = $partsParameters['part-hdd-title'];
                $partsDataToInsert['hdd_price'] = $partsParameters['part-hdd-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-hdd-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-hdd-id'];

                if ($systemParts -> hdd_id && $paramPartId && $paramPartId != $systemParts -> hdd_id)
                {
                  $partsDataToInsert['hdd_visibility'] = 1;
                  $partsDataToInsert['hdd_visibility_affected'] = 0;
                }

                else if (!$systemParts -> hdd_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['hdd_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-ssd-title'] && $partsParameters['part-ssd-price'])
              {
                $partsDataToInsert['ssd_title'] = $partsParameters['part-ssd-title'];
                $partsDataToInsert['ssd_price'] = $partsParameters['part-ssd-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-ssd-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-ssd-id'];

                if ($systemParts -> ssd_id && $paramPartId && $paramPartId != $systemParts -> ssd_id)
                {
                  $partsDataToInsert['ssd_visibility'] = 1;
                  $partsDataToInsert['ssd_visibility_affected'] = 0;
                }

                else if (!$systemParts -> ssd_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['ssd_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-power-supply-title'] && $partsParameters['part-power-supply-price'])
              {
                $partsDataToInsert['power_supply_title'] = $partsParameters['part-power-supply-title'];
                $partsDataToInsert['power_supply_price'] = $partsParameters['part-power-supply-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-power-supply-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-power-supply-id'];

                if ($systemParts -> power_supply_id && $paramPartId && $paramPartId != $systemParts -> power_supply_id)
                {
                  $partsDataToInsert['power_supply_visibility'] = 1;
                  $partsDataToInsert['power_supply_visibility_affected'] = 0;
                }

                else if (!$systemParts -> power_supply_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['power_supply_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-case-title'] && $partsParameters['part-case-price'])
              {
                $partsDataToInsert['case_title'] = $partsParameters['part-case-title'];
                $partsDataToInsert['case_price'] = $partsParameters['part-case-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-case-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-case-id'];

                if ($systemParts -> case_id && $paramPartId && $paramPartId != $systemParts -> case_id)
                {
                  $partsDataToInsert['case_visibility'] = 1;
                  $partsDataToInsert['case_visibility_affected'] = 0;
                }

                else if (!$systemParts -> case_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['case_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($partsParameters['part-cooler-title'] && $partsParameters['part-cooler-price'])
              {
                $partsDataToInsert['cooler_title'] = $partsParameters['part-cooler-title'];
                $partsDataToInsert['cooler_price'] = $partsParameters['part-cooler-price'];

                $numOfFilledParts++;

                $partStockTypeId = (int) $partsParameters['part-cooler-stock-type-id'];

                if ($inStockRecord && $inStockRecord -> id != $partStockTypeId)
                {
                  $notInStockPartsNum++;
                }

                $paramPartId = (int) $partsParameters['part-cooler-id'];

                if ($systemParts -> cooler_id && $paramPartId && $paramPartId != $systemParts -> cooler_id)
                {
                  $partsDataToInsert['cooler_visibility'] = 1;
                  $partsDataToInsert['cooler_visibility_affected'] = 0;
                }

                else if (!$systemParts -> cooler_visibility)
                {
                  $allPartsAreVisible = false;

                  $partsDataToInsert['cooler_visibility'] = 0;
                }

                if (!$paramPartId)
                {
                  $numOfManuallyIndicatedParts++;
                }
              }

              if ($inStockRecord && $notInStockRecord)
              {
                if ($numOfFilledParts)
                {
                  if ($notInStockPartsNum || $numOfManuallyIndicatedParts || !$allPartsAreVisible)
                  {
                    $computerCopy -> stockTypeId = $notInStockRecord -> id;
                  }

                  else
                  {
                    $computerCopy -> stockTypeId = $inStockRecord -> id;
                  }

                  $computerCopy -> save();
                }

                $partsDataToInsert['computer_id'] = $computerCopy -> id;

                \DB::table('computer_parts') -> insert($partsDataToInsert);
              }

              $additionalImages = \DB::table('computers_images') -> where('computerId', $record -> id) -> get();

              if ($additionalImages -> count())
              {
                $originalAdditionalImagesPath = realpath('./images/computers/slides/original');
                $previewAdditionalImagesPath = realpath('./images/computers/slides/preview');
                $additionalImagesToInsert = [];

                foreach($additionalImages as $additionalImage)
                {
                  $additionalImageNameParts = explode('.', $additionalImage -> image);
                  $copiedAdditionalImageName = md5(microtime()) . mt_rand(1, 10000) . '.' . $additionalImageNameParts[1];
                  $copiedAdditionalImageNameExists = \DB::table('computers_images') -> where('image', $copiedAdditionalImageName) -> count();

                  while($copiedAdditionalImageNameExists)
                  {
                    $copiedAdditionalImageName = md5(microtime()) . mt_rand(1, 10000) . '.' . $additionalImageNameParts[1];
                    $copiedAdditionalImageNameExists = \DB::table('computers_images') -> where('image', $copiedAdditionalImageName) -> count();
                  }

                  $originalAdditionalImageFullName = $originalAdditionalImagesPath . '/' . $additionalImage -> image;
                  $previewAdditionalImageFullName = $previewAdditionalImagesPath . '/' . $additionalImage -> image;

                  $copiedOriginalAdditionalImageFullName = $originalAdditionalImagesPath . '/' . $copiedAdditionalImageName;
                  $copiedPreviewAdditionalImageFullName = $previewAdditionalImagesPath . '/' . $copiedAdditionalImageName;

                  $additionalImagesToInsert[] = ['image' => $copiedAdditionalImageName,
                                                 'computerId' => $computerCopy -> id];

                  \File::copy($originalAdditionalImageFullName, $copiedOriginalAdditionalImageFullName);
                  \File::copy($previewAdditionalImageFullName, $copiedPreviewAdditionalImageFullName);
                }

                \DB::table('computers_images') -> insert($additionalImagesToInsert);
              }

              \File::copy($originalMainImageFullName, $copiedOriginalMainImageFullName);
              \File::copy($previewMainImageFullName, $copiedPreviewMainImageFullName);

              return ['created' => true];
            }
          }

          catch(\Exception $e)
          {
            return ['created' => false];
          }
        }
      }

      return ['created' => false];
    }

    public function destroy($id) // Remove the specified resource from storage
    {
      try{

        RecordDeletable::deleteRecord(Computer::class, $id);

        \DB::table('computer_parts') -> where('computer_id', $id) -> delete();

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

        BaseDataUpdatable::updateBaseData(Computer::class, $data);

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

                $imagesSources = MainImageUpdatable::updateImage(Computer::class, $recordId, $file);

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
                $className = Computer::class;
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

      $classBaseName = class_basename(Computer::class);
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

    public function templates(Request $request)
    {
      return \View::make('contents.controlPanel.computers.templates') -> with([

          'headerTemplates' => \DB::table('computer_header_description_templates') -> get(),
          'footerTemplates' => \DB::table('computer_footer_description_templates') -> get()
      ]);
    }

    public function storeComputerHeaderTemplate(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> only(['title', 'description']);
      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'description' => 'required|string|min:1|max:1000000' ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
         $data['success'] = true;

         \DB::table('computer_header_description_templates') -> insert($parameters);
      }

      return $data;
    }

    public function storeComputerFooterTemplate(Request $request)
    {
      $data['success'] = false;

      $parameters = $request -> only(['title', 'description']);
      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'description' => 'required|string|min:1|max:1000000' ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
         $data['success'] = true;

         \DB::table('computer_footer_description_templates') -> insert($parameters);
      }

      return $data;
    }

    public function updateHeaderTemplateTitle(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> only([ 'title', 'record-id' ]);

      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'record-id' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $data['updated'] = true;

        \DB::table('computer_header_description_templates') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, 'record-id'));
      }

      return $data;
    }

    public function updateFooterTemplateTitle(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> only([ 'title', 'record-id' ]);

      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'record-id' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $data['updated'] = true;

        \DB::table('computer_footer_description_templates') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, 'record-id'));
      }

      return $data;
    }

    public function destroyHeaderTemplate($id)
    {
      try{

          \DB::table('computer_header_description_templates') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    public function destroyFooterTemplate($id)
    {
      try{

          \DB::table('computer_footer_description_templates') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    public function editHeaderTemplate($id)
    {
      $template = \DB::table('computer_header_description_templates') -> where('id', $id) -> first();

      return \View::make('contents.controlPanel.computers.editHeaderTemplate') -> with([
        'template' => $template,
        'templateExists' => !is_null($template)
      ]);
    }

    public function editFooterTemplate($id)
    {
      $template = \DB::table('computer_footer_description_templates') -> where('id', $id) -> first();

      return \View::make('contents.controlPanel.computers.editFooterTemplate') -> with([
          'template' => $template,
          'templateExists' => !is_null($template)
      ]);
    }

    public function updateTemplateHeader(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> only([ 'title', 'description', 'record-id' ]);

      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'description' => 'required|string|min:1|max:1000000',
                 'record-id' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $data['updated'] = true;

        \DB::table('computer_header_description_templates') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, 'record-id'));
      }

      return $data;
    }

    public function updateTemplateFooter(Request $request)
    {
      $data['updated'] = false;

      $parameters = $request -> only([ 'title', 'description', 'record-id' ]);

      $rules = [ 'title' => 'required|string|min:1|max:300',
                 'description' => 'required|string|min:1|max:1000000',
                 'record-id' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $data['updated'] = true;

        \DB::table('computer_footer_description_templates') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, 'record-id'));
      }

      return $data;
    }
}
