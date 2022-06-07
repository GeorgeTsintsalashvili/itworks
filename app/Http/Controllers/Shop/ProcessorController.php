<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Processor;

class ProcessorController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 15;
      $supportedOrders = [1, 2, 3, 4, 5, 6, 7, 8];
      $priceRange = BaseModel::getPriceRange(Processor::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'cpu-series' => 'required|string',
                                                  'cpu-manufacturer' => 'required|string',
                                                  'cpu-socket' => 'required|string',
                                                  'cores' => 'required|string',
                                                  'speed-from' => 'required|string',
                                                  'speed-to' => 'required|string',
                                                  'technology-process' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $speedFrom = abs((float) $parameters['speed-from']);
          $speedTo = abs((float) $parameters['speed-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> processorMinPrice && $priceFrom <= $priceRange -> processorMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> processorMinPrice && $priceTo <= $priceRange -> processorMaxPrice;
          $inputSpeedRangeIsCorrect = $speedFrom != 0 && $speedTo != 0 && $speedFrom <= $speedTo;

          if ($priceFromIsInRange && $priceToIsInRange && $inputSpeedRangeIsCorrect)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $speedRange = \DB::table('processors') -> selectRaw('CEIL(MAX(`clockSpeed`)) AS `maxSpeed`, FLOOR(MIN(`clockSpeed`)) AS `minSpeed`') -> first();
            $speedRangeExists = !is_null($speedRange);

            $cores = \DB::table('processors') -> selectRaw('DISTINCT(`cores`) AS `cores`') -> where('visibility', 1) -> get();
            $coresExist = $cores -> count() != 0;

            $cpuSeries = \DB::table('cpu_series') -> get();
            $cpuSeriesExist = $cpuSeries -> count() != 0;

            $cpuManufacturers = \DB::table('cpu_manufacturers') -> get();
            $cpuManufacturersExist = $cpuManufacturers -> count() != 0;

            $cpuSockets = \DB::table('cpu_sockets') -> get();
            $cpuSocketsExist = $cpuSockets -> count() != 0;

            $cpuTechnologyProcesses = \DB::table('cpu_technology_processes') -> get();
            $cpuTechnologyProcessesExist = $cpuTechnologyProcesses -> count() != 0;

            if ($cpuTechnologyProcessesExist && $cpuSocketsExist && $cpuManufacturersExist && $cpuSeriesExist && $coresExist && $stockTypeExists && $conditionExists)
            {
              $coresParts = array_map('intval', explode(':', $parameters['cores']));
              $cpuSeriesParts = array_map('intval', explode(':', $parameters['cpu-series']));
              $cpuManufacturersParts = array_map('intval', explode(':', $parameters['cpu-manufacturer']));
              $cpuSocketsParts = array_map('intval', explode(':', $parameters['cpu-socket']));
              $cpuTechnologyProcessesParts = array_map('intval', explode(':', $parameters['technology-process']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['processors.id', 'title', 'mainImage', 'discount', 'price', 'clockSpeed', 'socketTitle', 'size', 'cores', 'stockTypeId', 'enableAddToCartButton'];
              $coresNumbers = $cpuSeriesNumbers = $cpuManufacturersNumbers = $cpuSocketsNumbers = $cpuTechnologyProcessesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('processors') -> select($columns)
                                                -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processors.socketId')
                                                -> join('cpu_technology_processes', 'cpu_technology_processes.id', '=', 'processors.technologyProcessId')
                                                -> join('stock_types', 'stock_types.id', '=', 'processors.stockTypeId')
                                                -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($cores as $value) $coresNumbers[] = $value -> cores;
              foreach($cpuSeries as $value) $cpuSeriesNumbers[] = $value -> id;
              foreach($cpuManufacturers as $value) $cpuManufacturersNumbers[] = $value -> id;
              foreach($cpuSockets as $value) $cpuSocketsNumbers[] = $value -> id;
              foreach($cpuTechnologyProcesses as $value) $cpuTechnologyProcessesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($coresParts, $coresNumbers) == $coresParts) $query = $query -> whereIn('cores', $coresParts);
              if (array_intersect($cpuSeriesParts, $cpuSeriesNumbers) == $cpuSeriesParts) $query = $query -> whereIn('seriesId', $cpuSeriesParts);
              if (array_intersect($cpuManufacturersParts, $cpuManufacturersNumbers) == $cpuManufacturersParts) $query = $query -> whereIn('manufacturerId', $cpuManufacturersParts);
              if (array_intersect($cpuSocketsParts, $cpuSocketsNumbers) == $cpuSocketsParts) $query = $query -> whereIn('socketId', $cpuSocketsParts);
              if (array_intersect($cpuTechnologyProcessesParts, $cpuTechnologyProcessesNumbers) == $cpuTechnologyProcessesParts) $query = $query -> whereIn('technologyProcessId', $cpuTechnologyProcessesParts);

              $speedFrom = $speedFrom < $speedRange -> minSpeed ? $speedRange -> minSpeed : $speedFrom;
              $speedTo = $speedRange -> maxSpeed < $speedTo ? $speedRange -> maxSpeed : $speedTo;

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo) -> where('clockSpeed', '>=', $speedFrom) -> where('clockSpeed', '<=', $speedTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = 'price';

                if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'clockSpeed';

                else if ($productsOrder == 5 || $productsOrder == 6) $orderColumn = 'cores';

                else if ($productsOrder == 7 || $productsOrder == 8) $orderColumn = 'timestamp';

                $query = $query -> orderBy($orderColumn, $orderNumber == 0 ? 'desc' : 'asc');
              }

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

                   $product -> newPrice = $product -> price - $product -> discount;
                });
              }
            }
          }
        }
      }

      return View::make('contents.shop.processors.getProcessors', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 15;

      $data['processorsExist'] = false;
      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Processor::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> processorMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> processorMaxPrice;

        $totalNumOfProducts = Processor::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['processors'] = \DB::table('processors') -> select(['processors.id', 'title', 'mainImage', 'discount', 'price', 'clockSpeed', 'socketTitle', 'size', 'cores', 'stockTypeId', 'enableAddToCartButton'])
                                                       -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processors.socketId')
                                                       -> join('cpu_technology_processes', 'cpu_technology_processes.id', '=', 'processors.technologyProcessId')
                                                       -> join('stock_types', 'stock_types.id', '=', 'processors.stockTypeId')
                                                       -> where('visibility', '1')
                                                       -> where('price', '>=', $productMinPrice)
                                                       -> where('price', '<=', $productMaxPrice)
                                                       -> skip(($page - 1) * $numOfProductsToView)
                                                       -> take($numOfProductsToView)
                                                       -> get();

        $data['processorsExist'] = !$data['processors'] -> isEmpty();

        if ($data['processorsExist'])
        {
           $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Processor::class);

           $data['configuration']['cpuSeries'] = \DB::table('cpu_series') -> get();
           $data['configuration']['cpuManufacturers'] = \DB::table('cpu_manufacturers') -> get();
           $data['configuration']['cpuSockets'] = \DB::table('cpu_sockets') -> get();
           $data['configuration']['cpuTechnologyProcesses'] = \DB::table('cpu_technology_processes') -> get();
           $data['configuration']['conditions'] = \DB::table('conditions') -> get();
           $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
           $data['configuration']['maxClockSpeed'] = ceil(\DB::table('processors') -> selectRaw('MAX(`clockSpeed`) AS `maxClockSpeed`')
                                                                                   -> where('visibility', 1)
                                                                                   -> where('price', '>=', $productMinPrice)
                                                                                   -> where('price', '<=', $productMaxPrice)
                                                                                   -> get()
                                                                                   -> first()
                                                                                   -> maxClockSpeed);

           $data['configuration']['minClockSpeed'] = floor(\DB::table('processors') -> selectRaw('MIN(`clockSpeed`) AS `minClockSpeed`')
                                                                                    -> where('visibility', 1)
                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                    -> get()
                                                                                    -> first()
                                                                                    -> minClockSpeed);

           $data['configuration']['cpuCores'] = \DB::table('processors') -> selectRaw('DISTINCT(`cores`) AS `cores`') -> where('visibility', 1)
                                                                         -> where('price', '>=', $productMinPrice)
                                                                         -> where('price', '<=', $productMaxPrice)
                                                                         -> get();

           foreach($data['configuration']['conditions'] as $key => $value)
           {
             $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('processors') -> where('conditionId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();
           }

           foreach($data['configuration']['stockTypes'] as $key => $value)
           {
             $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('processors') -> where('stockTypeId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();
           }

           foreach($data['configuration']['cpuCores'] as $key => $value)
           {
             $data['configuration']['cpuCores'][$key] -> numOfProducts = \DB::table('processors') -> where('cores', '=', $value -> cores)
                                                                                                  -> where('visibility', 1)
                                                                                                  -> where('price', '>=', $productMinPrice)
                                                                                                  -> where('price', '<=', $productMaxPrice)
                                                                                                  -> count();
           }

           foreach($data['configuration']['cpuTechnologyProcesses'] as $key => $value)
           {
             $data['configuration']['cpuTechnologyProcesses'][$key] -> numOfProducts = \DB::table('processors') -> where('technologyProcessId', $value -> id)
                                                                                                                -> where('visibility', 1)
                                                                                                                -> where('price', '>=', $productMinPrice)
                                                                                                                -> where('price', '<=', $productMaxPrice)
                                                                                                                -> count();
           }

           foreach($data['configuration']['cpuManufacturers'] as $key => $value)
           {
             $data['configuration']['cpuManufacturers'][$key] -> numOfProducts = \DB::table('processors') -> where('manufacturerId', $value -> id)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
           }

           foreach($data['configuration']['cpuSockets'] as $key => $value)
           {
             $data['configuration']['cpuSockets'][$key] -> numOfProducts = \DB::table('processors') -> where('socketId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();
           }

           foreach($data['configuration']['cpuSeries'] as $key => $value)
           {
             $data['configuration']['cpuSeries'][$key] -> numOfProducts = \DB::table('processors') -> where('seriesId', $value -> id)
                                                                                                   -> where('visibility', 1)
                                                                                                   -> where('price', '>=', $productMinPrice)
                                                                                                   -> where('price', '<=', $productMaxPrice)
                                                                                                   -> count();
           }

           foreach($data['processors'] as $key => $value)
           {
             $data['processors'][$key] -> newPrice = $value -> price - $value -> discount;
           }

           $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

           $data['pages'] = $paginator -> pages;
           $data['maxPage'] = $paginator -> maxPage;
           $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(Processor::class);

      return View::make('contents.shop.processors.index', ['contentData' => $data,
                                                           'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $defaultNumberOfProductsToShow = 12;
      $pricePart = 0.2;
      $columns = ['processors.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['processor'] = Processor::where('visibility', 1) -> find($id, $columns);
      $data['processorExists'] = !is_null($data['processor']);

      if ($data['processorExists'])
      {
        $generalData['seoFields'] -> description = $data['processor'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['processor'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['processor'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['processor'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('processors_images') -> where('processorId', '=', $data['processor'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['processor'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['processor'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['processor'] -> newPrice = $data['processor'] -> price - $data['processor'] -> discount;
        $data['processor'] -> categoryId = BaseModel::getTableAliasByModelName(Processor::class);

        $percent = $data['processor'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['processor'] -> newPrice - $percent);
        $rightRange = (int) ($data['processor'] -> newPrice + $percent);

        $fields = ['processors.id', 'title', 'mainImage', 'discount', 'price', 'clockSpeed', 'socketTitle', 'size', 'cores'];
        $query = \DB::table('processors') -> select($fields)
                                          -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processors.socketId')
                                          -> join('cpu_technology_processes', 'cpu_technology_processes.id', '=', 'processors.technologyProcessId')
                                          -> where('visibility', 1)
                                          -> where('price', '<=', $rightRange)
                                          -> where('price', '>=', $leftRange)
                                          -> where('processors.id', '!=', $data['processor'] -> id);

        $data['recommendedProcessors'] = $query -> take($defaultNumberOfProductsToShow) -> get();
        $data['recommendedProcessorsExist'] = $query -> count() != 0;

        if ($data['recommendedProcessorsExist'])
        {
          $data['recommendedProcessors'] -> each(function($processor){

               $processor -> newPrice = $processor -> price - $processor -> discount;
          });
        }

        BaseModel::collectStatisticalData(Processor::class);

        return View::make('contents.shop.processors.view', ['contentData' => $data,
                                                            'generalData' => $generalData]);
      }

      else abort(404);
    }
}
