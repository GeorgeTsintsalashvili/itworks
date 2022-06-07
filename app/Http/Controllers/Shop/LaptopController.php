<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Laptop;

class LaptopController extends Controllers\Controller
{
  public function getList(Request $request)
  {
    $data['productsExist'] = false;

    $numOfProductsToView = 15;
    $supportedOrders = [1, 2, 3, 4, 5, 6];

    $priceRange = BaseModel::getPriceRange(Laptop::class);

    $parameters = $request -> all(); // user input

    $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                'price-from' => 'required|integer',
                                                'price-to' => 'required|integer',
                                                'order' => 'required|integer',
                                                'numOfProductsToShow' => 'required|integer',
                                                'stock-type' => 'required|string',
                                                'condition' => 'required|string',
                                                'laptop-system' => 'required|string',
                                                'diagonal-from' => 'required|string',
                                                'diagonal-to' => 'required|string',
                                                'memory' => 'required|string']);

    if (!$validator -> fails() && !is_null($priceRange))
    {
      $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
      $productsOrder = abs((int) $parameters['order']);

      if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
      {
        $priceFrom = abs((int) $parameters['price-from']);
        $priceTo = abs((int) $parameters['price-to']);

        $diagonalFrom = abs((int) $parameters['diagonal-from']);
        $diagonalTo = ceil(abs($parameters['diagonal-to']));

        $priceFromIsInRange = $priceFrom >= $priceRange -> laptopMinPrice && $priceFrom <= $priceRange -> laptopMaxPrice;
        $priceToIsInRange = $priceTo >= $priceRange -> laptopMinPrice && $priceTo <= $priceRange -> laptopMaxPrice;

        if ($priceFromIsInRange && $priceToIsInRange)
        {
          $conditions = \DB::table('conditions') -> get();
          $stockTypes = \DB::table('stock_types') -> get();
          $laptopSystems = \DB::table('laptop_systems') -> get();
          $memories = \DB::table('laptops') -> select(['memory']) -> distinct() -> get();
          $numOfLaptops = \DB::table('laptops') -> where('visibility', 1) -> count();

          if ($numOfLaptops != 0)
          {
            $laptopSystemsParts = array_map('intval', explode(':', $parameters['laptop-system']));
            $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
            $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));
            $memoriesParts = array_map('intval', explode(':', $parameters['memory']));

            $columns = ['laptops.id', 'title', 'mainImage', 'discount', 'price', 'laptopSystemTitle', 'diagonal', 'memory', 'enableAddToCartButton'];
            $laptopSystemsNumbers = $conditionNumbers = $stockTypesNumbers = $memoriesNumbers = [];

            $query = \DB::table('laptops') -> select($columns)
                                           -> join('laptop_systems', 'laptop_systems.id', '=', 'laptops.laptopSystemId')
                                           -> join('stock_types', 'stock_types.id', '=', 'laptops.stockTypeId')
                                           -> where('visibility', 1);

            foreach($conditions as $value) $conditionNumbers[] = $value -> id;
            foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
            foreach($laptopSystems as $value) $laptopSystemsNumbers[] = $value -> id;
            foreach($memories as $value) $memoriesNumbers[] = $value -> memory;

            if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
            if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
            if (array_intersect($laptopSystemsParts, $laptopSystemsNumbers) == $laptopSystemsParts) $query = $query -> whereIn('laptopSystemId', $laptopSystemsParts);
            if (array_intersect($memoriesParts, $memoriesNumbers) == $memoriesParts) $query = $query -> whereIn('memory', $memoriesParts);

            $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo) -> where('diagonal', '>=', $diagonalFrom) -> where('diagonal', '<=', $diagonalTo);

            if (in_array($productsOrder, $supportedOrders))
            {
              $orderNumber = !($productsOrder % 2);
              $orderColumn = 'price';

              if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'diagonal';

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

    return View::make('contents.shop.laptops.getLaptops', ['data' => $data]);
  }

  public function index(int $page = 1)
  {
    $generalData = BaseModel::getGeneralData();
    $numOfProductsToView = 9;

    $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(Laptop::class);
    $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
    $data['laptopsExist'] = false;

    if ($data['configuration']['productPriceRangeExists'])
    {
      $productMinPrice = $data['configuration']['productPriceRange'] -> laptopMinPrice;
      $productMaxPrice = $data['configuration']['productPriceRange'] -> laptopMaxPrice;

      $totalNumOfProducts = Laptop::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

      $data['laptops'] = \DB::table('laptops') -> select(['laptops.id', 'title', 'mainImage', 'discount', 'price', 'laptopSystemTitle', 'diagonal', 'memory', 'enableAddToCartButton'])
                                               -> join('laptop_systems', 'laptop_systems.id', '=', 'laptops.laptopSystemId')
                                               -> join('stock_types', 'stock_types.id', '=', 'laptops.stockTypeId')
                                               -> where('visibility', '1')
                                               -> where('price', '>=', $productMinPrice)
                                               -> where('price', '<=', $productMaxPrice)
                                               -> skip(($page - 1) * $numOfProductsToView)
                                               -> take($numOfProductsToView)
                                               -> get();

      $data['laptopsExist'] = !$data['laptops'] -> isEmpty();

      if ($data['laptopsExist'])
      {
        $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(Laptop::class);

        $data['configuration']['laptopSystems'] = \DB::table('laptop_systems') -> get();

        $range = \DB::table('laptops') -> select(\DB::raw('MIN(`diagonal`) AS `minDiagonal`, MAX(`diagonal`) AS `maxDiagonal`'))
                                       -> where('visibility', 1)
                                       -> where('price', '>=', $productMinPrice)
                                       -> where('price', '<=', $productMaxPrice)
                                       -> first();

        $data['configuration']['memories'] = \DB::table('laptops') -> select(['memory']) -> distinct() -> get();

        $data['configuration']['minDiagonal'] = $range -> minDiagonal;
        $data['configuration']['maxDiagonal'] = $range -> maxDiagonal;

        $data['configuration']['conditions'] = \DB::table('conditions') -> get();
        $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

        foreach($data['configuration']['memories'] as $key => $value)

        $data['configuration']['memories'][$key] -> numOfProducts = \DB::table('laptops') -> where('memory', $value -> memory)
                                                                                          -> where('visibility', 1)
                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                          -> count();

        foreach($data['configuration']['conditions'] as $key => $value)

        $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('laptops') -> where('conditionId', $value -> id)
                                                                                            -> where('visibility', 1)
                                                                                            -> where('price', '>=', $productMinPrice)
                                                                                            -> where('price', '<=', $productMaxPrice)
                                                                                            -> count();

        foreach($data['configuration']['stockTypes'] as $key => $value)

        $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('laptops') -> where('stockTypeId', $value -> id)
                                                                                            -> where('visibility', 1)
                                                                                            -> where('price', '>=', $productMinPrice)
                                                                                            -> where('price', '<=', $productMaxPrice)
                                                                                            -> count();

        foreach($data['configuration']['laptopSystems'] as $key => $value)

        $data['configuration']['laptopSystems'][$key] -> numOfProducts = \DB::table('laptops') -> where('laptopSystemId', $value -> id)
                                                                                               -> where('visibility', 1)
                                                                                               -> where('price', '>=', $productMinPrice)
                                                                                               -> where('price', '<=', $productMaxPrice)
                                                                                               -> count();

        foreach($data['laptops'] as $key => $value)
        {
          $data['laptops'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

        $data['pages'] = $paginator -> pages;
        $data['maxPage'] = $paginator -> maxPage;
        $data['currentPage'] = $paginator -> currentPage;
      }
    }

    BaseModel::collectStatisticalData(Laptop::class);

    return View::make('contents.shop.laptops.index', ['contentData' => $data,
                                                      'generalData' => $generalData]);
  }

  public function view($id)
  {
    $generalData = \App\Models\Shop\BaseModel::getGeneralData();

    $numOfProductsToView = 12;
    $pricePart = 0.2;

    $columns = ['laptops.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];
    $fields = ['laptops.id', 'title', 'mainImage', 'discount', 'price', 'laptopSystemTitle', 'diagonal', 'memory'];

    $data['laptop'] = \DB::table('laptops') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
    $data['laptopExists'] = !is_null($data['laptop']);

    if ($data['laptopExists'])
    {
      $generalData['seoFields'] -> description = $data['laptop'] -> seoDescription;
      $generalData['seoFields'] -> keywords = $data['laptop'] -> seoKeywords;
      $generalData['seoFields'] -> title = $data['laptop'] -> title;

      $stockData = \DB::table('stock_types') -> select() -> where('id', '=', $data['laptop'] -> stockTypeId) -> get() -> first();

      $data['images'] = \DB::table('laptops_images') -> where('laptopId', '=', $data['laptop'] -> id) -> get();
      $data['imagesExist'] = !$data['images'] -> isEmpty();

      $data['stockTitle'] = $stockData -> stockTitle;
      $data['stockStatusColor'] = $stockData -> statusColor;
      $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

      $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['laptop'] -> conditionId) -> get() -> first() -> conditionTitle;
      $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['laptop'] -> warrantyId) -> get() -> first() -> durationUnit;

      $data['laptop'] -> newPrice = $data['laptop'] -> price - $data['laptop'] -> discount;
      $data['laptop'] -> categoryId = BaseModel::getTableAliasByModelName(Laptop::class);

      $percent = $data['laptop'] -> newPrice * $pricePart;
      $leftRange = (int) ($data['laptop'] -> newPrice - $percent);
      $rightRange = (int) ($data['laptop'] -> newPrice + $percent);

      $data['recommendedLaptops'] = \DB::table('laptops') -> select($fields)
                                                          -> join('laptop_systems', 'laptop_systems.id', '=', 'laptops.laptopSystemId')
                                                          -> where('visibility', 1)
                                                          -> where('price', '<=', $rightRange)
                                                          -> where('price', '>=', $leftRange)
                                                          -> where('laptops.id', '!=', $data['laptop'] -> id)
                                                          -> take($numOfProductsToView)
                                                          -> get();

      $data['recommendedLaptopsExist'] = !$data['recommendedLaptops'] -> isEmpty();

      if ($data['recommendedLaptopsExist'])
      {
        foreach($data['recommendedLaptops'] as $key => $value)

        $data['recommendedLaptops'][$key] -> newPrice = $value -> price - $value -> discount;
      }

      BaseModel::collectStatisticalData(Laptop::class);

      return View::make('contents.shop.laptops.view', ['contentData' => $data,
                                                       'generalData' => $generalData]);
    }

    else abort(404);
  }
}
