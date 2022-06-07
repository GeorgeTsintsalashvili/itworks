<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\VideoCard;

class VideoCardController extends Controllers\Controller
{
    public function getList(Request $request)
    {
      $data['productsExist'] = false;

      $numOfProductsToView = 9;
      $supportedOrders = [1, 2, 3, 4, 5, 6];
      $priceRange = BaseModel::getPriceRange(VideoCard::class);

      $parameters = $request -> all(); // user input

      $validator = \Validator::make($parameters, ['active-page' => 'required|integer',
                                                  'price-from' => 'required|integer',
                                                  'price-to' => 'required|integer',
                                                  'order' => 'required|integer',
                                                  'numOfProductsToShow' => 'required|integer',
                                                  'stock-type' => 'required|string',
                                                  'condition' => 'required|string',
                                                  'gpu-manufacturer' => 'required|string',
                                                  'memory-capacity' => 'required|string',
                                                  'memory-type' => 'required|string',
                                                  'memory-bandwidth' => 'required|string',
                                                  'video-card-manufacturer' => 'required|string']);

      if (!$validator -> fails() && !is_null($priceRange))
      {
        $numOfProductsToView = abs((int) $parameters['numOfProductsToShow']);
        $productsOrder = abs((int) $parameters['order']);

        if ($numOfProductsToView && $numOfProductsToView % 3 == 0 && $numOfProductsToView <= 30)
        {
          $priceFrom = abs((int) $parameters['price-from']);
          $priceTo = abs((int) $parameters['price-to']);

          $priceFromIsInRange = $priceFrom >= $priceRange -> videoCardMinPrice && $priceFrom <= $priceRange -> videoCardMaxPrice;
          $priceToIsInRange = $priceTo >= $priceRange -> videoCardMinPrice && $priceTo <= $priceRange -> videoCardMaxPrice;

          if ($priceFromIsInRange && $priceToIsInRange)
          {
            $conditions = \DB::table('conditions') -> get();
            $conditionExists = $conditions -> count() != 0;

            $stockTypes = \DB::table('stock_types') -> get();
            $stockTypeExists = $stockTypes -> count() != 0;

            $memoryCapacity = \DB::table('video_cards') -> selectRaw('DISTINCT(`memory`) AS `memory`') -> get();
            $memoryCapacityExists = $memoryCapacity -> count() != 0;

            $gpuManufacturers = \DB::table('gpu_manufacturers') -> get();
            $gpuManufacturersExist = $gpuManufacturers -> count() != 0;

            $videoCardsManufacturers = \DB::table('video_cards_manufacturers') -> get();
            $videoCardsManufacturersExist = $videoCardsManufacturers -> count() != 0;

            $memoryTypes = \DB::table('video_cards_memory_types') -> get();
            $memoryTypesExist = $memoryTypes -> count() != 0;

            $memoryBandwidth = \DB::table('video_cards') -> selectRaw('DISTINCT(`memoryBandwidth`) AS `memoryBandwidth`') -> get();
            $memoryBandwidthExists = $memoryBandwidth -> count() != 0;

            if ($memoryCapacityExists && $gpuManufacturersExist && $videoCardsManufacturersExist && $memoryTypesExist && $memoryBandwidthExists && $stockTypeExists && $conditionExists)
            {
              $memoryCapacityParamParts = array_map('intval', explode(':', $parameters['memory-capacity']));
              $gpuManufacturerParamParts = array_map('intval', explode(':', $parameters['gpu-manufacturer']));
              $videoCardsManufacturersParamParts = array_map('intval', explode(':', $parameters['video-card-manufacturer']));
              $memoryTypesParamParts = array_map('floatval', explode(':', $parameters['memory-type']));
              $memoryBandwidthParamParts = array_map('floatval', explode(':', $parameters['memory-bandwidth']));
              $conditionsParts = array_map('intval', explode(':', $parameters['condition']));
              $stockTypesParts = array_map('intval', explode(':', $parameters['stock-type']));

              $columns = ['video_cards.id', 'title', 'mainImage', 'discount', 'price', 'gpuTitle', 'typeTitle', 'memory', 'memoryBandwidth', 'stockTypeId', 'enableAddToCartButton'];
              $memoryCapacityNumbers = $videoCardsManufacturersNumbers = $gpuManufacturersNumbers = $memoryTypesNumbers = $memoryBandwidthNumbers = $conditionNumbers = $stockTypesNumbers = [];

              $query = \DB::table('video_cards') -> select($columns)
                                                 -> join('video_cards_memory_types', 'video_cards_memory_types.id', '=', 'video_cards.memoryTypeId')
                                                 -> join('gpu_manufacturers', 'gpu_manufacturers.id', '=', 'video_cards.gpuManufacturerId')
                                                 -> join('stock_types', 'stock_types.id', '=', 'video_cards.stockTypeId')
                                                 -> where('visibility', 1);

              foreach($conditions as $value) $conditionNumbers[] = $value -> id;
              foreach($stockTypes as $value) $stockTypesNumbers[] = $value -> id;
              foreach($memoryCapacity as $value) $memoryCapacityNumbers[] = $value -> memory;
              foreach($memoryBandwidth as $value) $memoryBandwidthNumbers[] = $value -> memoryBandwidth;
              foreach($gpuManufacturers as $value) $gpuManufacturersNumbers[] = $value -> id;
              foreach($videoCardsManufacturers as $value) $videoCardsManufacturersNumbers[] = $value -> id;
              foreach($memoryTypes as $value) $memoryTypesNumbers[] = $value -> id;

              if (array_intersect($conditionsParts, $conditionNumbers) == $conditionsParts) $query = $query -> whereIn('conditionId', $conditionsParts);
              if (array_intersect($stockTypesParts, $stockTypesNumbers) == $stockTypesParts) $query = $query -> whereIn('stockTypeId', $stockTypesParts);
              if (array_intersect($memoryCapacityParamParts, $memoryCapacityNumbers) == $memoryCapacityParamParts) $query = $query -> whereIn('memory', $memoryCapacityParamParts);
              if (array_intersect($gpuManufacturerParamParts, $gpuManufacturersNumbers) == $gpuManufacturerParamParts) $query = $query -> whereIn('gpuManufacturerId', $gpuManufacturerParamParts);
              if (array_intersect($videoCardsManufacturersParamParts, $videoCardsManufacturersNumbers) == $videoCardsManufacturersParamParts) $query = $query -> whereIn('videoCardManufacturerId', $videoCardsManufacturersParamParts);
              if (array_intersect($memoryTypesParamParts, $memoryTypesNumbers) == $memoryTypesParamParts) $query = $query -> whereIn('memoryTypeId', $memoryTypesParamParts);
              if (array_intersect($memoryBandwidthParamParts, $memoryBandwidthNumbers) == $memoryBandwidthParamParts) $query = $query -> whereIn('memoryBandwidth', $memoryBandwidthParamParts);

              $query = $query -> where('price', '>=', $priceFrom) -> where('price', '<=', $priceTo);

              if (in_array($productsOrder, $supportedOrders))
              {
                $orderNumber = !($productsOrder % 2);
                $orderColumn = 'price';

                if ($productsOrder == 3 || $productsOrder == 4) $orderColumn = 'memory';

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

      return View::make('contents.shop.videoCards.getVideoCards', ['data' => $data]);
    }

    public function index(int $page = 1)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;

      $data['configuration']['productPriceRange'] = BaseModel::getPriceRange(VideoCard::class);
      $data['configuration']['productPriceRangeExists'] = !is_null($data['configuration']['productPriceRange']);
      $data['videoCardsExist'] = false;

      if ($data['configuration']['productPriceRangeExists'])
      {
        $productMinPrice = $data['configuration']['productPriceRange'] -> videoCardMinPrice;
        $productMaxPrice = $data['configuration']['productPriceRange'] -> videoCardMaxPrice;

        $totalNumOfProducts = VideoCard::where('visibility', 1) -> where('price', '>=', $productMinPrice) -> where('price', '<=', $productMaxPrice) -> count();

        $data['videoCards'] = \DB::table('video_cards') -> select(['video_cards.id', 'title', 'mainImage', 'discount', 'price', 'gpuTitle', 'typeTitle', 'memory', 'memoryBandwidth', 'stockTypeId', 'enableAddToCartButton'])
                                                        -> join('video_cards_memory_types', 'video_cards_memory_types.id', '=', 'video_cards.memoryTypeId')
                                                        -> join('gpu_manufacturers', 'gpu_manufacturers.id', '=', 'video_cards.gpuManufacturerId')
                                                        -> join('stock_types', 'stock_types.id', '=', 'video_cards.stockTypeId')
                                                        -> where('visibility', 1)
                                                        -> where('price', '>=', $productMinPrice)
                                                        -> where('price', '<=', $productMaxPrice)
                                                        -> skip(($page - 1) * $numOfProductsToView)
                                                        -> take($numOfProductsToView)
                                                        -> get();

        $data['videoCardsExist'] = !$data['videoCards'] -> isEmpty();

        if ($data['videoCardsExist'])
        {
          $data['productsCategoryId'] = BaseModel::getTableAliasByModelName(VideoCard::class);

          $data['configuration']['gpuManufacturers'] = \DB::table('gpu_manufacturers') -> get();
          $data['configuration']['videoCardsManufacturers'] = \DB::table('video_cards_manufacturers') -> get();
          $data['configuration']['memoryTypes'] = \DB::table('video_cards_memory_types') -> get();

          $data['configuration']['conditions'] = \DB::table('conditions') -> get();
          $data['configuration']['stockTypes'] = \DB::table('stock_types') -> get();

          $data['configuration']['memoryInterfaces'] = \DB::table('video_cards') -> selectRaw('DISTINCT(`memoryBandwidth`) AS `memoryBandwidth`')
                                                                                 -> where('visibility', 1)
                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                 -> orderBy('memoryBandwidth', 'ASC')
                                                                                 -> get();

          $data['configuration']['memoryCapacities'] = \DB::table('video_cards') -> selectRaw('DISTINCT(`memory`) AS `memory`')
                                                                                 -> where('visibility', 1)
                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                 -> orderBy('memory', 'ASC')
                                                                                 -> get();

          foreach($data['configuration']['memoryCapacities'] as $key => $value)
          {
            $data['configuration']['memoryCapacities'][$key] -> numOfProducts = \DB::table('video_cards') -> where('memory', $value -> memory)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['configuration']['memoryInterfaces'] as $key => $value)
          {
            $data['configuration']['memoryInterfaces'][$key] -> numOfProducts = \DB::table('video_cards') -> where('memoryBandwidth', $value -> memoryBandwidth)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['configuration']['gpuManufacturers'] as $key => $value)
          {
            $data['configuration']['gpuManufacturers'][$key] -> numOfProducts = \DB::table('video_cards') -> where('gpuManufacturerId', $value -> id)
                                                                                                          -> where('visibility', 1)
                                                                                                          -> where('price', '>=', $productMinPrice)
                                                                                                          -> where('price', '<=', $productMaxPrice)
                                                                                                          -> count();
          }

          foreach($data['configuration']['videoCardsManufacturers'] as $key => $value)
          {
            $data['configuration']['videoCardsManufacturers'][$key] -> numOfProducts = \DB::table('video_cards') -> where('videoCardManufacturerId', $value -> id)
                                                                                                                 -> where('visibility', 1)
                                                                                                                 -> where('price', '>=', $productMinPrice)
                                                                                                                 -> where('price', '<=', $productMaxPrice)
                                                                                                                 -> count();
          }

          $data['configuration']['videoCardsManufacturers'] = $data['configuration']['videoCardsManufacturers'] -> filter(function ($record) {

            return $record -> numOfProducts;
          });

          foreach($data['configuration']['memoryTypes'] as $key => $value)
          {
            $data['configuration']['memoryTypes'][$key] -> numOfProducts = \DB::table('video_cards') -> where('memoryTypeId', $value -> id)
                                                                                                     -> where('visibility', 1)
                                                                                                     -> where('price', '>=', $productMinPrice)
                                                                                                     -> where('price', '<=', $productMaxPrice)
                                                                                                     -> count();
          }

          $data['configuration']['memoryTypes'] = $data['configuration']['memoryTypes'] -> filter(function ($record) {

            return $record -> numOfProducts;
          });

          foreach($data['configuration']['conditions'] as $key => $value)
          {
            $data['configuration']['conditions'][$key] -> numOfProducts = \DB::table('video_cards') -> where('conditionId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();
          }

          foreach($data['configuration']['stockTypes'] as $key => $value)
          {
            $data['configuration']['stockTypes'][$key] -> numOfProducts = \DB::table('video_cards') -> where('stockTypeId', $value -> id)
                                                                                                    -> where('visibility', 1)
                                                                                                    -> where('price', '>=', $productMinPrice)
                                                                                                    -> where('price', '<=', $productMaxPrice)
                                                                                                    -> count();
          }

          foreach($data['videoCards'] as $key => $value)
          {
            $data['videoCards'][$key] -> newPrice = $value -> price - $value -> discount;
          }

          $paginator = \Paginator::build($totalNumOfProducts, 3, $numOfProductsToView, $page, 2, 0);

          $data['pages'] = $paginator -> pages;
          $data['maxPage'] = $paginator -> maxPage;
          $data['currentPage'] = $paginator -> currentPage;
        }
      }

      BaseModel::collectStatisticalData(VideoCard::class);

      return View::make('contents.shop.videoCards.index', ['contentData' => $data,
                                                           'generalData' => $generalData]);
    }

    public function view($id)
    {
      $generalData = BaseModel::getGeneralData();
      $numOfProductsToView = 12;
      $pricePart = 0.2;
      $columns = ['video_cards.id', 'title', 'mainImage', 'discount', 'price', 'description', 'warrantyDuration', 'warrantyId', 'stockTypeId', 'conditionId', 'quantity', 'seoDescription', 'seoKeywords'];

      $data['videoCard'] = \DB::table('video_cards') -> select($columns) -> where('id', $id) -> where('visibility', 1) -> get() -> first();
      $data['videoCardExists'] = !is_null($data['videoCard']);

      if ($data['videoCardExists'])
      {
        $generalData['seoFields'] -> description = $data['videoCard'] -> seoDescription;
        $generalData['seoFields'] -> keywords = $data['videoCard'] -> seoKeywords;
        $generalData['seoFields'] -> title = $data['videoCard'] -> title;

        $stockData = \DB::table('stock_types') -> where('id', '=', $data['videoCard'] -> stockTypeId) -> get() -> first();

        $data['images'] = \DB::table('video_cards_images') -> where('videoCardId', '=', $data['videoCard'] -> id) -> get();
        $data['imagesExist'] = !$data['images'] -> isEmpty();

        $data['stockTitle'] = $stockData -> stockTitle;
        $data['stockStatusColor'] = $stockData -> statusColor;
        $data['enableAddToCartButton'] = $stockData -> enableAddToCartButton;

        $data['conditionTitle'] = \DB::table('conditions') -> where('id', '=', $data['videoCard'] -> conditionId) -> get() -> first() -> conditionTitle;
        $data['warrantyTitle'] = \DB::table('warranties') -> where('id', '=', $data['videoCard'] -> warrantyId) -> get() -> first() -> durationUnit;

        $data['videoCard'] -> newPrice = $data['videoCard'] -> price - $data['videoCard'] -> discount;
        $data['videoCard'] -> categoryId = BaseModel::getTableAliasByModelName(VideoCard::class);

        $percent = $data['videoCard'] -> newPrice * $pricePart;
        $leftRange = (int) ($data['videoCard'] -> newPrice - $percent);
        $rightRange = (int) ($data['videoCard'] -> newPrice + $percent);

        $fields = ['video_cards.id', 'title', 'mainImage', 'discount', 'price', 'memory', 'memoryBandwidth', 'typeTitle', 'gpuTitle'];

        $data['recommendedVideoCards'] = \DB::table('video_cards') -> select($fields)
                                                                   -> join('gpu_manufacturers', 'gpu_manufacturers.id', '=', 'gpuManufacturerId')
                                                                   -> join('video_cards_memory_types', 'video_cards_memory_types.id', '=', 'memoryTypeId')
                                                                   -> where('visibility', 1)
                                                                   -> where('price', '<=', $rightRange)
                                                                   -> where('price', '>=', $leftRange)
                                                                   -> where('video_cards.id', '!=', $data['videoCard'] -> id)
                                                                   -> limit($numOfProductsToView)
                                                                   -> get();

        $data['recommendedVideoCardsExist'] = !$data['recommendedVideoCards'] -> isEmpty();

        if ($data['recommendedVideoCardsExist'])
        {
          foreach($data['recommendedVideoCards'] as $key => $value)

          $data['recommendedVideoCards'][$key] -> newPrice = $value -> price - $value -> discount;
        }

        BaseModel::collectStatisticalData(VideoCard::class);

        return View::make('contents.shop.videoCards.view', ['contentData' => $data,
                                                            'generalData' => $generalData]);
      }

      else abort(404);
    }
}
