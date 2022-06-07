<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\PowerSupply;

class PowerSupplyController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 15;
      $supportedOrders = [1, 2, 3, 4, 5, 6];
      $priceRange = BaseModel::getPriceRange(powerSupply::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'power-from' => 'required|integer',
                                                  'power-to' => 'required|integer']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $powerFrom = abs((int) $parameters['power-from']);
          $powerTo = abs((int) $parameters['power-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> powerSupplyMinPrice && $priceFrom <= $priceRange -> powerSupplyMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> powerSupplyMinPrice && $priceTo <= $priceRange -> powerSupplyMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange && $powerFrom != 0 && $powerTo != 0 && $powerFrom <= $powerTo)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $powerRange = \DB::table('power_supplies') -> selectRaw('MIN(`power`) AS `minPower`, MAX(`power`) AS `maxPower`') -> first();
            $powerRangeExists = !is_null($powerRange);

            if ($stockTypeExists && $conditionExists && $powerRangeExists)
            {
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['power_supplies.id', 'title', 'mainImage', 'discount', 'price', 'power', 'stockTypeId', 'enableAddToCartButton'];
              $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('power_supplies') -> select($columns) -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);

              $query = $query -> join('stock_types', 'stock_types.id', '=', 'power_supplies.stockTypeId') -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo) -> where('power', '>=', $powerFrom) -> where('power', '<=', $powerTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = 'price';

                if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'power';

                else if ($productsOrder == 5 || $productsOrder == 6) $orderColumn = 'timestamp';

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

      return View::make('contents.shop.powerSupplies.getPowerSupplies', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(PowerSupply::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['powerSuppliesExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> powerSupplyMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> powerSupplyMaxPrice;

        $totalNumOfProducts = PowerSupply::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['powerSupplies'] = \DB::table('power_supplies') -> select(['power_supplies.id', 'title', 'mainImage', 'price', 'discount', 'power', 'stockTypeId', 'enableAddToCartButton'])
                                                              -> join('stock_types', 'stock_types.id', '=', 'power_supplies.stockTypeId')
                                                              -> where('visibility', 1)
                                                              -> where('price', '>=', $productMinPrice)
                                                              -> where('price', '<=', $productMaxPrice)
                                                              -> skip(($page - 1) * $numOfProductsToView)
                                                              -> take($numOfProductsToView)
                                                              -> get();

        $data['powerSuppliesExist'] = !$data['powerSupplies'] -> isEmpty();

        if ($data['powerSuppliesExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(PowerSupply::class);

          $data['configuration']['minPower'] = \DB::table('power_supplies') -> selectRaw('MIN(`power`) as `minPower`')
                                                                            -> where('visibility', 1)
                                                                            -> where('price', '>=', $productMinPrice)
                                                                            -> where('price', '<=', $productMaxPrice)
                                                                            -> get()
                                                                            -> first()
                                                                            -> minPower;

          $data['configuration']['maxPower'] = \DB::table('power_supplies') -> selectRaw('MAX(`power`) as `maxPower`')
                                                                            -> where('visibility', 1)
                                                                            -> where('price', '>=', $productMinPrice)
                                                                            -> where('price', '<=', $productMaxPrice)
                                                                            -> get()
                                                                            -> first()
                                                                            -> maxPower;

          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();

          foreach($data['configuration']['conditions'] as $key => $value)
          {
            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('power_supplies') -> where('conditionId', $value -> id)
                                                                                                       -> where('visibility', 1)
                                                                                                       -> where('price', '>=', $productMinPrice)
                                                                                                       -> where('price', '<=', $productMaxPrice)
                                                                                                       -> count();
          }

          foreach($data['configuration']['stockTypes'] as $key => $value)
          {
            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('power_supplies') -> where('stockTypeId', $value -> id)
                                                                                                       -> where('visibility', 1)
                                                                                                       -> where('price', '>=', $productMinPrice)
                                                                                                       -> where('price', '<=', $productMaxPrice)
                                                                                                       -> count();
          }

          foreach($data['powerSupplies'] as $key => $value)
          {
            $data['powerSupplies'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(PowerSupply::class);

      return View::make('contents.shop.powerSupplies.index', ['contentData' => $data,
                                                              'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;
      $pricePart = 0.2;
      $columns = ['power_supplies.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['powerSupply'] = \DB::table('power_supplies') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['powerSupplyExists'] = !is_null($data['powerSupply']);

      if ($data['powerSupplyExists'])
      {
        $generalData['seoFields'] -> description = $data['powerSupply'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['powerSupply'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['powerSupply'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['powerSupply'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('power_supplies_images') -> where('powerSupplyId', '=', $data['powerSupply'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['powerSupply'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['powerSupply'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['powerSupply'] -> newPrice = $data['powerSupply'] -> price - $data['powerSupply'] -> discount;
        $data['powerSupply'] -> categoryId = BaseModel::getTableAliasByModelName(PowerSupply::class);

        $percent = $data['powerSupply'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['powerSupply'] -> newPrice - $percent);
        $rightRange = (int) ($data['powerSupply'] -> newPrice + $percent);

        $fields = ['power_supplies.id', 'title', 'mainImage', 'discount', 'price', 'power'];

        $data['recommendedPowerSupplies'] = \DB::table('power_supplies') -> select($fields)
                                                                         -> where('visibility', 1)
                                                                         -> where('price', '<=', $rightRange)
                                                                         -> where('price', '>=', $leftRange)
                                                                         -> where('power_supplies.id', '!=', $data['powerSupply'] -> id)
                                                                         -> take($numOfProductsToView)
                                                                         -> get();

        $data['recommendedPowerSuppliesExist'] = !$data['recommendedPowerSupplies'] -> isEmpty();

        if ($data['recommendedPowerSuppliesExist'])
        {
          foreach($data['recommendedPowerSupplies'] as $key => $value)

          $data['recommendedPowerSupplies'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(PowerSupply::class);

        return View::make('contents.shop.powerSupplies.view', ['contentData' => $data,
                                                               'generalData' => $generalData]);
      }

      else abort(404);
    }
}
