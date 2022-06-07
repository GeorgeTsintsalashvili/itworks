<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\OpticalDiscDrive;

class OpticalDiscDriveController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 6;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(OpticalDiscDrive::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'destination' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> opticalDiscDriveMinPrice && $priceFrom <= $priceRange -> opticalDiscDriveMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> opticalDiscDriveMinPrice && $priceTo <= $priceRange -> opticalDiscDriveMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $opticalDiscDriveTypes = \DB::table('optical_disc_drives_types') -> get();
            $opticalDiscDriveTypesExist = $opticalDiscDriveTypes -> count() != 0;

            if ($opticalDiscDriveTypesExist && $stockTypeExists && $conditionExists)
            {
              $opticalDiscDriveTypesParts = array_map('intval', explode(':', $parameters['destination']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['optical_disc_drives.id', 'title', 'mainImage', 'discount', 'price', 'enableAddToCartButton'];
              $opticalDiscDriveTypesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('optical_disc_drives') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'optical_disc_drives.stockTypeId') -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($opticalDiscDriveTypes as $value) $opticalDiscDriveTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($opticalDiscDriveTypesParts, $opticalDiscDriveTypesNumbers) == $opticalDiscDriveTypesParts) $query = $query -> whereIn('oddTypeId', $opticalDiscDriveTypesParts);

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

      return View::make('contents.shop.opticalDiscDrives.getOpticalDiscDrives', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 6;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(OpticalDiscDrive::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['opticalDiscDrivesExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> opticalDiscDriveMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> opticalDiscDriveMaxPrice;

        $totalNumOfProducts = OpticalDiscDrive::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['opticalDiscDrives'] = \DB::table('optical_disc_drives') -> select(['optical_disc_drives.id', 'title', 'mainImage', 'discount', 'price', 'typeTitle', 'enableAddToCartButton'])
                                                                       -> join('optical_disc_drives_types', 'optical_disc_drives_types.id', '=', 'optical_disc_drives.oddTypeId')
                                                                       -> join('stock_types', 'stock_types.id', '=', 'optical_disc_drives.stockTypeId')
                                                                       -> where('visibility', 1)
                                                                       -> where('price', '>=', $productMinPrice)
                                                                       -> where('price', '<=', $productMaxPrice)
                                                                       -> skip(($page - 1) * $numOfProductsToView)
                                                                       -> take($numOfProductsToView)
                                                                       -> get();

        $data['opticalDiscDrivesExist'] = !$data['opticalDiscDrives'] -> isEmpty();

        if ($data['opticalDiscDrivesExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(OpticalDiscDrive::class);

          $data['configuration']['opticalDiscDrivesTypes'] = \DB::table('optical_disc_drives_types') -> get();
          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          foreach($data['configuration']['opticalDiscDrivesTypes'] as $key => $value)
          {
            $data['configuration']['opticalDiscDrivesTypes'][$key] -> numOfProducts = \DB::table('optical_disc_drives') -> where('oddTypeId', $value -> id)
                                                                                                                        -> where('visibility', 1)
                                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                                        -> count();
          }

          foreach($data['configuration']['conditions'] as $key => $value)
          {
            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('optical_disc_drives') -> where('conditionId', $value -> id)
                                                                                                            -> where('visibility', 1)
                                                                                                            -> where('price', '>=', $productMinPrice)
                                                                                                            -> where('price', '<=', $productMaxPrice)
                                                                                                            -> count();
          }

          foreach($data['configuration']['stockTypes'] as $key => $value)
          {
            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('optical_disc_drives') -> where('stockTypeId', $value -> id)
                                                                                                            -> where('visibility', 1)
                                                                                                            -> where('price', '>=', $productMinPrice)
                                                                                                            -> where('price', '<=', $productMaxPrice)
                                                                                                            -> count();
          }

          foreach($data['opticalDiscDrives'] as $key => $value)
          {
            $data['opticalDiscDrives'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(OpticalDiscDrive::class);

      return View::make('contents.shop.opticalDiscDrives.index', ['contentData' => $data,
                                                                  'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();

      $numOfProductsToView = 12;
      $productPricePart = 0.2;
      $columns = ['optical_disc_drives.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['opticalDiscDrive'] = \DB::table('optical_disc_drives') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['opticalDiscDriveExists'] = !is_null($data['opticalDiscDrive']);

      if ($data['opticalDiscDriveExists'])
      {
        $generalData['seoFields'] -> description = $data['opticalDiscDrive'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['opticalDiscDrive'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['opticalDiscDrive'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['opticalDiscDrive'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('optical_disc_drives_images') -> where('opticalDiscDriveId', '=', $data['opticalDiscDrive'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['opticalDiscDrive'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['opticalDiscDrive'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['opticalDiscDrive'] -> newPrice = $data['opticalDiscDrive'] -> price - $data['opticalDiscDrive'] -> discount;
        $data['opticalDiscDrive'] -> categoryId = BaseModel::getTableAliasByModelName(OpticalDiscDrive::class);

        $percent = $data['opticalDiscDrive'] -> newPrice * $productPricePart;
        $leftRange = (int) ($data['opticalDiscDrive'] -> newPrice - $percent);
        $rightRange = (int) ($data['opticalDiscDrive'] -> newPrice + $percent);

        $fields = ['optical_disc_drives.id', 'title', 'mainImage', 'discount', 'price'];

        $data['recommendedOpticalDiscDrives'] = \DB::table('optical_disc_drives') -> select($fields)
                                                                                  -> where('visibility', 1)
                                                                                  -> where('price', '<=', $rightRange)
                                                                                  -> where('price', '>=', $leftRange)
                                                                                  -> where('optical_disc_drives.id', '!=', $data['opticalDiscDrive'] -> id)
                                                                                  -> take($numOfProductsToView)
                                                                                  -> get();

        $data['recommendedOpticalDiscDrivesExist'] = !$data['recommendedOpticalDiscDrives'] -> isEmpty();

        if ($data['recommendedOpticalDiscDrivesExist'])
        {
          foreach($data['recommendedOpticalDiscDrives'] as $key => $value)

          $data['recommendedOpticalDiscDrives'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(OpticalDiscDrive::class);

        return View::make('contents.shop.opticalDiscDrives.view', ['contentData' => $data,
                                                                   'generalData' => $generalData]);
      }

      else abort(404);
    }
}
