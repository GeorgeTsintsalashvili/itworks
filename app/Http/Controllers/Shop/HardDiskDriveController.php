<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\HardDiskDrive;

class HardDiskDriveController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 15;
      $supportedOrders = [1, 2, 3, 4, 5, 6, 7, 8];
      $priceRange = BaseModel::getPriceRange(HardDiskDrive::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'rpm' => 'required|string',
                                                  'capacity' => 'required|string',
                                                  'form-factor-id' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> hardDiskDriveMinPrice && $priceFrom <= $priceRange -> hardDiskDriveMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> hardDiskDriveMinPrice && $priceTo <= $priceRange -> hardDiskDriveMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $rpms = \DB::table('hard_disk_drives') -> selectRaw('DISTINCT(`rpm`) AS `rpm`') -> where('visibility', 1) -> get();
            $rpmsExist = $rpms -> count() != 0;

            $capacities = \DB::table('hard_disk_drives') -> selectRaw('DISTINCT(`capacity`) AS `capacity`') -> where('visibility', 1) -> get();
            $capacitiesExist = $capacities -> count() != 0;

            $formFactors = \DB::table('hard_disk_drives_form_factors') -> get();
            $formFactorsExist = $formFactors -> count() != 0;

            if ($formFactorsExist && $capacitiesExist && $rpmsExist && $stockTypeExists && $conditionExists)
            {
              $rpmParts = array_map('intval', explode(':', $parameters['rpm']));
              $capacityParts = array_map('intval', explode(':', $parameters['capacity']));
              $formFactorsParts = array_map('intval', explode(':', $parameters['form-factor-id']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['hard_disk_drives.id', 'title', 'mainImage', 'discount', 'price', 'rpm', 'capacity', 'formFactorTitle', 'stockTypeId', 'enableAddToCartButton'];
              $rpmNumbers = $capacityNumbers = $formFactorsNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('hard_disk_drives') -> select($columns)
                                                      -> join('hard_disk_drives_form_factors', 'hard_disk_drives_form_factors.id', '=', 'hard_disk_drives.formFactorId')
                                                      -> join('stock_types', 'stock_types.id', '=', 'hard_disk_drives.stockTypeId')
                                                      -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($rpms as $value) $rpmNumbers[] = $value -> rpm;
              foreach($capacities as $value) $capacityNumbers[] = $value -> capacity;
              foreach($formFactors as $value) $formFactorsNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($rpmParts, $rpmNumbers) == $rpmParts) $query = $query -> whereIn('rpm', $rpmParts);
              if (array_intersect($capacityParts, $capacityNumbers) == $capacityParts) $query = $query -> whereIn('capacity', $capacityParts);
              if (array_intersect($formFactorsParts, $formFactorsNumbers) == $formFactorsParts) $query = $query -> whereIn('formFactorId', $formFactorsParts);

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);

                if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'capacity';

                else if ($productsOrder == 5 || $productsOrder == 6) $orderColumn = 'rpm';

                else if ($productsOrder == 7 || $productsOrder == 8) $orderColumn = 'timestamp';

                else $orderColumn = 'price';

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

      return View::make('contents.shop.hardDiskDrives.getHardDiskDrives', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(HardDiskDrive::class);
      $data['configuration']['numOfProductsToShow'] = $numOfProductsToView;

      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['hardDiskDrivesExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> hardDiskDriveMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> hardDiskDriveMaxPrice;

        $totalNumOfProducts = HardDiskDrive::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['hardDiskDrives'] = \DB::table('hard_disk_drives') -> select(['hard_disk_drives.id', 'title', 'mainImage', 'discount', 'price', 'rpm', 'formFactorId', 'capacity', 'formFactorTitle', 'stockTypeId', 'enableAddToCartButton'])
                                                                 -> join('hard_disk_drives_form_factors', 'hard_disk_drives_form_factors.id', '=', 'hard_disk_drives.formFactorId')
                                                                 -> join('stock_types', 'stock_types.id', '=', 'hard_disk_drives.stockTypeId')
                                                                 -> where('visibility', 1)
                                                                 -> where('price', '>=', $productMinPrice)
                                                                 -> where('price', '<=', $productMaxPrice)
                                                                 -> skip(($page - 1) * $numOfProductsToView)
                                                                 -> take($numOfProductsToView)
                                                                 -> get();

        $data['hardDiskDrivesExist'] = !$data['hardDiskDrives'] -> isEmpty();

        if ($data['hardDiskDrivesExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(HardDiskDrive::class);

          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          $data['configuration']['rpm'] = \DB::table('hard_disk_drives') -> select(\DB::raw('DISTINCT(`rpm`) AS `rpm`'))
                                                                         -> where('visibility', 1)
                                                                         -> where('price', '>=', $productMinPrice)
                                                                         -> where('price', '<=', $productMaxPrice)
                                                                         -> orderBy('rpm', 'asc')
                                                                         -> get();

          $data['configuration']['capacity'] = \DB::table('hard_disk_drives') -> select(\DB::raw('DISTINCT(`capacity`) AS `capacity`'))
                                                                              -> where('visibility', 1)
                                                                              -> where('price', '>=', $productMinPrice)
                                                                              -> where('price', '<=', $productMaxPrice)
                                                                              -> orderBy('capacity', 'asc')
                                                                              -> get();

          $data['configuration']['formFactors'] = \DB::table('hard_disk_drives_form_factors') -> get();

          foreach($data['configuration']['formFactors'] as $key => $formFactor)
          {
            $data['configuration']['formFactors'][$key] -> numOfProducts = \DB::table('hard_disk_drives') -> where('formFactorId', '=', $formFactor -> id)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['configuration']['rpm'] as $key => $rpm)
          {
            $data['configuration']['rpm'][$key] -> numOfProducts = \DB::table('hard_disk_drives') -> where('rpm', '=', $rpm -> rpm)
                                                                                                  -> where('visibility', 1)
                                                                                                  -> where('price', '>=', $productMinPrice)
                                                                                                  -> where('price', '<=', $productMaxPrice)
                                                                                                  -> count();
          }

          foreach($data['configuration']['capacity'] as $key => $capacity)
          {
            $data['configuration']['capacity'][$key] -> numOfProducts = \DB::table('hard_disk_drives') -> where('capacity', $capacity -> capacity)
                                                                                                       -> where('visibility', 1)
                                                                                                       -> where('price', '>=', $productMinPrice)
                                                                                                       -> where('price', '<=', $productMaxPrice)
                                                                                                       -> count();
          }

          foreach($data['configuration']['conditions'] as $key => $value)
          {
            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('hard_disk_drives') -> where('conditionId', $value -> id)
                                                                                                         -> where('visibility', 1)
                                                                                                         -> where('price', '>=', $productMinPrice)
                                                                                                         -> where('price', '<=', $productMaxPrice)
                                                                                                         -> count();
          }

          foreach($data['configuration']['stockTypes'] as $key => $value)
          {
            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('hard_disk_drives') -> where('stockTypeId', $value -> id)
                                                                                                         -> where('visibility', 1)
                                                                                                         -> where('price', '>=', $productMinPrice)
                                                                                                         -> where('price', '<=', $productMaxPrice)
                                                                                                         -> count();
          }

          foreach($data['hardDiskDrives'] as $key => $value)
          {
            $data['hardDiskDrives'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(HardDiskDrive::class);

      return View::make('contents.shop.hardDiskDrives.index', ['contentData' => $data,
                                                               'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $columns = ['hard_disk_drives.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['hardDiskDrive'] = \DB::table('hard_disk_drives') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['hardDiskDriveExists'] = !is_null($data['hardDiskDrive']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['hardDiskDriveExists'])
      {
        $generalData['seoFields'] -> description = $data['hardDiskDrive'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['hardDiskDrive'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['hardDiskDrive'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['hardDiskDrive'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('hard_disk_drives_images') -> where('hardDiskDriveId', '=', $data['hardDiskDrive'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['hardDiskDrive'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['hardDiskDrive'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['hardDiskDrive'] -> newPrice = $data['hardDiskDrive'] -> price - $data['hardDiskDrive'] -> discount;
        $data['hardDiskDrive'] -> categoryId = BaseModel::getTableAliasByModelName(HardDiskDrive::class);

        $percent = $data['hardDiskDrive'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['hardDiskDrive'] -> newPrice - $percent);
        $rightRange = (int) ($data['hardDiskDrive'] -> newPrice + $percent);

        $fields = ['hard_disk_drives.id', 'title', 'mainImage', 'discount', 'price', 'rpm', 'capacity', 'formFactorTitle'];

        $data['recommendedHardDiskDrives'] = \DB::table('hard_disk_drives') -> select($fields)
                                                                            -> join('hard_disk_drives_form_factors', 'hard_disk_drives_form_factors.id', '=', 'hard_disk_drives.formFactorId')
                                                                            -> where('visibility', 1)
                                                                            -> where('price', '<=', $rightRange)
                                                                            -> where('price', '>=', $leftRange)
                                                                            -> where('hard_disk_drives.id', '!=', $data['hardDiskDrive'] -> id)
                                                                            -> take($numOfProductsToView)
                                                                            -> get();

        $data['recommendedHardDiskDrivesExist'] = !$data['recommendedHardDiskDrives'] -> isEmpty();

        if ($data['recommendedHardDiskDrivesExist'])
        {
          foreach($data['recommendedHardDiskDrives'] as $key => $value)

          $data['recommendedHardDiskDrives'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(HardDiskDrive::class);

        return View::make('contents.shop.hardDiskDrives.view', ['contentData' => $data,
                                                                'generalData' => $generalData]);
      }

      else return abort(404);
    }
}
