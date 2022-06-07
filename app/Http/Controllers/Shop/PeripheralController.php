<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Peripheral;

class PeripheralController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 6;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(Peripheral::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'type' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> peripheralMinPrice && $priceFrom <= $priceRange -> peripheralMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> peripheralMinPrice && $priceTo <= $priceRange -> peripheralMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $peripheralsTypes = \DB::table('peripherals_types') -> get();
            $peripheralsTypesExist = $peripheralsTypes -> count() != 0;

            if ($peripheralsTypesExist && $stockTypeExists && $conditionExists)
            {
              $peripheralsTypesParts = array_map('intval', explode(':', $parameters['type']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['peripherals.id', 'title', 'mainImage', 'discount', 'price', 'enableAddToCartButton'];
              $peripheralsTypesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('peripherals') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'peripherals.stockTypeId') -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($peripheralsTypes as $value) $peripheralsTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($peripheralsTypesParts, $peripheralsTypesNumbers) == $peripheralsTypesParts) $query = $query -> whereIn('typeId', $peripheralsTypesParts);

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

      return View::make('contents.shop.peripherals.getPeripherals', ['data' => $data]);
    }

    public function index(int $categoryId = 0, int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['categoryId'] = $categoryId;
      $data['peripheralsExist'] = false;

      $data['configuration']['peripheralsTypes'] = \DB::table('peripherals_types') -> get();
      $data['configuration']['peripheralsTypesExist'] = !$data['configuration']['peripheralsTypes'] -> isEmpty();

      if ($data['configuration']['peripheralsTypesExist'])
      {
        $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Peripheral::class);
        $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);

        if ($data['configuration']['productPriceRangeExists'])
        {
          $productMinPrice = $data['configuration']['productPriceRange'] -> peripheralMinPrice;
          $productMaxPrice = $data['configuration']['productPriceRange'] -> peripheralMaxPrice;

          $productsModel = Peripheral::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice);

          $productsQuery = \DB::table('peripherals') -> select(['peripherals.id', 'title', 'mainImage', 'price', 'discount', 'enableAddToCartButton'])
                                                     -> join('stock_types', 'stock_types.id', '=', 'peripherals.stockTypeId')
                                                     -> where('visibility', 1)
                                                     -> where('price', '>=', $productMinPrice)
                                                     -> where('price', '<=', $productMaxPrice);

          if ($categoryId != 0)
          {
            $productsModel = $productsModel -> where('typeId', '=', $categoryId);
            $productsQuery = $productsQuery -> where('typeId', '=', $categoryId);
          }

          $totalNumOfProducts = $productsModel -> count();

          $data['peripherals'] = $productsQuery -> skip(($page - 1) * $numOfProductsToView) -> take($numOfProductsToView) -> get();

          $data['peripheralsExist'] = !$data['peripherals'] -> isEmpty();

          if ($data['peripheralsExist'])
          {
            $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Peripheral::class);

            $data['configuration']['peripheralTypes'] = \DB::table('peripherals_types') -> get();
            $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
            $data['configuration']['conditions'] = \DB::table('conditions') -> get();

            foreach($data['configuration']['peripheralTypes'] as $key => $value)

            $data['configuration']['peripheralTypes'][$key] -> numOfProducts = \DB::table('peripherals') -> where('typeId', $value -> id)
                                                                                                         -> where('visibility', 1)
                                                                                                         -> where('price', '>=', $productMinPrice)
                                                                                                         -> where('price', '<=', $productMaxPrice)
                                                                                                         -> count();

            foreach($data['configuration']['conditions'] as $key => $value)

            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('peripherals') -> where('conditionId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

            foreach($data['configuration']['stockTypes'] as $key => $value)

            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('peripherals') -> where('stockTypeId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

            foreach($data['peripherals'] as $key => $value)
            {
              $data['peripherals'][$key] -> newPrice = $value -> price - $value -> discount;
            }

            $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

            $data['pages'] = $paginator -> pages;
            $data['maxPage'] = $paginator -> maxPage;
            $data['currentPage'] = $paginator -> currentPage;
          }
        }
      }

      BaseModel::collectStatisticalData(Peripheral::class);

      return View::make('contents.shop.peripherals.index', ['contentData' => $data,
                                                            'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $columns = ['peripherals.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'seoKeywords', 'seoDescription', 'quantity'];

      $data['peripheral'] = \DB::table('peripherals') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['peripheralExists'] = !is_null($data['peripheral']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['peripheralExists'])
      {
        $generalData['seoFields'] -> description = $data['peripheral'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['peripheral'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['peripheral'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['peripheral'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('peripherals_images') -> where('peripheralId', '=', $data['peripheral'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['peripheral'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['peripheral'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['peripheral'] -> newPrice = $data['peripheral'] -> price - $data['peripheral'] -> discount;
        $data['peripheral'] -> categoryId = BaseModel::getTableAliasByModelName(Peripheral::class);

        $percent = $data['peripheral'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['peripheral'] -> newPrice - $percent);
        $rightRange = (int) ($data['peripheral'] -> newPrice + $percent);
        $fields = ['peripherals.id', 'title', 'mainImage', 'discount', 'price'];

        $data['recommendedPeripherals'] = \DB::table('peripherals') -> select($fields)
                                                                    -> where('visibility', 1)
                                                                    -> where('price', '<=', $rightRange)
                                                                    -> where('price', '>=', $leftRange)
                                                                    -> where('peripherals.id', '!=', $data['peripheral'] -> id)
                                                                    -> take($numOfProductsToView)
                                                                    -> get();

        $data['recommendedPeripheralsExist'] = !$data['recommendedPeripherals'] -> isEmpty();

        if ($data['recommendedPeripheralsExist'])

        foreach($data['recommendedPeripherals'] as $key => $value)
        {
          $data['recommendedPeripherals'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(Peripheral::class);

        return View::make('contents.shop.peripherals.view', ['contentData' => $data,
                                                             'generalData' => $generalData]);
      }

      else abort(404);
    }
}
