<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\ProcessorCooler;

class ProcessorCoolerController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 6;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(ProcessorCooler::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'size' => 'required|string',
                                                  'cpu-socket' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> processorCoolerMinPrice && $priceFrom <= $priceRange -> processorCoolerMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> processorCoolerMinPrice && $priceTo <= $priceRange -> processorCoolerMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $cpuSockets = \DB::table('cpu_sockets') -> get();
            $cpuSocketsExist = $cpuSockets -> count() != 0;

            $coolersSizes = \DB::table('processor_coolers') -> selectRaw('DISTINCT(`size`) AS `size`') -> get();
            $coolersSizesExist = $coolersSizes -> count() != 0;

            if ($coolersSizesExist && $cpuSocketsExist && $stockTypeExists && $conditionExists)
            {
              $cpuSocketsParts = array_map('intval', explode(':', $parameters['cpu-socket']));
              $coolersSizesParts = array_map('intval', explode(':', $parameters['size']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = '`processor_coolers`.`id`,`title`,`mainImage`,`discount`,`price`,`stockTypeId`,`enableAddToCartButton`,GROUP_CONCAT(`socketTitle` SEPARATOR ", ") AS `socketTitle`';
              $cpuSocketsNumbers = $coolersSizesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('processor_coolers_and_sockets') -> selectRaw($columns)
                                                                   -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processor_coolers_and_sockets.socketId')
                                                                   -> join('processor_coolers', 'processor_coolers.id', '=', 'processor_coolers_and_sockets.processorCoolerId')
                                                                   -> join('stock_types', 'stock_types.id', '=', 'processor_coolers.stockTypeId')
                                                                   -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($cpuSockets as $value) $cpuSocketsNumbers[] = $value -> id;
              foreach($coolersSizes as $value) $coolersSizesNumbers[] = $value -> size;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($cpuSocketsParts, $cpuSocketsNumbers) == $cpuSocketsParts) $query = $query -> whereIn('socketId', $cpuSocketsParts);
              if (array_intersect($coolersSizesParts, $coolersSizesNumbers) == $coolersSizesParts) $query = $query -> whereIn('size', $coolersSizesParts);

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = $productsOrder == 1 || $productsOrder == 2 ? 'price' : 'timestamp';

                $query = $query -> orderBy($orderColumn, $orderNumber == 0 ? 'desc' : 'asc');
              }

              $query = $query -> groupBy('processorCoolerId');
              $currentPage = abs((int) $parameters['active-page']);
              $totalNumOfProducts = $query -> getCountForPagination();

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

      return View::make('contents.shop.processorCoolers.getProcessorCoolers', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(ProcessorCooler::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['processorCoolersExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> processorCoolerMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> processorCoolerMaxPrice;

        $totalNumOfProducts = ProcessorCooler::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['coolers'] = \DB::table('processor_coolers_and_sockets') -> selectRaw('`processor_coolers`.`id`,`title`,`mainImage`,`discount`,`price`,`stockTypeId`,`enableAddToCartButton`,GROUP_CONCAT(`socketTitle` SEPARATOR ", ") AS `socketTitle`')
                                                                       -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processor_coolers_and_sockets.socketId')
                                                                       -> join('processor_coolers', 'processor_coolers.id', '=', 'processor_coolers_and_sockets.processorCoolerId')
                                                                       -> join('stock_types', 'stock_types.id', '=', 'processor_coolers.stockTypeId')
                                                                       -> where('visibility', '1')
                                                                       -> where('price', '>=', $productMinPrice)
                                                                       -> where('price', '<=', $productMaxPrice)
                                                                       -> groupBy('processorCoolerId')
                                                                       -> skip(($page - 1) * $numOfProductsToView)
                                                                       -> take($numOfProductsToView)
                                                                       -> get();

        $data['processorCoolersExist'] = !$data['coolers'] -> isEmpty();

        if ($data['processorCoolersExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(ProcessorCooler::class);

          $data['configuration']['cpuSockets'] = \DB::table('cpu_sockets') -> get();
          $data['configuration']['coolersSizes'] = \DB::table('processor_coolers') -> selectRaw('DISTINCT(`size`) AS `size`') -> where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          foreach($data['configuration']['cpuSockets'] as $key => $value)
          {
            $data['configuration']['cpuSockets'][$key] -> numOfProducts = \DB::table('processor_coolers_and_sockets') -> join('processor_coolers', 'processor_coolers.id', '=', 'processor_coolers_and_sockets.processorCoolerId')
                                                                                                                      -> where('socketId', $value -> id)
                                                                                                                      -> where('visibility', 1)
                                                                                                                      -> where('price', '>=', $productMinPrice)
                                                                                                                      -> where('price', '<=', $productMaxPrice)
                                                                                                                      -> count();
          }

          foreach($data['configuration']['coolersSizes'] as $key => $value)
          {
            $data['configuration']['coolersSizes'][$key] -> numOfProducts = \DB::table('processor_coolers') -> where('size', '=', $value -> size)
                                                                                                            -> where('visibility', 1)
                                                                                                            -> where('price', '>=', $productMinPrice)
                                                                                                            -> where('price', '<=', $productMaxPrice)
                                                                                                            -> count();
          }

          foreach($data['configuration']['conditions'] as $key => $value)
          {
            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('processor_coolers') -> where('conditionId', $value -> id)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['configuration']['stockTypes'] as $key => $value)
          {
            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('processor_coolers') -> where('stockTypeId', $value -> id)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['coolers'] as $key => $value)
          {
            $data['coolers'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(ProcessorCooler::class);

      return View::make('contents.shop.processorCoolers.index', ['contentData' => $data,
                                                                 'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;
      $pricePart = 0.2;
      $columns = ['processor_coolers.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['cooler'] = \DB::table('processor_coolers') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['processorCoolerExists'] = !is_null($data['cooler']);

      if ($data['processorCoolerExists'])
      {
        $generalData['seoFields'] -> description = $data['cooler'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['cooler'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['cooler'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['cooler'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('processor_coolers_images') -> where('processorCoolerId', '=', $data['cooler'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['cooler'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['cooler'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['cooler'] -> newPrice = $data['cooler'] -> price - $data['cooler'] -> discount;
        $data['cooler'] -> categoryId = BaseModel::getTableAliasByModelName(ProcessorCooler::class);

        $percent = $data['cooler'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['cooler'] -> newPrice - $percent);
        $rightRange = (int) ($data['cooler'] -> newPrice + $percent);

        $fields = '`processor_coolers`.`id`,`title`,`mainImage`,`discount`,`price`,GROUP_CONCAT(`socketTitle` SEPARATOR ", ") AS `socketTitle`';

        $data['recommendedCoolers'] = \DB::table('processor_coolers_and_sockets') -> selectRaw($fields)
                                                                                  -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processor_coolers_and_sockets.socketId')
                                                                                  -> join('processor_coolers', 'processor_coolers.id', '=', 'processor_coolers_and_sockets.processorCoolerId')
                                                                                  -> where('visibility', 1)
                                                                                  -> where('price', '<=', $rightRange)
                                                                                  -> where('price', '>=', $leftRange)
                                                                                  -> where('processor_coolers.id', '!=', $data['cooler'] -> id)
                                                                                  -> groupBy('processorCoolerId')
                                                                                  -> take($numOfProductsToView)
                                                                                  -> get();

        $data['recommendedCoolersExist'] = !$data['recommendedCoolers'] -> isEmpty();

        if ($data['recommendedCoolersExist'])
        {
          foreach($data['recommendedCoolers'] as $key => $value)

          $data['recommendedCoolers'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(ProcessorCooler::class);

        return View::make('contents.shop.processorCoolers.view', ['contentData' => $data,
                                                                  'generalData' => $generalData]);
      }

      else abort(404);
    }
}
