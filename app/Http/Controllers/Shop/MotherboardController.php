<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Motherboard;

class MotherboardController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 9;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(Motherboard::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'motherboard-manufacturer' => 'required|string',
                                                  'cpu-socket' => 'required|string',
                                                  'form-factor' => 'required|string',
                                                  'memory-type' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> motherboardMinPrice && $priceFrom <= $priceRange -> motherboardMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> motherboardMinPrice && $priceTo <= $priceRange -> motherboardMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $memoryTypes = \DB::table('memory_modules_types') -> get();
            $memoryTypesExist = $memoryTypes -> count() != 0;

            $motherboardManufacturers = \DB::table('motherboards_manufacturers') -> get();
            $motherboardManufacturersExist = $motherboardManufacturers -> count() != 0;

            $cpuSockets = \DB::table('cpu_sockets') -> get();
            $cpuSocketsExist = $cpuSockets -> count() != 0;

            $formFactors = \DB::table('case_form_factors') -> get();
            $formFactorsExist = $formFactors -> count() != 0;

            if ($memoryTypesExist && $motherboardManufacturersExist && $cpuSocketsExist && $formFactorsExist && $stockTypeExists && $conditionExists)
            {
              $motherboardManufacturersParts = array_map('intval', explode(':', $parameters['motherboard-manufacturer']));
              $memoryTypesParts = array_map('intval', explode(':', $parameters['memory-type']));
              $cpuSocketsParts = array_map('intval', explode(':', $parameters['cpu-socket']));
              $formFactorsParts = array_map('intval', explode(':', $parameters['form-factor']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['motherboards.id', 'title', 'mainImage', 'discount', 'price', 'socketTitle', 'manufacturerTitle', 'formFactorTitle', 'typeTitle', 'stockTypeId', 'enableAddToCartButton'];
              $memoryTypesNumbers = $formFactorsNumbers = $motherboardManufacturersNumbers = $cpuSocketsNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('motherboards') -> select($columns)
                                                  -> join('cpu_sockets', 'cpu_sockets.id', '=', 'motherboards.socketId')
                                                  -> join('motherboards_manufacturers', 'motherboards_manufacturers.id', '=', 'motherboards.manufacturerId')
                                                  -> join('memory_modules_types', 'memory_modules_types.id', '=', 'motherboards.memoryTypeId')
                                                  -> join('case_form_factors', 'case_form_factors.id', '=', 'motherboards.formFactorId')
                                                  -> join('stock_types', 'stock_types.id', '=', 'motherboards.stockTypeId')
                                                  -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($memoryTypes as $value) $memoryTypesNumbers[] = $value -> id;
              foreach($formFactors as $value) $formFactorsNumbers[] = $value -> id;
              foreach($motherboardManufacturers as $value) $motherboardManufacturersNumbers[] = $value -> id;
              foreach($cpuSockets as $value) $cpuSocketsNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($memoryTypesParts, $memoryTypesNumbers) == $memoryTypesParts) $query = $query -> whereIn('memoryTypeId', $memoryTypesParts);
              if (array_intersect($formFactorsParts, $formFactorsNumbers) == $formFactorsParts) $query = $query -> whereIn('formFactorId', $formFactorsParts);
              if (array_intersect($motherboardManufacturersParts, $motherboardManufacturersNumbers) == $motherboardManufacturersParts) $query = $query -> whereIn('manufacturerId', $motherboardManufacturersParts);
              if (array_intersect($cpuSocketsParts, $cpuSocketsNumbers) == $cpuSocketsParts) $query = $query -> whereIn('socketId', $cpuSocketsParts);

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = $productsOrder == 1 || $productsOrder == 2 ? 'price' : 'timestamp';

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

      return View::make('contents.shop.motherboards.getMotherboards', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Motherboard::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['motherboardsExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> motherboardMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> motherboardMaxPrice;

        $totalNumOfProducts = Motherboard::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['motherboards'] = \DB::table('motherboards') -> select(['motherboards.id', 'title', 'mainImage', 'discount', 'price', 'socketTitle', 'manufacturerTitle', 'formFactorTitle', 'typeTitle', 'stockTypeId', 'enableAddToCartButton'])
                                                           -> join('cpu_sockets', 'cpu_sockets.id', '=', 'motherboards.socketId')
                                                           -> join('motherboards_manufacturers', 'motherboards_manufacturers.id', '=', 'motherboards.manufacturerId')
                                                           -> join('case_form_factors', 'case_form_factors.id', '=', 'motherboards.formFactorId')
                                                           -> join('memory_modules_types', 'memory_modules_types.id', '=', 'motherboards.memoryTypeId')
                                                           -> join('stock_types', 'stock_types.id', '=', 'motherboards.stockTypeId')
                                                           -> where('visibility', 1)
                                                           -> where('price', '>=', $productMinPrice)
                                                           -> where('price', '<=', $productMaxPrice)
                                                           -> skip(($page - 1) * $numOfProductsToView)
                                                           -> take($numOfProductsToView)
                                                           -> get();

        $data['motherboardsExist'] = !$data['motherboards'] -> isEmpty();

        if ($data['motherboardsExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Motherboard::class);

          $data['configuration']['numOfProductsToShow'] = $numOfProductsToView;

          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          $data['configuration']['motherboardsManufacturers'] = \DB::table('motherboards_manufacturers') -> get();
          $data['configuration']['cpuSockets'] = \DB::table('cpu_sockets') -> get();
          $data['configuration']['formFactors'] = \DB::table('case_form_factors') -> get();
          $data['configuration']['memoryTypes'] = \DB::table('memory_modules_types') -> get();

          foreach($data['configuration']['memoryTypes'] as $key => $value)

          $data['configuration']['memoryTypes'][$key] -> numOfProducts = \DB::table('motherboards') -> where('memoryTypeId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

          foreach($data['configuration']['formFactors'] as $key => $value)

          $data['configuration']['formFactors'][$key] -> numOfProducts = \DB::table('motherboards') -> where('formFactorId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

          foreach($data['configuration']['conditions'] as $key => $value)

          $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('motherboards') -> where('conditionId', $value -> id)
                                                                                                   -> where('visibility', 1)
                                                                                                   -> where('price', '>=', $productMinPrice)
                                                                                                   -> where('price', '<=', $productMaxPrice)
                                                                                                   -> count();

          foreach($data['configuration']['stockTypes'] as $key => $value)

          $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('motherboards') -> where('stockTypeId', $value -> id)
                                                                                                   -> where('visibility', 1)
                                                                                                   -> where('price', '>=', $productMinPrice)
                                                                                                   -> where('price', '<=', $productMaxPrice)
                                                                                                   -> count();

          foreach($data['configuration']['cpuSockets'] as $key => $value)

          $data['configuration']['cpuSockets'][$key] -> numOfProducts = \DB::table('motherboards') -> where('socketId', $value -> id)
                                                                                                   -> where('visibility', 1)
                                                                                                   -> where('price', '>=', $productMinPrice)
                                                                                                   -> where('price', '<=', $productMaxPrice)
                                                                                                   -> count();

          foreach($data['configuration']['motherboardsManufacturers'] as $key => $value)

          $data['configuration']['motherboardsManufacturers'][$key] -> numOfProducts = \DB::table('motherboards') -> where('manufacturerId', $value -> id)
                                                                                                                  -> where('visibility', 1)
                                                                                                                  -> where('price', '>=', $productMinPrice)
                                                                                                                  -> where('price', '<=', $productMaxPrice)
                                                                                                                  -> count();

          foreach($data['motherboards'] as $key => $value)
          {
            $data['motherboards'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(Motherboard::class);

      return View::make('contents.shop.motherboards.index', ['contentData' => $data,
                                                             'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;
      $pricePart = 0.2;
      $columns = ['motherboards.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['motherboard'] = Motherboard::find($id, $columns);
      $data['motherboardExists'] = !is_null($data['motherboard']);

      if ($data['motherboardExists'])
      {
        $generalData['seoFields'] -> description = $data['motherboard'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['motherboard'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['motherboard'] -> title;

        $stockType = $data['motherboard'] -> stockType;
        $condition = $data['motherboard'] -> condition;
        $warranty = $data['motherboard'] -> warranty;

        $data['images'] = \DB::table('motherboards_images') -> where('motherboardId', '=', $data['motherboard'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockType -> stockTitle;
        $data['stockStatusColor'] = $stockType -> statusColor;
        $data['enableAddToCartButton'] = $stockType -> enableAddToCartButton;

        $data['conditionTitle'] = $condition -> conditionTitle;
        $data['warrantyTitle'] = $warranty -> durationUnit;

        $data['motherboard'] -> newPrice = $data['motherboard'] -> price - $data['motherboard'] -> discount;
        $data['motherboard'] -> categoryId = BaseModel::getTableAliasByModelName(Motherboard::class);

        $percent = $data['motherboard'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['motherboard'] -> newPrice - $percent);
        $rightRange = (int) ($data['motherboard'] -> newPrice + $percent);

        $fields = ['motherboards.id', 'title', 'mainImage', 'discount', 'price', 'socketTitle', 'manufacturerTitle', 'formFactorTitle', 'typeTitle'];
        $query = \DB::table('motherboards') -> select($fields)
                                            -> join('cpu_sockets', 'cpu_sockets.id', '=', 'motherboards.socketId')
                                            -> join('motherboards_manufacturers', 'motherboards_manufacturers.id', '=', 'motherboards.manufacturerId')
                                            -> join('memory_modules_types', 'memory_modules_types.id', '=', 'motherboards.memoryTypeId')
                                            -> join('case_form_factors', 'case_form_factors.id', '=', 'motherboards.formFactorId')
                                            -> where('visibility', 1)
                                            -> where('price', '<=', $rightRange)
                                            -> where('price', '>=', $leftRange)
                                            -> where('motherboards.id', '!=', $data['motherboard'] -> id);

        $data['recommendedMotherboards'] = $query -> take($numOfProductsToView) -> get();
        $data['recommendedProductsExist'] = !$data['recommendedMotherboards'] -> isEmpty();

        foreach($data['recommendedMotherboards'] as $key => $value)
        {
          $data['recommendedMotherboards'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(Motherboard::class);

        return View::make('contents.shop.motherboards.view', ['contentData' => $data,
                                                              'generalData' => $generalData]);
      }

      else abort(404);
    }
}
