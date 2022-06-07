<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;
use App\Traits\Searchable;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Product;

class ProductController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      // initialize options

      $numOfProductsToView = 9;
      $data['productsExist'] = false;

      $supportedOrders = [0, 1, 2, 3, 4];
      $searchTextMaximumLength = 100;
      $categoryIdMaxLength = 15;

      // validate user input

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'category-id' => 'required|string|min:4',
                                                  'query' => 'required|string|min:1|max:100']);

      $productPriceRanges = \DB::table('price_configurations') -> first();

      if (!$validator -> fails() && !is_null($productPriceRanges))
      {
        $priceValues = [];

        foreach($productPriceRanges as $key => $value) $priceValues[] = $value;

        $data['productMinPrice'] = min($priceValues);
        $data['productMaxPrice'] = max($priceValues);

        $tablesData = \DB::table('tables') -> get();
        $columns = ['pathPart', 'price', 'discount', 'id', 'title', 'mainImage', 'timestamp'];
        $indexedColumns = ['title', 'description'];

        $searchQuery = $parameters['query'];
        $categoryId = $parameters['category-id'];

        $numberOfTables = $tablesData -> count();

        if ($numberOfTables != 0)
        {
          $maximumLengthOfCategoryIdParam = ($categoryIdMaxLength * $numberOfTables) + $numberOfTables - 1;

          $categoryId = substr($parameters['category-id'], 0, $maximumLengthOfCategoryIdParam);
          $categoryIdParts = explode(':', $categoryId);
          $tablesIdentifiers = [];

          $tablesData -> each(function($table) use (&$tablesIdentifiers){

              $tablesIdentifiers[] = $table -> alias;
          });

          if (array_intersect($categoryIdParts, $tablesIdentifiers) == $categoryIdParts)
          {
            $priceFrom = abs((int) $parameters['price-from']);
            $priceTo = abs((int) $parameters['price-to']);
            $currentPage = abs((int) $parameters['active-page']);

            $minPriceIsInAllowedRange = $priceFrom >= $data['productMinPrice'] && $priceFrom <= $data['productMaxPrice'];
            $maxPriceIsInAllowedRange = $priceTo <= $data['productMaxPrice'] && $priceTo >= $data['productMinPrice'];

            if ($minPriceIsInAllowedRange && $maxPriceIsInAllowedRange && $currentPage)
            {
              $productsOrder = abs((int) $parameters['order']);
              $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);

              if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
              {
                $numOfProductsToView = $numOfProductsToView;

                $conditions = \DB::table('conditions') -> get();
                $conditionExists = $conditions -> count() != 0;

                $stockTypes = \DB::table('stock_types') -> get();
                $stockTypeExists = $stockTypes -> count() != 0;

                if ($conditionExists && $stockTypeExists)
                {
                  $conditionsParts = explode(':', $parameters['condition']);
                  $stockTypesParts = explode(':', $parameters['stock-type']);

                  $primaryQueryBuilder = null;
                  $totalNumOfProducts = 0;
                  $stockTypeIdentifiers = $conditionIdentifiers = [];

                  foreach($stockTypes as $value) $stockTypeIdentifiers[] = $value -> id;

                  foreach($conditions as $value) $conditionIdentifiers[] = $value -> id;

                  foreach($categoryIdParts as $categoryIdentifier)
                  {
                    $categoryTableData = $tablesData -> where('alias', $categoryIdentifier) -> first();
                    $tableName = $categoryTableData -> name;

                    $tempQueryBuilder = \DB::table($tableName);
                    $tempQueryBuilder = Searchable::booleanSearch($tempQueryBuilder, $columns, $searchQuery, $indexedColumns)-> where('visibility', 1);

                    if (array_intersect($conditionsParts, $conditionIdentifiers) == $conditionsParts) $tempQueryBuilder = $tempQueryBuilder -> whereIn('conditionId', $conditionsParts);

                    if (array_intersect($stockTypesParts, $stockTypeIdentifiers) == $stockTypesParts) $tempQueryBuilder = $tempQueryBuilder -> whereIn('stockTypeId', $stockTypesParts);

                    if (is_null($primaryQueryBuilder)) $primaryQueryBuilder = $tempQueryBuilder;

                    else $primaryQueryBuilder -> union($tempQueryBuilder);

                    $totalNumOfProducts += $tempQueryBuilder -> count();
                  }

                  if (in_array($productsOrder, $supportedOrders))
                  {
                    $orderNumber = !($productsOrder % 2);
                    $orderColumn = 'relevance';

                    if ($productsOrder == 1 || $productsOrder == 2) $orderColumn = 'price';

                    else if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'timestamp';

                    $order = $orderColumn == 'relevance' ? 'desc' : ($orderNumber == 0 ? 'desc' : 'asc');

                    $primaryQueryBuilder = $primaryQueryBuilder -> orderBy($orderColumn, $order);
                  }

                  if ($totalNumOfProducts != 0)
                  {
                    $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $currentPage, 2, 0);

                    $data['pages'] = $paginator -> pages;
                    $data['maxPage'] = $paginator -> maxPage;
                    $data['currentPage'] = $currentPage;

                    $data['products'] = $primaryQueryBuilder -> skip(($currentPage - 1) * $numOfProductsToView) -> take($numOfProductsToView) -> get();
                    $data['productsExist'] = true;

                    $data['products'] -> map(function($product){

                        $product -> newPrice = $product -> price - $product -> discount;
                    });
                  }
                }
              }
            }
          }
        }
      }

      return View::make('contents.shop.products.getSearchResults', ['data' => $data]);
    }

    public function getLiveSearchResults(Request $request)
    {
      $resultsToView = 20;
      $data['products'] = null;
      $data['productsExist'] = false;

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['query' => 'required|string|min:1|max:100',
                                                  'category-id' => 'required|string|min:4|max:10']);

      if (!$validator -> fails())
      {
        $searchQuery = $parameters['query'];
        $categoryId = $parameters['category-id'];

        $tablesData = \DB::table('tables') -> get();
        $columns = ['pathPart', 'price', 'discount', 'id', 'title', 'mainImage'];
        $indexedColumns = ['title', 'description'];

        if (!$tablesData -> isEmpty())
        {
          $tableDataByCategory = \DB::table('tables') -> where('alias', $categoryId) -> first();
          $categoryExists = !is_null($tableDataByCategory);

          if ($categoryExists)
          {
             $tableName = $tableDataByCategory -> name;

             $queryBuilder = \DB::table($tableName);
             $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $searchQuery, $indexedColumns);
             $queryBuilder = $queryBuilder -> where('visibility', 1) -> orderBy('relevance', 'desc') -> take($resultsToView);

             if ($queryBuilder -> count() != 0)
             {
                $data['productsExist'] = true;
                $data['products'] = $queryBuilder -> get();

                $data['products'] -> map(function($product){

                    $product -> price = $product -> price - $product -> discount;
                });
             }
          }

          else
          {
            $primaryQueryBuilder = null;

            foreach($tablesData as $table)
            {
              $tableName = $table -> name;

              $tempQueryBuilder = \DB::table($tableName);
              $tempQueryBuilder = Searchable::booleanSearch($tempQueryBuilder, $columns, $searchQuery, $indexedColumns) -> where('visibility', 1);

              if ($primaryQueryBuilder === null) $primaryQueryBuilder = $tempQueryBuilder;

              else $primaryQueryBuilder -> union($tempQueryBuilder);
            }

            if (!is_null($primaryQueryBuilder) && $primaryQueryBuilder -> count())
            {
              $data['productsExist'] = true;
              $data['products'] = $primaryQueryBuilder -> orderBy('relevance', 'desc') -> take($resultsToView) -> get();

              $data['products'] -> map(function($product){

                  $product -> price = $product -> price - $product -> discount;
              });
            }
          }
        }
      }

      return View::make('contents.shop.products.liveSearchResults', ['data' => $data]);
    }

    public function search(Request $request)
    {
      $generalData = BaseModel::getGeneralData();

      $data['productsExist'] = false;
      $data['categoryIdentifiers'] = null;
      $data['paramsAreValid'] = false;

      $data['products'] = [];
      $data['categories'] = [];

      $data['active'] = 1;
      $data['category-id'] = 'f1u3ja5i7';

      $numOfProductsToView = 9;
      $searchTextMaximumLength = 100;
      $categoryIdMaximumLength = 15;

      $productPriceRanges = \DB::table('price_configurations') -> select() -> first();
      $priceValues = [];

      foreach($productPriceRanges as $key => $value)
      {
        if ($key !== 'id')
        {
          $priceValues[] = $value;
        }
      }

      // get maximum and minimum prices

      $data['productMinPrice'] = min($priceValues);
      $data['productMaxPrice'] = max($priceValues);

      // get stock types and conditions

      $data['stockTypes'] = \DB::table('stock_types') -> get();
      $data['conditions'] = \DB::table('conditions') -> get();

      // set counters to zero

      foreach($data['stockTypes'] as $key => $value) $data['stockTypes'][$key] -> quantity = 0;
      foreach($data['conditions'] as $key => $value) $data['conditions'][$key] -> quantity = 0;

      // request data validation

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['query' => 'required|string|min:1|max:100',
                                                  'categoryId' => 'required|string|min:4:max:15']);

      if (!$validator -> fails())
      {
        $data['paramsAreValid'] = true;

        $parameters['query'] = trim($parameters['query']);
        $parameters['categoryId'] = trim($parameters['categoryId']);

        $tablesData = \DB::table('tables') -> get();
        $columns = ['pathPart', 'price', 'discount', 'id', 'title', 'mainImage'];
        $indexedColumns = ['title', 'description'];

        if (!$tablesData -> isEmpty())
        {
          $searchQuery = $parameters['query'];
          $categoryId = substr($parameters['categoryId'], 0, $categoryIdMaximumLength);

          $tableDataByCategory = \DB::table('tables') -> where('alias', $categoryId) -> first();
          $categoryExists = !is_null($tableDataByCategory);

          $data['search-query'] = htmlentities($searchQuery, ENT_QUOTES, 'UTF-8');

          if ($categoryExists)
          {
            $tableName = $tableDataByCategory -> name;

            $queryBuilder = \DB::table($tableName);
            $queryBuilder = Searchable::booleanSearch($queryBuilder, $columns, $searchQuery, $indexedColumns);
            $queryBuilder = $queryBuilder -> where('visibility', 1) -> orderBy('relevance', 'desc') -> take($numOfProductsToView);

            $data['products'] = $queryBuilder -> get();

            $totalNumOfProducts = $queryBuilder -> count();
            $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $data['active'], 2, 0);

            $data['pages'] = $paginator -> pages;
            $data['maxPage'] = $paginator -> maxPage;
            $data['currentPage'] = $data['active'];
            $data['productsExist'] = $totalNumOfProducts != 0;

            if ($data['productsExist'])
            {
              $data['categories'][] = ['categoryTitle' => $tableDataByCategory -> title,
                                       'categoryId' => $categoryId,
                                       'quantity' => $totalNumOfProducts];

              $data['categoryIdentifiers'] = $categoryId;

              foreach($data['products'] as $key => $value)
              {
                $data['products'][$key] -> newPrice = $value -> price - $value -> discount;
              }

              foreach($data['stockTypes'] as $key => $value)
              {
                $stockTypesCounterQueryBuilder = \DB::table($tableName);
                $stockTypesCounterQueryBuilder = Searchable::booleanSearch($stockTypesCounterQueryBuilder, ['id'], $searchQuery, $indexedColumns);

                $data['stockTypes'][$key] -> quantity += $stockTypesCounterQueryBuilder -> where('visibility', 1) -> where('stockTypeId', $value -> id) -> count();
              }

              foreach($data['conditions'] as $key => $value)
              {
                $conditionsCounterQueryBuilder = \DB::table($tableName);
                $conditionsCounterQueryBuilder = Searchable::booleanSearch($conditionsCounterQueryBuilder, ['id'], $searchQuery, $indexedColumns);

                $data['conditions'][$key] -> quantity += $conditionsCounterQueryBuilder -> where('visibility', 1) -> where('conditionId', $value -> id) -> count();
              }
            }
          }

          else
          {
            $primaryQueryBuilder = null;
            $totalNumOfProducts = 0;

            foreach($tablesData as $table)
            {
              $tableName = $table -> name;
              $categoryId = $table -> alias;

              $tempQueryBuilder = \DB::table($tableName);
              $tempQueryBuilder = Searchable::booleanSearch($tempQueryBuilder, $columns, $searchQuery, $indexedColumns) -> where('visibility', 1);

              if ($primaryQueryBuilder === null) $primaryQueryBuilder = $tempQueryBuilder;

              else $primaryQueryBuilder -> union($tempQueryBuilder);

              foreach($data['stockTypes'] as $key => $value)
              {
                $stockTypesCounterQueryBuilder = \DB::table($tableName);
                $stockTypesCounterQueryBuilder = Searchable::booleanSearch($stockTypesCounterQueryBuilder, ['id'], $searchQuery, $indexedColumns);

                $data['stockTypes'][$key] -> quantity += $stockTypesCounterQueryBuilder -> where('visibility', 1) -> where('stockTypeId', $value -> id) -> count();
              }

              foreach($data['conditions'] as $key => $value)
              {
                $conditionsCounterQueryBuilder = \DB::table($tableName);
                $conditionsCounterQueryBuilder = Searchable::booleanSearch($conditionsCounterQueryBuilder, ['id'], $searchQuery, $indexedColumns);

                $data['conditions'][$key] -> quantity += $conditionsCounterQueryBuilder -> where('visibility', 1) -> where('conditionId', $value -> id) -> count();
              }

              $numberOfRows = $tempQueryBuilder -> count();

              if ($numberOfRows != 0)
              {
                $data['categories'][] = ['categoryTitle' => $table -> title, 'categoryId' => $categoryId, 'quantity' => $numberOfRows];

                $totalNumOfProducts += $numberOfRows;

                $data['categoryIdentifiers'] .= "{$categoryId}:";
              }
            }

            if ($primaryQueryBuilder !== null)
            {
              $primaryQueryBuilder = $primaryQueryBuilder -> orderBy('relevance', 'desc') -> take($numOfProductsToView);

              $data['categoryIdentifiers'] = rtrim($data['categoryIdentifiers'], ':');
              $data['numOfProducts'] = $totalNumOfProducts;
              $data['itemsPerPage'] = $numOfProductsToView;
              $data['productsExist'] = $data['numOfProducts'] != 0;

              $paginator = \Paginator::build($data['numOfProducts'], 3, $data['itemsPerPage'], $data['active'], 2, 0);

              $data['pages'] = $paginator -> pages;
              $data['maxPage'] = $paginator -> maxPage;
              $data['currentPage'] = $data['active'];
              $data['products'] = $primaryQueryBuilder -> get();

              if ($data['productsExist'])
              {
                $data['products'] -> map(function($product){

                    $product -> newPrice = $product -> price - $product -> discount;
                });
              }
            }
          }
        }
      }

      return View::make('contents.shop.products.search', ['contentData' => $data,
                                                          'generalData' => $generalData]);
    }
}
