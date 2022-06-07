<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Computer;

class ComputerController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 12;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(Computer::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'condition' => 'required|string',
                                                  'cpu-series' => 'required|string',
                                                  'computer-graphics' => 'required|string',
                                                  'memory' => 'required|string',
                                                  'hdd-storage' => 'required|string',
                                                  'ssd-storage' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> computerMinPrice && $priceFrom <= $priceRange -> computerMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> computerMinPrice && $priceTo <= $priceRange -> computerMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $cpuSeries = \DB::table('cpu_series') -> select(['id']) -> get();
            $cpuSeriesExist = $cpuSeries -> count() != 0;

            $computerGraphics = \DB::table('computer_graphics') -> get();
            $computerGraphicsExists = $computerGraphics -> count() != 0;

            $memory = \DB::table('computers') -> selectRaw('DISTINCT(`memory`) AS `memory`') -> where('visibility', 1) -> get();
            $memoryExists = $memory -> count() != 0;

            $hddStorage = \DB::table('computers') -> selectRaw('DISTINCT(`hardDiscDriveCapacity`) AS `storage`') -> where('hardDiscDriveCapacity', '!=', 0) -> where('visibility', 1) -> get();
            $hddStorageExists = $hddStorage -> count() != 0;

            $ssdStorage = \DB::table('computers') -> selectRaw('DISTINCT(`solidStateDriveCapacity`) AS `storage`') -> where('solidStateDriveCapacity', '!=', 0) -> where('visibility', 1) -> get();
            $ssdStorageExists = $ssdStorage -> count() != 0;

            if ($cpuSeriesExist && $computerGraphicsExists && $memoryExists && $hddStorageExists && $ssdStorageExists && $conditionExists)
            {
              $cpuSeriesParts = array_map('intval', explode(':', $parameters['cpu-series']));
              $computerGraphicsParts = array_map('intval', explode(':', $parameters['computer-graphics']));
              $memoryParts = array_map('intval', explode(':', $parameters['memory']));
              $hddStorageParts = array_map('intval', explode(':', $parameters['hdd-storage']));
              $ssdStorageParts = array_map('intval', explode(':', $parameters['ssd-storage']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));

              $columns = ['computers.id', 'isOffer', 'conditionId', 'title', 'mainImage', 'discount', 'price', 'cpu', 'memory', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'gpuTitle', 'enableAddToCartButton'];
              $cpuSeriesNumbers = $computerGraphicsNumbers = $memoryNumbers = $hddStorageNumbers = $ssdStorageNumbers = $conditionNumbers = [];

              $query = \DB::table('computers') -> select($columns)
                                               -> join('computer_graphics', 'computer_graphics.id', '=', 'computers.computerGraphicsId')
                                               -> join('stock_types', 'stock_types.id', '=', 'computers.stockTypeId')
                                               -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($cpuSeries as $value) $cpuSeriesNumbers[] = $value -> id;
              foreach($computerGraphics as $value) $computerGraphicsNumbers[] = $value -> id;
              foreach($memory as $value) $memoryNumbers[] = $value -> memory;
              foreach($hddStorage as $value) $hddStorageNumbers[] = $value -> storage;
              foreach($ssdStorage as $value) $ssdStorageNumbers[] = $value -> storage;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($cpuSeriesParts, $cpuSeriesNumbers) == $cpuSeriesParts) $query = $query -> whereIn('seriesId', $cpuSeriesParts);
              if (array_intersect($computerGraphicsParts, $computerGraphicsNumbers) == $computerGraphicsParts) $query = $query -> whereIn('computerGraphicsId', $computerGraphicsParts);
              if (array_intersect($memoryParts, $memoryNumbers) == $memoryParts) $query = $query -> whereIn('memory', $memoryParts);
              if (array_intersect($hddStorageParts, $hddStorageNumbers) == $hddStorageParts) $query = $query -> whereIn('hardDiscDriveCapacity', $hddStorageParts);
              if (array_intersect($ssdStorageParts, $ssdStorageNumbers) == $ssdStorageParts) $query = $query -> whereIn('solidStateDriveCapacity', $ssdStorageParts);

              $videoMemoryValidator = \Validator::make($parameters, ['video-memory' => 'required|string']);

              if (!$videoMemoryValidator -> fails())
              {
                $videoMemoryParts = array_map('intval', explode(':', $parameters['video-memory']));
                $videoMemoryNumbers = [];

                $videoMemory = \DB::table('computers') -> selectRaw('DISTINCT(`videoMemory`) AS `videoMemory`') -> where('videoMemory', '!=', 0) -> where('visibility', 1) -> get();
                $videoMemoryExists = $videoMemory -> count() != 0;

                if ($videoMemoryExists && count($videoMemoryParts) != 0)
                {
                  foreach($videoMemory as $value) $videoMemoryNumbers[] = $value -> videoMemory;

                  if (array_intersect($videoMemoryParts, $videoMemoryNumbers) == $videoMemoryParts) $query = $query -> whereIn('videoMemory', $videoMemoryParts);
                }
              }

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = $productsOrder == 1 || $productsOrder == 2 ? 'price' : 'timestamp';

                $query = $query -> orderBy($orderColumn, $orderNumber == 0 ? 'desc' : 'asc');
              }

              else $query = $query -> orderBy('isOffer', 'desc');

              $currentPage = abs((int) $parameters['active-page']);
              $totalNumOfProducts = $query -> count();

              if ($currentPage != 0 && $totalNumOfProducts != 0)
              {
                $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $currentPage, 2, 0);

                $data['pages'] = $paginator -> pages;
                $data['maxPage'] = $paginator -> maxPage;
                $data['currentPage'] = $currentPage;

                $data['products'] = $query -> skip(($currentPage - 1) * $numOfProductsToView) -> take($numOfProductsToView) -> get();
                $data['productsExist'] = true;

                $data['products'] -> map(function($product){

                    $hddCapacity = $product -> hardDiscDriveCapacity;
                    $ssdCapacity = $product -> solidStateDriveCapacity;
                    $storage = null;

                    if ($hddCapacity && $ssdCapacity) $storage = "HDD {$hddCapacity} GB SSD {$ssdCapacity} GB";

                    else if ($hddCapacity && !$ssdCapacity) $storage = "HDD {$hddCapacity} GB";

                    else if (!$hddCapacity && $ssdCapacity) $storage = "SSD {$ssdCapacity} GB";

                    $product -> newPrice = $product -> price - $product -> discount;
                    $product -> storage = $storage;
                });
              }
            }
          }
        }
      }

      return View::make('contents.shop.computers.getComputers', ['data' => $data]);
    }

    public function getComputersForHomePage($id)
    {
      $systemId = (int) $id;
      $numberOfSystemsToView = 20;

      $columns = ['computers.id', 'title', 'mainImage', 'discount', 'price', 'cpu', 'memory', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'gpuTitle'];

      $computers = \DB::table('computers') -> select($columns) -> where('visibility', 1) -> where('seriesId', '=', $systemId) -> take($numberOfSystemsToView) -> get();
      $computersExist = !$computers -> isEmpty();

      if ($computersExist)
      {
        $computers -> map(function($system){

            $hddCapacity = $system -> hardDiscDriveCapacity;
            $ssdCapacity = $system -> solidStateDriveCapacity;
            $storage = null;

            if ($hddCapacity && $ssdCapacity) $storage = "HDD {$hddCapacity} GB SSD {$ssdCapacity} GB";

            else if ($hddCapacity && !$ssdCapacity) $storage = "HDD {$hddCapacity} GB";

            else if (!$hddCapacity && $ssdCapacity) $storage = "SSD {$ssdCapacity} GB";

            $system -> newPrice = $system -> price - $system -> discount;
            $system -> storage = $storage;
        });
      }

      return View::make('contents.shop.computers.getComputersForHomePage', ['computers' => $computers,
                                                                            'computersExist' => $computersExist]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Computer::class);
      $data['configuration']['computerPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['computersExist'] = false;

      if ($data['configuration']['computerPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> computerMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> computerMaxPrice;

        $totalNumOfProducts = Computer::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['computers'] = \DB::table('computers') -> select(['computers.id', 'isOffer', 'title', 'mainImage', 'discount', 'price', 'cpu', 'memory', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'gpuTitle', 'enableAddToCartButton'])
                                                     -> join('stock_types', 'stock_types.id', '=', 'computers.stockTypeId')
                                                     -> where('visibility', 1)
                                                     -> where('price', '>=', $productMinPrice)
                                                     -> where('price', '<=', $productMaxPrice)
                                                     -> orderBy('isOffer', 'desc')
                                                     -> skip(($page - 1) * $numOfProductsToView)
                                                     -> take($numOfProductsToView)
                                                     -> get();

        $data['computersExist'] = !$data['computers'] -> isEmpty();

        if ($data['computersExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Computer::class);

          $data['configuration']['cpuSeries'] = \DB::table('cpu_series') -> get();
          $data['configuration']['computerGraphics'] = \DB::table('computer_graphics') -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();

          $data['configuration']['memory'] = \DB::table('computers') -> select(\DB::raw('DISTINCT(`memory`) as `memory`'))
                                                                     -> where('visibility', 1)
                                                                     -> where('price', '>=', $productMinPrice)
                                                                     -> where('price', '<=', $productMaxPrice)
                                                                     -> orderBy('memory', 'desc')
                                                                     -> get();

          $data['configuration']['videoMemory'] = \DB::table('computers') -> select(\DB::raw('DISTINCT(`videoMemory`) as `videoMemory`'))
                                                                          -> where('visibility', 1)
                                                                          -> where('videoMemory', '!=', 0)
                                                                          -> where('price', '>=', $productMinPrice)
                                                                          -> where('price', '<=', $productMaxPrice)
                                                                          -> orderBy('videoMemory', 'desc')
                                                                          -> get();

          $data['configuration']['hddStorage'] = \DB::table('computers') -> select(\DB::raw('DISTINCT(`hardDiscDriveCapacity`) as `storage`'))
                                                                         -> where('visibility', 1)
                                                                         -> where('hardDiscDriveCapacity', '!=', 0)
                                                                         -> where('price', '>=', $productMinPrice)
                                                                         -> where('price', '<=', $productMaxPrice)
                                                                         -> orderBy('hardDiscDriveCapacity', 'desc')
                                                                         -> get();

          $data['configuration']['ssdStorage'] = \DB::table('computers') -> select(\DB::raw('DISTINCT(`solidStateDriveCapacity`) as `storage`'))
                                                                         -> where('visibility', 1)
                                                                         -> where('solidStateDriveCapacity', '!=', 0)
                                                                         -> where('price', '>=', $productMinPrice)
                                                                         -> where('price', '<=', $productMaxPrice)
                                                                         -> orderBy('solidStateDriveCapacity', 'desc')
                                                                         -> get();

          foreach($data['configuration']['conditions'] as $key => $value)

          $data['configuration']['conditions'][$key] -> numOfComputers = \DB::table('computers') -> where('conditionId', $value -> id)
                                                                                                 -> where('visibility', 1)
                                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                                 -> count();

          foreach($data['configuration']['ssdStorage'] as $key => $value)

          $data['configuration']['ssdStorage'][$key] -> numOfComputers = \DB::table('computers') -> where('solidStateDriveCapacity', '=', $value -> storage)
                                                                                                 -> where('visibility', 1)
                                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                                 -> count();

          foreach($data['configuration']['hddStorage'] as $key => $value)

          $data['configuration']['hddStorage'][$key] -> numOfComputers = \DB::table('computers') -> where('hardDiscDriveCapacity', '=', $value -> storage)
                                                                                                 -> where('visibility', 1)
                                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                                 -> count();

          foreach($data['configuration']['memory'] as $key => $value)

          $data['configuration']['memory'][$key] -> numOfComputers = \DB::table('computers') -> where('memory', '=', $value -> memory)
                                                                                             -> where('visibility', 1)
                                                                                             -> where('price', '>=', $productMinPrice)
                                                                                             -> where('price', '<=', $productMaxPrice)
                                                                                             -> count();

          foreach($data['configuration']['videoMemory'] as $key => $value)

          $data['configuration']['videoMemory'][$key] -> numOfComputers = \DB::table('computers') -> where('videoMemory', '=', $value -> videoMemory)
                                                                                                  -> where('visibility', 1)
                                                                                                  -> where('price', '>=', $productMinPrice)
                                                                                                  -> where('price', '<=', $productMaxPrice)
                                                                                                  -> count();

          foreach($data['configuration']['computerGraphics'] as $key => $value)

          $data['configuration']['computerGraphics'][$key] -> numOfComputers = \DB::table('computers') -> where('computerGraphicsId', $value -> id)
                                                                                                       -> where('visibility', 1)
                                                                                                       -> where('price', '>=', $productMinPrice)
                                                                                                       -> where('price', '<=', $productMaxPrice)
                                                                                                       -> count();

          foreach($data['configuration']['cpuSeries'] as $key => $value)

          $data['configuration']['cpuSeries'][$key] -> numOfComputers = \DB::table('computers') -> where('seriesId', $value -> id)
                                                                                                -> where('visibility', 1)
                                                                                                -> where('price', '>=', $productMinPrice)
                                                                                                -> where('price', '<=', $productMaxPrice)
                                                                                                -> count();

          foreach($data['computers'] as $key => $value)
          {
            $data['computers'][$key] -> newPrice = $value -> price - $value -> discount;

            $hddExists = $value -> hardDiscDriveCapacity != 0;
            $ssdExists = $value -> solidStateDriveCapacity != 0;
            $storage = null;

            if ($hddExists && $ssdExists) $storage = sprintf('HDD %d GB + SSD %d GB', $value -> hardDiscDriveCapacity, $value -> solidStateDriveCapacity);

            else if ($hddExists && !$ssdExists) $storage = sprintf('HDD %d GB', $value -> hardDiscDriveCapacity);

            else if (!$hddExists && $ssdExists) $storage = sprintf('SSD %d GB', $value -> solidStateDriveCapacity);

            $data['computers'][$key] -> storage = $storage;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(Computer::class);

      return View::make('contents.shop.computers.index', ['contentData' => $data,
                                                          'generalData' => $generalData]);
    }

    public function view($id)
    {
       $generalData = BaseModel::getGeneralData();
       $columns = ['computers.id', 'isOffer', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'conditionId', 'seoDescription', 'seoKeywords', 'stockTitle', 'stockTypeId', 'statusColor', 'check', 'quantity'];

       $data['computer'] = \DB::table('computers') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'computers.stockTypeId') -> where('computers.id', $id) -> where('visibility', 1) -> get() -> first();
       $data['computerExists'] = !is_null($data['computer']);

       $numOfProductsToView = 12;
       $pricePart = 0.2;

       if ($data['computerExists'])
       {
         $generalData['seoFields'] -> description = $data['computer'] -> seoDescription;
         $generalData['seoFields'] -> keywords = $data['computer'] -> seoKeywords;
         $generalData['seoFields'] -> title = $data['computer'] -> title;

         $stockData = \DB::table('stock_types') -> where('id', '=', $data['computer'] -> stockTypeId) -> get() -> first();

         $data['images'] = \DB::table('computers_images') -> where('computerId', '=', $data['computer'] -> id) -> get();
         $data['imagesExist'] = !$data['images'] -> isEmpty();

         $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['computer'] -> conditionId) -> get() -> first() -> conditionTitle;
         $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['computer'] -> warrantyId) -> get() -> first() -> durationUnit;

         $data['computer'] -> newPrice = $data['computer'] -> price - $data['computer'] -> discount;
         $data['computer'] -> categoryId = BaseModel::getTableAliasByModelName(Computer::class);
         $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

         $percent = $data['computer'] -> newPrice * $pricePart;

         $leftRange = (int) ($data['computer'] -> newPrice - $percent);
         $rightRange = (int) ($data['computer'] -> newPrice + $percent);

         $fields = ['computers.id', 'isOffer', 'title', 'mainImage', 'discount', 'price', 'cpu', 'memory', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'gpuTitle'];

         $data['recommendedComputers'] = \DB::table('computers') -> select($fields)
                                                                 -> where('visibility', 1)
                                                                 -> whereBetween('price', [$leftRange, $rightRange])
                                                                 -> where('computers.id', '!=', $data['computer'] -> id)
                                                                 -> take($numOfProductsToView)
                                                                 -> get();

         $data['recommendedComputersExist'] = !$data['recommendedComputers'] -> isEmpty();

         if ($data['recommendedComputersExist'])
         {
           foreach($data['recommendedComputers'] as $key => $value)
           {
             $data['recommendedComputers'][$key] -> newPrice = $value -> price - $value -> discount;

             $hddExists = $value -> hardDiscDriveCapacity != 0;
             $ssdExists = $value -> solidStateDriveCapacity != 0;
             $computerStorage = null;

             if ($hddExists && $ssdExists) $computerStorage = sprintf('HDD %d GB + SSD %d GB', $value -> hardDiscDriveCapacity, $value -> solidStateDriveCapacity);

             else if ($hddExists && !$ssdExists) $computerStorage = sprintf('HDD %d GB', $value -> hardDiscDriveCapacity);

             else if (!$hddExists && $ssdExists) $computerStorage = sprintf('SSD %d GB', $value -> solidStateDriveCapacity);

             $data['recommendedComputers'][$key] -> storage = $computerStorage;
           }
         }

         BaseModel::collectStatisticalData(Computer::class);

         return View::make('contents.shop.computers.view', ['contentData' => $data,
                                                            'generalData' => $generalData]);
       }

       else abort(404);
    }
}
