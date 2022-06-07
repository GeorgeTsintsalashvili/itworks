<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\NetworkDevice;

class NetworkDeviceController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 6;
      $supportedOrders = [1, 2, 3, 4];
      $priceRange = BaseModel::getPriceRange(NetworkDevice::class);

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

          $priceFromIsInRange = $priceFrom >= $priceRange -> networkDeviceMinPrice && $priceFrom <= $priceRange -> networkDeviceMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> networkDeviceMinPrice && $priceTo <= $priceRange -> networkDeviceMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $networkDevicesTypes = \DB::table('network_devices_types') -> get();
            $networkDevicesTypesExist = $networkDevicesTypes -> count() != 0;

            if ($networkDevicesTypesExist && $stockTypeExists && $conditionExists)
            {
              $networkDevicesTypesParts = array_map('intval', explode(':', $parameters['type']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['network_devices.id', 'title', 'mainImage', 'discount', 'price', 'enableAddToCartButton'];
              $networkDevicesTypesNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('network_devices') -> select($columns) -> join('stock_types', 'stock_types.id', '=', 'network_devices.stockTypeId') -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($networkDevicesTypes as $value) $networkDevicesTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($networkDevicesTypesParts, $networkDevicesTypesNumbers) == $networkDevicesTypesParts) $query = $query -> whereIn('typeId', $networkDevicesTypesParts);

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

      return View::make('contents.shop.networkDevices.getNetworkDevices', ['data' => $data]);
    }

    public function index(int $categoryId = 0, int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 9;

      $data['categoryId'] = $categoryId;
      $data['networkDevicesExist'] = false;

      $data['configuration']['networkDevicesTypes'] = \DB::table('network_devices_types') -> get();
      $data['configuration']['networkDevicesTypesExist'] = !$data['configuration']['networkDevicesTypes'] -> isEmpty();

      if ($data['configuration']['networkDevicesTypesExist'])
      {
        $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(NetworkDevice::class);
        $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);

        if ($data['configuration']['productPriceRangeExists'])
        {
          $productMinPrice = $data['configuration']['productPriceRange'] -> networkDeviceMinPrice;
          $productMaxPrice = $data['configuration']['productPriceRange'] -> networkDeviceMaxPrice;

          $productsModel = NetworkDevice::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice);

          $productsQuery = \DB::table('network_devices') -> select(['network_devices.id', 'title', 'mainImage', 'price', 'discount', 'enableAddToCartButton'])
                                                         -> join('stock_types', 'stock_types.id', '=', 'network_devices.stockTypeId')
                                                         -> where('visibility', 1)
                                                         -> where('price', '>=', $productMinPrice)
                                                         -> where('price', '<=', $productMaxPrice);

          if ($categoryId != 0)
          {
            $productsModel = $productsModel -> where('typeId', '=', $categoryId);
            $productsQuery = $productsQuery -> where('typeId', '=', $categoryId);
          }

          $totalNumOfProducts = $productsModel -> count();

          $data['networkDevices'] = $productsQuery -> skip(($page - 1) * $numOfProductsToView) -> take($numOfProductsToView) -> get();

          $data['networkDevicesExist'] = !$data['networkDevices'] -> isEmpty();

          if ($data['networkDevicesExist'])
          {
            $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(NetworkDevice::class);

            $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();
            $data['configuration']['conditions'] = \DB::table('conditions') -> get();

            foreach($data['configuration']['networkDevicesTypes'] as $key => $value)

            $data['configuration']['networkDevicesTypes'][$key] -> numOfProducts = \DB::table('network_devices') -> where('typeId', $value -> id)
                                                                                                                 -> where('visibility', 1)
                                                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                                                 -> count();

            foreach($data['configuration']['conditions'] as $key => $value)

            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('network_devices') -> where('conditionId', $value -> id)
                                                                                                        -> where('visibility', 1)
                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                        -> count();

            foreach($data['configuration']['stockTypes'] as $key => $value)

            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('network_devices') -> where('stockTypeId', $value -> id)
                                                                                                        -> where('visibility', 1)
                                                                                                        -> where('price', '>=', $productMinPrice)
                                                                                                        -> where('price', '<=', $productMaxPrice)
                                                                                                        -> count();

            foreach($data['networkDevices'] as $key => $value)
            {
              $data['networkDevices'][$key] -> newPrice = $value -> price - $value -> discount;
            }

            $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

            $data['pages'] = $paginator -> pages;
            $data['maxPage'] = $paginator -> maxPage;
            $data['currentPage'] = $paginator -> currentPage;
          }
        }
      }

      BaseModel::collectStatisticalData(NetworkDevice::class);

      return View::make('contents.shop.networkDevices.index', ['contentData' => $data,
                                                               'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $columns = ['network_devices.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'seoKeywords', 'seoDescription', 'quantity'];

      $data['networkDevice'] = \DB::table('network_devices') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['networkDeviceExists'] = !is_null($data['networkDevice']);

      $numOfProductsToView = 12;
      $pricePart = 0.2;

      if ($data['networkDeviceExists'])
      {
        $generalData['seoFields'] -> description = $data['networkDevice'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['networkDevice'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['networkDevice'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['networkDevice'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('network_devices_images') -> where('networkDeviceId', '=', $data['networkDevice'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['networkDevice'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['networkDevice'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['networkDevice'] -> newPrice = $data['networkDevice'] -> price - $data['networkDevice'] -> discount;
        $data['networkDevice'] -> categoryId = BaseModel::getTableAliasByModelName(NetworkDevice::class);

        $percent = $data['networkDevice'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['networkDevice'] -> newPrice - $percent);
        $rightRange = (int) ($data['networkDevice'] -> newPrice + $percent);
        $fields = ['network_devices.id', 'title', 'mainImage', 'discount', 'price'];

        $data['recommendedNetworkDevices'] = \DB::table('network_devices') -> select($fields)
                                                                           -> where('visibility', 1)
                                                                           -> where('price', '<=', $rightRange)
                                                                           -> where('price', '>=', $leftRange)
                                                                           -> where('network_devices.id', '!=', $data['networkDevice'] -> id)
                                                                           -> take($numOfProductsToView)
                                                                           -> get();

        $data['recommendedNetworkDevicesExist'] = !$data['recommendedNetworkDevices'] -> isEmpty();

        if ($data['recommendedNetworkDevicesExist'])

        foreach($data['recommendedNetworkDevices'] as $key => $value)
        {
          $data['recommendedNetworkDevices'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(NetworkDevice::class);

        return View::make('contents.shop.networkDevices.view', ['contentData' => $data,
                                                                'generalData' => $generalData]);
      }

      else abort(404);
    }
}
