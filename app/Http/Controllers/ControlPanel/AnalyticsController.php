<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

use Illuminate\Http\Request;
use App\Rules\NaturalNumber;

class AnalyticsController extends Controller
{
    public function index()
    {
      $pages = \DB::table('pages') -> select(['id', 'title']) -> get();
      $activePageId = 0;
      $activeAnalyticsId = 'pmmzy4q2vq';

      if($pages -> count() != 0)
      {
        $firstPage = $pages -> first();
        $activePageId = $firstPage -> id;
      }

      return \View::make('contents.controlPanel.analytics.index', [

          'pages' => $pages,
          'activePageId' => $activePageId,
          'activeAnalyticsId' => $activeAnalyticsId
      ]);
    }

    public function requireData(Request $request)
    {
       $data['labels'] = [];
       $data['series'] = [];

       $data['totalNumberOfVisitors'] = 0;
       $data['absoluteNumberOfVisitors'] = 0;
       $data['maxValue'] = 0;

       $parameters = $request -> only([ 'page-id', 'analytics-id' ]);
       $rulesToApply = [ 'page-id' => [ 'required', new NaturalNumber ],
                         'analytics-id' => 'required|string|min:1|max:100' ];

       $validator = \Validator::make($parameters, $rulesToApply);

       if(!$validator -> fails())
       {
         $allPages = \DB::table('pages') -> select(['id']) -> get();
         $allowedAnalytics = new Collection(['pmmzy4q2vq', 'iry1tax1z2', 'lochs6uu6e', '4yh6vmzl39']);
         $pagesIdentifiers = $allPages -> pluck('id');
         $currentPage = \DB::table('pages') -> where('id', '=', $parameters['page-id']) -> first();
         $pageExistsAndAnalyticsIsValid = !is_null($currentPage) && $allowedAnalytics -> contains($parameters['analytics-id']);

         if($pageExistsAndAnalyticsIsValid)
         {
           date_default_timezone_set('Asia/Tbilisi');

           $currentTimestamp = time();
           $seconds = (int) ltrim(date('s', $currentTimestamp), 0); // from 0 to 59
           $minutes = (int) ltrim(date('i', $currentTimestamp), 0); // from 0 to 59
           $hours = (int) date('G', $currentTimestamp); // from 0 to 23

           $secondsInElapsedHours = $hours * 3600;
           $secondsInElapsedMinutes = $minutes * 60;

           if($parameters['analytics-id'] === 'pmmzy4q2vq')
           {
             $currentDayAnalyticsDataForActivePage = array_fill(0, 24, 0);
             $numberOfSecondsElapsedTillCurrentHour = $currentTimestamp - ($secondsInElapsedMinutes + $seconds);

             for($i = 0, $k = $hours; $i <= $hours; $i++, $k--)
             {
               $hoursOffset = $i * 3600;
               $leftBound = $numberOfSecondsElapsedTillCurrentHour - $hoursOffset;
               $rightBound = $leftBound + 3600;

               $currentDayAnalyticsDataForActivePage[$k] = \DB::table('visitors') -> where('pageId', $parameters['page-id']) -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();

               $data['absoluteNumberOfVisitors'] += \DB::table('visitors') -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();
             }

             $data['totalNumberOfVisitors'] = array_sum($currentDayAnalyticsDataForActivePage);
             $data['labels'] = range(0, 23);
             $data['series'] = $currentDayAnalyticsDataForActivePage;
             $data['maxValue'] = max($currentDayAnalyticsDataForActivePage);
           }

           else if($parameters['analytics-id'] === 'iry1tax1z2')
           {
             $currentWeekAnalyticsDataForActivePage = array_fill(0, 7, 0);
             $currentDayNumberOfWeek = date('N', $currentTimestamp) - 1;

             $secondsInDay = 24 * 3600;
             $numberOfSecondsElapsedInDay = $secondsInElapsedHours + $secondsInElapsedMinutes + $seconds;
             $timestampByWeek = $currentTimestamp - $numberOfSecondsElapsedInDay;

             for($i = 0, $k = $currentDayNumberOfWeek; $i <= $currentDayNumberOfWeek; $i++, $k--)
             {
               $daysOffset = $i * $secondsInDay;
               $leftBound = $timestampByWeek - $daysOffset;
               $rightBound = $leftBound + $secondsInDay;

               $currentWeekAnalyticsDataForActivePage[$k] = \DB::table('visitors') -> where('pageId', $parameters['page-id']) -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();

               $data['absoluteNumberOfVisitors'] += \DB::table('visitors') -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();
             }

             $data['totalNumberOfVisitors'] = array_sum($currentWeekAnalyticsDataForActivePage);
             $data['labels'] = ['ორშ', 'სამშ', 'ოთხშ', 'ხუთშ', 'პარ', 'შაბ', 'კვ'];
             $data['series'] = $currentWeekAnalyticsDataForActivePage;
             $data['maxValue'] = max($currentWeekAnalyticsDataForActivePage);
           }

           else if($parameters['analytics-id'] === 'lochs6uu6e')
           {
             $currentYearNum = (int) date('Y', $currentTimestamp);
             $currentMonthNum = (int) date('n', $currentTimestamp);

             $currentDayOfMonthNum = (int) date('j', $currentTimestamp);
             $numberOfDaysInCurrentMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonthNum, $currentYearNum);

             $currentMonthAnalyticsDataForActivePage = array_fill(0, $numberOfDaysInCurrentMonth, 0);

             $secondsInDay = 24 * 3600;
             $numberOfSecondsElapsedInDay = $secondsInElapsedHours + $secondsInElapsedMinutes + $seconds;
             $timestampByMonth = $currentTimestamp - $numberOfSecondsElapsedInDay;

             for($i = 0, $k = $currentDayOfMonthNum - 1; $i < $currentDayOfMonthNum; $i++, $k--)
             {
               $daysOffset = $i * $secondsInDay;
               $leftBound = $timestampByMonth - $daysOffset;
               $rightBound = $leftBound + $secondsInDay;

               $currentMonthAnalyticsDataForActivePage[$k] = \DB::table('visitors') -> where('pageId', '=', $parameters['page-id']) -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();

               $data['absoluteNumberOfVisitors'] += \DB::table('visitors') -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();
             }

             $data['totalNumberOfVisitors'] = array_sum($currentMonthAnalyticsDataForActivePage);
             $data['labels'] = range(1, $numberOfDaysInCurrentMonth);
             $data['series'] = $currentMonthAnalyticsDataForActivePage;
             $data['maxValue'] = max($currentMonthAnalyticsDataForActivePage);
           }

           else if($parameters['analytics-id'] === '4yh6vmzl39')
           {
             $currentYearAnalyticsDataForActivePage = array_fill(0, 12, 0);
             $currentYearNum = (int) date('Y', $currentTimestamp);
             $currentMonthNum = (int) date('n', $currentTimestamp);
             $currentDayOfMonthNum = (int) date('j', $currentTimestamp);

             $sumOfDaysInMonths = [];
             $monthsRangeSum = 0;

             $secondsInDay = 24 * 3600;
             $exactNumberOfSecondsElapsedInDay = $secondsInElapsedHours + $secondsInElapsedMinutes + $seconds;
             $exactNumberOfSecondsElapsedInMonth = (($currentDayOfMonthNum - 1) * $secondsInDay) + $exactNumberOfSecondsElapsedInDay;
             $exactTimestampByYear = $currentTimestamp - $exactNumberOfSecondsElapsedInMonth;

             for($monthNum = 1; $monthNum <= 12; $monthNum++)
             {
               $numberOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $currentYearNum);
               $monthsRangeSum += $numberOfDaysInMonth;

               $sumOfDaysInMonths[] = $monthsRangeSum;
               $daysInMonths[] = $numberOfDaysInMonth;
             }

             $maxMonthIndex = $currentMonthNum - 1;

             for($i = 0, $j = $maxMonthIndex; $i < $currentMonthNum; $i++, $j--)
             {
               $monthsOffset = $secondsInDay * ($sumOfDaysInMonths[$i] - $daysInMonths[$i]);
               $leftBound = $exactTimestampByYear - $monthsOffset;
               $rightBound = $leftBound + ($secondsInDay * $daysInMonths[$maxMonthIndex - $i]);

               $currentYearAnalyticsDataForActivePage[$j] = \DB::table('visitors') -> where('pageId', $parameters['page-id']) -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();

               $data['absoluteNumberOfVisitors'] += \DB::table('visitors') -> where('timestamp', '>', $leftBound) -> where('timestamp', '<', $rightBound) -> count();
             }

             $data['totalNumberOfVisitors'] = array_sum($currentYearAnalyticsDataForActivePage);
             $data['labels'] = ['იან', 'თებ', 'მარ', 'აპრ', 'მაი', 'ივნ', 'ივლ', 'ავგ', 'სექ', 'ოქტ', 'ნოემ', 'დეკ'];
             $data['series'] = $currentYearAnalyticsDataForActivePage;
             $data['maxValue'] = max($currentYearAnalyticsDataForActivePage);
           }
         }
       }

       return $data;
    }

    public function destroyData(Request $request)
    {
      $parameters = $request -> only([ 'page-id', 'analytics-id' ]);
      $rulesToApply = [ 'page-id' => [ 'required', new NaturalNumber ],
                        'analytics-id' => 'required|string|min:1|max:100' ];

      $validator = \Validator::make($parameters, $rulesToApply);

      if(!$validator -> fails())
      {
        $allowedAnalytics = new Collection(['pmmzy4q2vq', 'iry1tax1z2', 'lochs6uu6e', '4yh6vmzl39']);
        $currentPage = \DB::table('pages') -> where('id', '=', $parameters['page-id']) -> first();
        $pageExistsAndAnalyticsIsValid = !is_null($currentPage) && $allowedAnalytics -> contains($parameters['analytics-id']);

        if($pageExistsAndAnalyticsIsValid)
        {
          date_default_timezone_set('Asia/Tbilisi');

          $currentTimestamp = time();
          $seconds = (int) ltrim(date('s', $currentTimestamp), 0); // from 0 to 59
          $minutes = (int) ltrim(date('i', $currentTimestamp), 0); // from 0 to 59
          $hours = (int) date('G', $currentTimestamp); // from 0 to 23

          $currentYearNum = (int) date('Y', $currentTimestamp);
          $currentMonthNum = (int) date('n', $currentTimestamp);
          $currentMonthDate = (int) date('j', $currentTimestamp);
          $currentWeekDay = (int) date('N', $currentTimestamp);

          $numberOfDaysInCurrentMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonthNum, $currentYearNum);
          $numberOfDaysInFebruary = cal_days_in_month(CAL_GREGORIAN, 2, $currentYearNum);
          $numberOfDaysInCurrentYear = $numberOfDaysInFebruary == 29 ? 366 : 365;

          $secondsElapsedInDay = mktime(0, 0, 0, $currentMonthNum, $currentMonthDate, $currentYearNum);
          $secondsElapsedInWeek = (mktime(0, 0, 0, $currentMonthNum, $currentMonthDate, $currentYearNum) - (24 * 3600 * $currentWeekDay));
          $secondsElapsedInMonth = mktime(0, 0, 0, $currentMonthNum, 1, $currentYearNum);
          $secondsElapsedInYear = mktime(0, 0, 0, 1, 1, $currentYearNum);

          $secondsElapsedToCompare = 0;

          switch($parameters['analytics-id'])
          {
            case 'pmmzy4q2vq' : $secondsElapsedToCompare = $secondsElapsedInDay;  break;
            case 'iry1tax1z2' : $secondsElapsedToCompare = $secondsElapsedInWeek; break;
            case 'lochs6uu6e' : $secondsElapsedToCompare = $secondsElapsedInMonth; break;
            case '4yh6vmzl39' : $secondsElapsedToCompare = $secondsElapsedInYear; break;
          }

          if($secondsElapsedToCompare != 0)
          {
            \DB::table('visitors') -> where('pageId', $parameters['page-id']) -> where('timestamp', '>', $secondsElapsedToCompare) -> delete();

            return ['success' => true];
          }
        }
      }

      return ['success' => false];
    }
}
