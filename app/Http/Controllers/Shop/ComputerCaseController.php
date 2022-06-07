<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\ComputerCase;

class ComputerCaseController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 9;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(ComputerCase::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'form-factor' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> computerCaseMinPrice && $priceFrom <= $priceRange -> computerCaseMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> computerCaseMinPrice && $priceTo <= $priceRange -> computerCaseMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $caseFormFactors = \DB::table('case_form_factors') -> get();
            $caseFormFactorsExist = $caseFormFactors -> count() != 0;

            if ($caseFormFactorsExist && $stockTypeExists && $conditionExists)
            {
              $caseFormFactorsParts = array_map('intval', explode(':', $parameters['form-factor']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = '`title`,`mainImage`,`discount`,`price`,`caseId`,`stockTypeId`,`enableAddToCartButton`,GROUP_CONCAT(`formFactorTitle` SEPARATOR ", ") AS `formFactorTitle`';
              $caseFormFactorsNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('cases_and_form_factors') -> selectRaw($columns)
                                                            -> join('case_form_factors', 'case_form_factors.id', '=', 'cases_and_form_factors.formFactorId')
                                                            -> join('computer_cases', 'computer_cases.id', '=', 'cases_and_form_factors.caseId')
                                                            -> join('stock_types', 'stock_types.id', '=', 'computer_cases.stockTypeId')
                                                            -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($caseFormFactors as $value) $caseFormFactorsNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($caseFormFactorsParts, $caseFormFactorsNumbers)) $query = $query -> whereIn('formFactorId', $caseFormFactorsParts);

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo) -> groupBy('caseId');

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = $productsOrder == 1 || $productsOrder == 2 ? 'price' : 'timestamp';

                $query = $query -> orderBy($orderColumn, $orderNumber == 0 ? 'desc' : 'asc');
              }

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

      return View::make('contents.shop.computerCases.getComputerCases', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 6;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(ComputerCase::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['casesExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> computerCaseMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> computerCaseMaxPrice;

        $totalNumOfProducts = ComputerCase::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['cases'] = \DB::table('cases_and_form_factors') -> select(\DB::raw('`caseId`,`title`,`mainImage`,`discount`,`price`,`stockTypeId`,`enableAddToCartButton`,GROUP_CONCAT(`formFactorTitle` SEPARATOR ", ") AS `formFactorTitle`'))
                                                              -> join('case_form_factors', 'case_form_factors.id', '=', 'cases_and_form_factors.formFactorId')
                                                              -> join('computer_cases', 'computer_cases.id', '=', 'cases_and_form_factors.caseId')
                                                              -> join('stock_types', 'stock_types.id', '=', 'computer_cases.stockTypeId')
                                                              -> where('visibility', 1)
                                                              -> where('price', '>=', $productMinPrice)
                                                              -> where('price', '<=', $productMaxPrice)
                                                              -> groupBy('caseId')
                                                              -> skip(($page - 1) * $numOfProductsToView)
                                                              -> take($numOfProductsToView)
                                                              -> get();

        $data['casesExist'] = !$data['cases'] -> isEmpty();

        if ($data['casesExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(ComputerCase::class);

          $data['configuration']['formFactors'] = \DB::table('case_form_factors') -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          foreach($data['configuration']['formFactors'] as $key => $value)

          $data['configuration']['formFactors'][$key] -> numOfProducts = \DB::table('cases_and_form_factors') -> join('computer_cases', 'computer_cases.id', '=', 'cases_and_form_factors.caseId')
                                                                                                              -> where('formFactorId', $value -> id)
                                                                                                              -> where('visibility', 1)
                                                                                                              -> where('price', '>=', $productMinPrice)
                                                                                                              -> where('price', '<=', $productMaxPrice)
                                                                                                              -> count();


          foreach($data['configuration']['conditions'] as $key => $value)

          $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('computer_cases') -> where('conditionId', $value -> id)
                                                                                                     -> where('visibility', 1)
                                                                                                     -> where('price', '>=', $productMinPrice)
                                                                                                     -> where('price', '<=', $productMaxPrice)
                                                                                                     -> count();


          foreach($data['configuration']['stockTypes'] as $key => $value)

          $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('computer_cases') -> where('stockTypeId', $value -> id)
                                                                                                     -> where('visibility', 1)
                                                                                                     -> where('price', '>=', $productMinPrice)
                                                                                                     -> where('price', '<=', $productMaxPrice)
                                                                                                     -> count();

          foreach($data['cases'] as $key => $value)
          {
            $data['cases'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(ComputerCase::class);

      return View::make('contents.shop.computerCases.index', ['contentData' => $data,
                                                              'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $columns = ['computer_cases.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['case'] = \DB::table('computer_cases') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['caseExists'] = !is_null($data['case']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['caseExists'])
      {
        $generalData['seoFields'] -> description = $data['case'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['case'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['case'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['case'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('computer_cases_images') -> where('computerCaseId', '=', $data['case'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['case'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['case'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['case'] -> newPrice = $data['case'] -> price - $data['case'] -> discount;
        $data['case'] -> categoryId = BaseModel::getTableAliasByModelName(ComputerCase::class);

        $percent = $data['case'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['case'] -> newPrice - $percent);
        $rightRange = (int) ($data['case'] -> newPrice + $percent);
        $fields = 'caseId,title,mainImage,discount,price,GROUP_CONCAT(`formFactorTitle` SEPARATOR ", ") AS `formFactorTitle`';

        $data['recommendedCases'] = \DB::table('cases_and_form_factors') -> select(\DB::raw($fields))
                                                                         -> join('case_form_factors', 'case_form_factors.id', '=', 'cases_and_form_factors.formFactorId')
                                                                         -> join('computer_cases', 'computer_cases.id', '=', 'cases_and_form_factors.caseId')
                                                                         -> where('visibility', 1)
                                                                         -> where('price', '<=', $rightRange)
                                                                         -> where('price', '>=', $leftRange)
                                                                         -> where('computer_cases.id', '!=', $data['case'] -> id)
                                                                         -> groupBy('caseId')
                                                                         -> take($numOfProductsToView)
                                                                         -> get();

        $data['recommendedCasesExist'] = !$data['recommendedCases'] -> isEmpty();

        if ($data['recommendedCasesExist'])

        foreach($data['recommendedCases'] as $key => $value)
        {
          $data['recommendedCases'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(ComputerCase::class);

        return View::make('contents.shop.computerCases.view', ['contentData' => $data,
                                                               'generalData' => $generalData]);
      }

      else abort(404);
    }
}
