<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\NotebookCharger;

class NotebookChargerController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 15;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(NotebookCharger::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'manufacturer' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> notebookChargerMinPrice && $priceFrom <= $priceRange -> notebookChargerMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> notebookChargerMinPrice && $priceTo <= $priceRange -> notebookChargerMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $manufacturers = \DB::table('notebooks_manufacturers') -> get();
            $manufacturersExist = $manufacturers -> count() != 0;

            if ($manufacturersExist && $stockTypeExists && $conditionExists)
            {
              $manufacturersParts = array_map('intval', explode(':', $parameters['manufacturer']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['notebook_chargers.id', 'title', 'mainImage', 'discount', 'price', 'enableAddToCartButton'];
              $manufacturersNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('notebook_chargers') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'notebook_chargers.stockTypeId') -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($manufacturers as $value) $manufacturersNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($manufacturersParts, $manufacturersNumbers) == $manufacturersParts) $query = $query -> whereIn('notebookManufacturerId', $manufacturersParts);

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

      return View::make('contents.shop.notebookChargers.getNotebookChargers', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['notebookChargersExist'] = false;
      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(NotebookCharger::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> notebookChargerMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> notebookChargerMaxPrice;

        $totalNumOfProducts = NotebookCharger::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['notebookChargers'] = \DB::table('notebook_chargers') -> select(['notebook_chargers.id', 'title', 'mainImage', 'price', 'discount', 'enableAddToCartButton'])
                                                                    -> join('stock_types', 'stock_types.id', '=', 'notebook_chargers.stockTypeId')
                                                                    -> where('visibility', 1)
                                                                    -> where('price', '>=', $productMinPrice)
                                                                    -> where('price', '<=', $productMaxPrice)
                                                                    -> skip(($page - 1) * $numOfProductsToView)
                                                                    -> take($numOfProductsToView)
                                                                    -> get();

        $data['notebookChargersExist'] = !$data['notebookChargers'] -> isEmpty();

        if ($data['notebookChargersExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(NotebookCharger::class);

          $data['configuration']['manufacturers'] = \DB::table('notebooks_manufacturers') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();

          foreach($data['configuration']['manufacturers'] as $key => $value)

          $data['configuration']['manufacturers'][$key] -> numOfProducts = \DB::table('notebook_chargers') -> where('notebookManufacturerId', $value -> id)
                                                                                                           -> where('visibility', 1)
                                                                                                           -> where('price', '>=', $productMinPrice)
                                                                                                           -> where('price', '<=', $productMaxPrice)
                                                                                                           -> count();
          foreach($data['configuration']['conditions'] as $key => $value)

          $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('notebook_chargers') -> where('conditionId', $value -> id)
                                                                                                        -> where('visibility', 1)
                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                        -> count();

          foreach($data['configuration']['stockTypes'] as $key => $value)

          $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('notebook_chargers') -> where('stockTypeId', $value -> id)
                                                                                                        -> where('visibility', 1)
                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                        -> count();

          foreach($data['notebookChargers'] as $key => $value)
          {
            $data['notebookChargers'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(NotebookCharger::class);

      return View::make('contents.shop.notebookChargers.index', ['contentData' => $data,
                                                                 'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $columns = ['notebook_chargers.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'seoKeywords', 'seoDescription', 'quantity'];

      $data['notebookCharger'] = \DB::table('notebook_chargers') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['notebookChargerExists'] = !is_null($data['notebookCharger']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['notebookChargerExists'])
      {
        $generalData['seoFields'] -> description = $data['notebookCharger'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['notebookCharger'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['notebookCharger'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['notebookCharger'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('notebook_chargers_images') -> where('notebookChargerId', '=', $data['notebookCharger'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['notebookCharger'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['notebookCharger'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['notebookCharger'] -> newPrice = $data['notebookCharger'] -> price - $data['notebookCharger'] -> discount;
        $data['notebookCharger'] -> categoryId = BaseModel::getTableAliasByModelName(NotebookCharger::class);

        $percent = $data['notebookCharger'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['notebookCharger'] -> newPrice - $percent);
        $rightRange = (int) ($data['notebookCharger'] -> newPrice + $percent);
        $fields = ['notebook_chargers.id', 'title', 'mainImage', 'discount', 'price'];

        $data['recommendedNotebookChargers'] = \DB::table('notebook_chargers') -> select($fields)
                                                                               -> where('visibility', 1)
                                                                               -> where('price', '<=', $rightRange)
                                                                               -> where('price', '>=', $leftRange)
                                                                               -> where('notebook_chargers.id', '!=', $data['notebookCharger'] -> id)
                                                                               -> take($numOfProductsToView)
                                                                               -> get();

        $data['recommendedNotebookChargersExist'] = !$data['recommendedNotebookChargers'] -> isEmpty();

        if ($data['recommendedNotebookChargersExist'])

        foreach($data['recommendedNotebookChargers'] as $key => $value)
        {
          $data['recommendedNotebookChargers'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        \App\Models\Shop\BaseModel::collectStatisticalData(NotebookCharger::class);

        return View::make('contents.shop.notebookChargers.view', ['contentData' => $data,
                                                                  'generalData' => $generalData]);
      }

      else abort(404);
    }
}
