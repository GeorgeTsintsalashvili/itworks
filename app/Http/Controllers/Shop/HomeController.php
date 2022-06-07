<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Home;

class HomeController extends Controllers\Controller
{
    public function index(Request $request)
    {
       $generalData = BaseModel::getGeneralData();

       $numberOfAccessoriesToView = 12;
       $numberOfSystemsToView = 15;
       $numberOfLatestProductsToView = 40;
       $numberOfDiscountedProductsToView = 50;

       $computerColumns = ['computers.id', 'title', 'mainImage', 'discount', 'price', 'cpu', 'memory', 'solidStateDriveCapacity', 'hardDiscDriveCapacity', 'gpuTitle'];
       $accessoriesColumns = ['accessories.id', 'price', 'discount', 'title', 'mainImage'];

       $specialOffers = \DB::table('computers') -> select($computerColumns) -> where('visibility', '=', 1) -> where('isOffer', '=', 1) -> get();
       $activeAccessories = null;
       $activeSystems = null;

       $discountedProductsQuery = null;
       $latestProductsQuery = null;

       $slides = \DB::table('slides') -> orderBy('orderNum') -> get();
       $cpuSeries = \DB::table('cpu_series') -> select(['cpu_series.id', 'homePageTitle']) -> join('computers', 'computers.seriesId', '=', 'cpu_series.id') -> groupBy('computers.seriesId') -> havingRaw('COUNT(`seriesId`) != 0') -> where('visibility', '!=', 0) -> get();
       $accessoriesTypes = \DB::table('accessories_types') -> select(['accessories_types.id', 'typeTitle']) -> join('accessories', 'accessories.accessoryTypeId', '=', 'accessories_types.id') -> groupBy('accessories.accessoryTypeId') -> havingRaw('COUNT(`accessoryTypeId`) != 0') -> where('visibility', '!=', 0) -> get();

       // data for raw sql statement

       $tablesData = \DB::table('tables') -> where('blacklisted', 0) -> get();

       $data['slidesExist'] = !$slides -> isEmpty();
       $data['cpuSeriesExist'] = !$cpuSeries -> isEmpty();
       $data['accessoriesTypesExist'] = !$accessoriesTypes -> isEmpty();
       $data['specialOffersExist'] = !$specialOffers -> isEmpty();
       $data['discountedProductsExist'] = false;
       $data['latestProductsExist'] = false;

       $data['activeAccessoryCategoryId'] = 0;
       $data['activeCpuSeriesId'] = 0;

       if ($data['accessoriesTypesExist'])
       {
          $activeAccessories = \DB::table('accessories') -> select($accessoriesColumns) -> where('accessoryTypeId', $accessoriesTypes -> first() -> id) -> where('visibility', '=', 1) -> take($numberOfAccessoriesToView) -> get();

          if (!$activeAccessories -> isEmpty())
          {
              $activeAccessories -> each(function ($accessory, $key) use($activeAccessories) {

                  $activeAccessories[$key] -> newPrice = $accessory -> price - $accessory -> discount;
              });

              $data['activeAccessoryCategoryId'] = $accessoriesTypes -> first() -> id;
          }
       }

       if ($data['cpuSeriesExist'])
       {
          $cpuSeries -> each(function($series) use ($computerColumns, $numberOfSystemsToView, &$activeSystems, &$data){

              $activeSystems = \DB::table('computers') -> select($computerColumns)
                                                       -> where('visibility', '=', 1)
                                                       -> where('seriesId', '=', $series -> id)
                                                       -> take($numberOfSystemsToView)
                                                       -> get();

              if (!$activeSystems -> isEmpty())
              {
                $activeSystems -> map(function($system){

                    $hddCapacity = $system -> hardDiscDriveCapacity;
                    $ssdCapacity = $system -> solidStateDriveCapacity;
                    $storage = null;

                    if ($hddCapacity && $ssdCapacity) $storage = "HDD {$hddCapacity} GB SSD {$ssdCapacity} GB";

                    else if ($hddCapacity && !$ssdCapacity) $storage = "HDD {$hddCapacity} GB";

                    else if (!$hddCapacity && $ssdCapacity) $storage = "SSD {$ssdCapacity} GB";

                    $system -> newPrice = $system -> price - $system -> discount;
                    $system -> storage = $storage;
                });

                $data['activeCpuSeriesId'] = $series -> id;

                return false;
              }
          });
       }

       if ($data['specialOffersExist'])
       {
          $specialOffers -> map(function($product){

              $hddCapacity = $product -> hardDiscDriveCapacity;
              $ssdCapacity = $product -> solidStateDriveCapacity;
              $storage = null;

              if ($hddCapacity && $ssdCapacity) $storage = "HDD {$hddCapacity} GB SSD {$ssdCapacity} GB";

              else if ($hddCapacity && !$ssdCapacity) $storage = "HDD {$hddCapacity} GB";

              else if (!$hddCapacity && $ssdCapacity) $storage = "SSD {$ssdCapacity} GB";

              $product -> newPrice = $product -> price - $product -> discount;
              $product -> storage = $storage;
          });
       }

       $columnsToSelect = ['id', 'timestamp', 'price', 'discount', 'title', 'mainImage', 'pathPart'];

       $tablesData -> each(function($item) use (&$discountedProductsQuery, &$latestProductsQuery, $columnsToSelect){

           $pathPartToAssign = \Str::camel($item -> name);

           $discountedProductsTempQuery = \DB::table($item -> name) -> select($columnsToSelect) -> where('visibility', 1) -> where('discount', '!=', 0);

           if (!$discountedProductsQuery) $discountedProductsQuery = $discountedProductsTempQuery;

           else $discountedProductsQuery -> union($discountedProductsTempQuery);

           $latestProductsTempQuery = \DB::table($item -> name) -> select($columnsToSelect) -> where('visibility', '=', 1);

           if (!$latestProductsQuery) $latestProductsQuery = $latestProductsTempQuery;

           else $latestProductsQuery -> union($latestProductsTempQuery);
       });

       $discountedProducts = $discountedProductsQuery -> take($numberOfDiscountedProductsToView) -> get();
       $latestProducts = $latestProductsQuery -> orderBy('timestamp', 'desc') -> take($numberOfLatestProductsToView) -> get();

       if (!$discountedProducts -> isEmpty())
       {
          $data['discountedProductsExist'] = true;

          $discountedProducts -> map(function($product){

              $product -> newPrice = $product -> price - $product -> discount;
          });

          $data['discountedProducts'] = $discountedProducts;
       }

       if (!$latestProducts -> isEmpty())
       {
           $data['latestProductsExist'] = true;

           $latestProducts -> map(function($product){

               $product -> newPrice = $product -> price - $product -> discount;
           });

           $data['latestProducts'] = $latestProducts;
       }

       $data['specialOffers'] = $specialOffers;
       $data['activeAccessories'] = $activeAccessories;
       $data['activeSystems'] = $activeSystems;

       $data['slides'] = $slides;
       $data['cpuSeries'] = $cpuSeries;
       $data['accessoriesTypes'] = $accessoriesTypes;

       BaseModel::collectStatisticalData(Home::class);

       return View::make('contents.shop.home.index', ['contentData' => $data,
                                                      'generalData' => $generalData]);
    }
}
