<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use \App\Helpers\Paginator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; // use View facade class

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Accessory;

class AccessoryController extends Controllers\Controller
{
    public function getList(Request $request) // injecting dependency into controller (passing object of type Request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 9;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(Accessory::class);

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

          $priceFromIsInRange = $priceFrom >= $priceRange -> accessoryMinPrice && $priceFrom <= $priceRange -> accessoryMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> accessoryMinPrice && $priceTo <= $priceRange -> accessoryMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $accessoryTypes = \DB::table('accessories_types') -> get();
            $accessoryTypesExist = $accessoryTypes -> count() != 0;

            if ($accessoryTypesExist && $stockTypeExists && $conditionExists)
            {
              $accessoryTypesParts = array_map('intval', explode(':', $parameters['type']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['accessories.id', 'title', 'mainImage', 'discount', 'price', 'enableAddToCartButton'];
              $accessoryTypesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('accessories') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'accessories.stockTypeId') -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($accessoryTypes as $value) $accessoryTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($accessoryTypesParts, $accessoryTypesNumbers) == $accessoryTypesParts) $query = $query -> whereIn('accessoryTypeId', $accessoryTypesParts);

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
                $maxPageNumber = ceil($totalNumOfProducts / $numOfProductsToView);
                $currentPage = $currentPage <= $maxPageNumber ? $currentPage : $maxPageNumber;

                $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $currentPage, 2, 0);

                $data['maxPage'] = $paginator -> maxPage;
                $data['pages'] = $paginator -> pages;
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

      return View::make('contents.shop.accessories.getAccessories', ['data' => $data]);
    }

    public function index(int $categoryId = 0, int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['categoryId'] = $categoryId;
      $data['accessoriesExist'] = false;

      $data['configuration']['accessoriesTypes'] = \DB::table('accessories_types') -> get();
      $data['configuration']['accessoriesTypesExist'] = !$data['configuration']['accessoriesTypes'] -> isEmpty();

      if ($data['configuration']['accessoriesTypesExist'])
      {
        $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Accessory::class);
        $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);

        if ($data['configuration']['productPriceRangeExists'])
        {
          $productMinPrice = $data['configuration']['productPriceRange'] -> accessoryMinPrice;
          $productMaxPrice = $data['configuration']['productPriceRange'] -> accessoryMaxPrice;

          $productsModel = Accessory::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice);

          $productsQuery = \DB::table('accessories') -> select(['accessories.id', 'title', 'mainImage', 'price', 'discount', 'enableAddToCartButton'])
                                                     -> join('stock_types', 'stock_types.id', '=', 'accessories.stockTypeId')
                                                     -> where('visibility', 1)
                                                     -> where('price', '>=', $productMinPrice)
                                                     -> where('price', '<=', $productMaxPrice);

          if ($categoryId != 0)
          {
            $productsModel = $productsModel -> where('accessoryTypeId', '=', $categoryId);
            $productsQuery = $productsQuery -> where('accessoryTypeId', '=', $categoryId);
          }

          $totalNumOfProducts = $productsModel -> count();

          $data['accessories'] = $productsQuery -> skip(($page - 1) * $numOfProductsToView) -> take($numOfProductsToView) -> get();

          $data['accessoriesExist'] = !$data['accessories'] -> isEmpty();

          if ($data['accessoriesExist'])
          {
            $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Accessory::class);

            $data['configuration']['accessoryTypes'] = \DB::table('accessories_types') -> get();
            $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
            $data['configuration']['conditions'] = \DB::table('conditions') -> get();

            foreach($data['configuration']['accessoryTypes'] as $key => $value)

            $data['configuration']['accessoryTypes'][$key] -> numOfProducts = \DB::table('accessories') -> where('accessoryTypeId', $value -> id)
                                                                                                        -> where('visibility', 1)
                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                        -> count();

            foreach($data['configuration']['conditions'] as $key => $value)

            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('accessories') -> where('conditionId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

            foreach($data['configuration']['stockTypes'] as $key => $value)

            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('accessories') -> where('stockTypeId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();

            foreach($data['accessories'] as $key => $value)
            {
              $data['accessories'][$key] -> newPrice = $value -> price - $value -> discount;
            }

            $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

            $data['pages'] = $paginator -> pages;
            $data['maxPage'] = $paginator -> maxPage;
            $data['currentPage'] = $paginator -> currentPage;
          }
        }
      }

      BaseModel::collectStatisticalData(Accessory::class);

      return View::make('contents.shop.accessories.index') -> with(['contentData' => $data,
                                                                    'generalData' => $generalData]); // is identical to view() global helper function
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();

      $columns = ['accessories.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'seoKeywords', 'seoDescription', 'quantity'];

      $data['accessory'] = Accessory::find($id, $columns);
      $data['accessoryExists'] = !is_null($data['accessory']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['accessoryExists'])
      {
        $generalData['seoFields'] -> description = $data['accessory'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['accessory'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['accessory'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['accessory'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('accessories_images') -> where('accessoryId', '=', $data['accessory'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['accessory'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['accessory'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['accessory'] -> newPrice = $data['accessory'] -> price - $data['accessory'] -> discount;
        $data['accessory'] -> categoryId = BaseModel::getTableAliasByModelName(Accessory::class);

        $percent = $data['accessory'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['accessory'] -> newPrice - $percent);
        $rightRange = (int) ($data['accessory'] -> newPrice + $percent);
        $fields = ['accessories.id', 'title', 'mainImage', 'discount', 'price'];

        $data['recommendedAccessories'] = \DB::table('accessories') -> select($fields)
                                                                    -> where('visibility', 1)
                                                                    -> where('price', '<=', $rightRange)
                                                                    -> where('price', '>=', $leftRange)
                                                                    -> where('accessories.id', '!=', $data['accessory'] -> id)
                                                                    -> take($numOfProductsToView)
                                                                    -> get();

        $data['recommendedAccessoriesExist'] = !$data['recommendedAccessories'] -> isEmpty();

        if ($data['recommendedAccessoriesExist'])
        {
          $data['recommendedAccessories'] -> each(function($accessory){

              $accessory -> newPrice = $accessory -> price - $accessory -> discount;
          });
        }

        BaseModel::collectStatisticalData(Accessory::class);

        return View::make('contents.shop.accessories.view', ['contentData' => $data,
                                                             'generalData' => $generalData]);
      }

      else abort(404);
    }

    public function getAccessoriesForHomePage($id)
    {
      $categoryId = (int) $id;

      $numberOfAccessoriesToView = 20;

      $accessories = \DB::table('accessories') -> select(['accessories.id', 'title', 'mainImage', 'price', 'discount']) -> where('visibility', 1) -> where('accessoryTypeId', '=', $categoryId) -> take($numberOfAccessoriesToView) -> get();

      $accessoriesExist = !$accessories -> isEmpty();

      return View::make('contents.shop.accessories.getAccessoriesForHomePage', ['accessories' => $accessories,
                                                                                'accessoriesExist' => $accessoriesExist]);
    }
}
