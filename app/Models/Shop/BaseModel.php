<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function collectStatisticalData($classFullName)
    {
       $pages = \DB::table('pages') -> get();

       if(!$pages -> isEmpty())
       {
          $classShortName = substr($classFullName, strrpos($classFullName, '\\') + 1);
          $pageData = \DB::table('pages') -> select(['id']) -> where('className', '=', $classShortName) -> get() -> first();

          if(!is_null($pageData))
          {
            $pageId = $pageData -> id;
            $visitorInternetAddress = $_SERVER['REMOTE_ADDR'];

            $timestamp = time();
            $visitPeriod = 86400;

            $lastVisitData = \DB::table('visitors') -> select('timestamp')
                                                    -> where('internetAddress', $visitorInternetAddress)
                                                    -> where('pageId', $pageId)
                                                    -> orderBy('timestamp', 'desc')
                                                    -> first();

            if(is_null($lastVisitData)) // if page is not visited

            \DB::table('visitors') -> insert(['pageId' => $pageId, 'timestamp' => $timestamp, 'internetAddress' => $visitorInternetAddress]);

            else
            {
              $maxVisitTimestamp = $lastVisitData -> timestamp;

              $visitDifference = $timestamp - $maxVisitTimestamp;

              if($visitDifference >= $visitPeriod)

              \DB::table('visitors') -> insert(['pageId' => $pageId, 'timeStamp' => $timestamp, 'internetAddress' => $visitorInternetAddress]);
            }
          }
       }
    }

    public static function getTableAliasByModelName($classFullName)
    {
      $classShortName = substr($classFullName, strrpos($classFullName, '\\') + 1);

      $pluralName = \Str::plural($classShortName);

      $tableName = \Str::snake($pluralName);

      $tableData = \DB::table('tables') -> select('alias') -> where('name', $tableName) -> first();

      return is_null($tableData) ? null : $tableData -> alias;
    }

    public static function getGeneralData()
    {
       $notificationCookieLifeSpan = time() + (3600 * 24 * 30);
       $notificationCookieName = 'display-message';

       $data['informationToUsers'] = \DB::table('notification') -> first();
       $data['displayMessageCookieIsDefined'] = true;

       $data['accessoriesTypes'] = \DB::table('accessories_types') -> get();
       $data['networkDevicesTypes'] = \DB::table('network_devices_types') -> get();
       $data['peripheralsTypes'] = \DB::table('peripherals_types') -> get();
       $data['contact'] = \DB::table('contacts') -> first();
       $data['seoFields'] = \DB::table('general_seo_data') -> first();
       $data['tables'] = \DB::table('tables') -> get();

       $data['contactExists'] = !is_null($data['contact']);
       $data['seoFieldsExist'] = !is_null($data['seoFields']);

       if($data['seoFieldsExist'] && $data['contactExists'])
       {
         $data['seoFields'] -> title = $data['contact'] -> companyName;
       }

       if(!is_null($data['informationToUsers']) && $data['informationToUsers'] -> visibility)
       {
          if(!isset($_COOKIE['display-message']))
          {
            $data['displayMessageCookieIsDefined'] = false;

            setcookie($notificationCookieName, 1, $notificationCookieLifeSpan);
          }
       }

       return $data;
    }

    public static function getPriceRange($classFullName, $tableName = null)
    {
      $classShortName = substr($classFullName, strrpos($classFullName, '\\') + 1);
      $tableName = is_null($tableName) ? 'price_configurations' : $tableName;

      $minColumn = \Str::camel($classShortName) . 'MinPrice';
      $maxColumn = \Str::camel($classShortName) . 'MaxPrice';

      $priceRange = \DB::table($tableName) -> select([$minColumn, $maxColumn]) -> first();

      return $priceRange;
    }
}
