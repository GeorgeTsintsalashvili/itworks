<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;

class UpdateStatements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update statements';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $columns = ['statements.id', 'updateEnabled', 'superVip', 'lastUpdateTimestamp', 'identifiers', 'updateSchedule', 'categoryId', 'parameterValue'];

      $superVipStatementsToUpdate = \DB::table('statements') -> select($columns) -> join('statements_categories', 'statements_categories.id', '=', 'statements.categoryId') -> where('updateEnabled', 1) -> where('superVip', 1) -> get();
      $ordinaryStatementsToUpdate = \DB::table('statements') -> select($columns) -> join('statements_categories', 'statements_categories.id', '=', 'statements.categoryId') -> where('updateEnabled', 1) -> where('superVip', 0) -> get();

      if($superVipStatementsToUpdate -> count())
      {
        $this -> updateSuperVipStatements($superVipStatementsToUpdate);
      }

      if($ordinaryStatementsToUpdate -> count())
      {
        $this -> updateNonSuperVipStatements($ordinaryStatementsToUpdate);
      }
    }

    // update non-super vip statements

    protected function updateNonSuperVipStatements($statementsToUpdate)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $userSessionData = \DB::table('statements_data') -> first();

      if($userSessionData)
      {
        $userSession = $userSessionData -> sessionText;
        $statementsUpdateAddress = 'https://www.mymarket.ge/ka/mypage/updatepr/';

        $userIdKey = 'user_id';
        $desiredUserId = '2267332';

        $statementsToUpdate -> each(function($statement) use($statementsUpdateAddress, $userIdKey, $desiredUserId, $userSession){

            $schedule = $statement -> updateSchedule;
            $categoryId = $statement -> parameterValue;
            $commaSeparatedIdentifiers = $statement -> identifiers;

            $statementUpdatePermited = $this -> getUpdatePermissionBasedOnSchedule($schedule);

              if($statementUpdatePermited)
              {
                $identifiers = explode(', ', $commaSeparatedIdentifiers);

                $statementsUpdateFormData = ['PrIDs' => $identifiers,
                                             'UpdateTypeID' => '0',
                                             'Quantity' => '1'];

                $statementUpdated = $this -> sendStatementUpdateRequest($statementsUpdateAddress, $userSession, $statementsUpdateFormData);

                if($statementUpdated) \DB::table('statements') -> where('id', $statement -> id) -> update(['lastUpdateTimestamp' => time()]);
              }
         });

        // method end
      }
    }

    // update super vip statements

    protected function updateSuperVipStatements($superVipStatementsToUpdate)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $userSessionData = \DB::table('statements_data') -> first();

      if($userSessionData)
      {
        $userSession = $userSessionData -> sessionText;

        $userSuperVipStatementsPageAddress = 'https://www.mymarket.ge/ka/mypage/products/';
        $superVipStatmentsPageAddress = 'https://www.mymarket.ge/ka/search/getproducts';
        $statementsUpdateAddress = 'https://www.mymarket.ge/ka/mypage/updatepr/';

        $userIdKey = 'user_id';
        $desiredUserId = '2267332';

        $userSuperVipStatmentsPageformData = ['MypageSearchKBD' => 'on',
                                              'StatusID' => '1',
                                              'DateTypeID' => '0',
                                              'Prom' => '20'];

        $userSuperVipStatementsPage = $this -> getUserSuperVipStatementsPageText($userSuperVipStatementsPageAddress, $userSession, $userSuperVipStatmentsPageformData);

        if($userSuperVipStatementsPage)
        {
          $superVipStatementsToUpdate -> each(function($statement) use($userSuperVipStatementsPage, $userSuperVipStatementsPageAddress, $superVipStatmentsPageAddress, $statementsUpdateAddress, $userIdKey, $desiredUserId, $userSession){

               $schedule = $statement -> updateSchedule;
               $categoryId = $statement -> parameterValue;
               $commaSeparatedIdentifiers = $statement -> identifiers;

               $statementUpdatePermited = $this -> getUpdatePermissionBasedOnSchedule($schedule);

               if($statementUpdatePermited)
               {
                 $superVipStatementsIdentifiersOnUserPage = [];
                 $identifiers = explode(', ', $commaSeparatedIdentifiers);

                 foreach($identifiers as $identifier)
                 {
                   if(strpos($userSuperVipStatementsPage, $identifier) !== false)
                   {
                     $superVipStatementsIdentifiersOnUserPage[] = $identifier;
                   }
                 }

                 if(count($superVipStatementsIdentifiersOnUserPage))
                 {
                   $superVipStatementsPageformData = ['CatID' => $categoryId];

                   $superVipStatementsPageResponse = $this -> getSuperVipStatements($superVipStatmentsPageAddress, $superVipStatementsPageformData);

                   if($superVipStatementsPageResponse)
                   {
                     $desiredUserStatementIsFirst = $this -> desiredUserStatementIsFirst($superVipStatementsPageResponse, $userIdKey, $desiredUserId);

                     if(!$desiredUserStatementIsFirst)
                     {
                       $statementsUpdateFormData = ['PrIDs' => $superVipStatementsIdentifiersOnUserPage,
                                                    'UpdateTypeID' => '0',
                                                    'Quantity' => '1'];

                       $statementUpdated = $this -> sendStatementUpdateRequest($statementsUpdateAddress, $userSession, $statementsUpdateFormData);

                       if($statementUpdated) \DB::table('statements') -> where('id', $statement -> id) -> update(['lastUpdateTimestamp' => time()]);
                     }
                   }
                 }
               }
          });

          // method end
        }
      }
    }

    // update statements

    function sendStatementUpdateRequest($statementsUpdateAddress, $session, $formData)
    {
      $updateSuccess = false;

      $headers = ['Cookie' => $session,
                  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36',
                  'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                  'Accept-Encoding' => 'gzip, deflate, br'];

      $response = Http::withHeaders($headers) -> asForm() -> post($statementsUpdateAddress, $formData);

      if($response -> successful())
      {
         $responseData = $response -> json();

         if(isset($responseData['StatusCode']))
         {
           $statusCode = (int) $responseData['StatusCode'];

           if($statusCode == 1)

           $updateSuccess = true;
         }
       }

       return $updateSuccess;
    }

    // get user super vip statements list page html text

    protected function getUserSuperVipStatementsPageText($address, $cookie, $formData)
    {
      $headers = ['Cookie' => $cookie,
                  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36',
                  'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                  'Accept-Encoding' => 'gzip, deflate, br'];

      $response = Http::withHeaders($headers) -> get($address, $formData);

      return $response -> failed() ? null : $response -> body();
    }

    // get super vip statements by category

    protected function getSuperVipStatements($address, $formData)
    {
      $headers = ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36',
                  'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                  'Accept-Encoding' => 'gzip, deflate, br'];

      $query = http_build_query($formData);

      $response = Http::withHeaders($headers) -> get($address, $formData);

      $statements = null;

      if($response -> successful())
      {
        $data = $response -> json();

        if($data) $statements = $data;
      }

      return $statements;
    }

    // check if a desired user statement is first

    protected function desiredUserStatementIsFirst($response, $userIdKey, $desiredUserId)
    {
      $userStatementIsFirst = false;

      if(isset($response['Data']))
      {
        $statements = $response['Data'];

        if(isset($statements['Prs']))
        {
          $statements = $statements['Prs'];

          if(count($statements))
          {
            $firstStatement = current($statements);

            if(isset($firstStatement[$userIdKey]))
            {
              $firstStatementUserId = $firstStatement[$userIdKey];

              if($firstStatementUserId === $desiredUserId)

              $userStatementIsFirst = true;
            }
          }
        }
      }

      return $userStatementIsFirst;
    }

    // get update permission

    protected function getUpdatePermissionBasedOnSchedule($schedule)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $updatePermited = false;

      $currentTimestamp = time();
      $currentHour = (int) date('G', $currentTimestamp);
      $currentMinutes = (int) ltrim(date('i', $currentTimestamp), 0);
      $currentMinutes = $currentMinutes == 0 ? 60 : $currentMinutes;

      $anyHourPattern = '/^\[\*\]$/';
      $anyHourSpecificMinuteIntervalPattern = '/^\[\*\]:[1-6]\d?$/';
      $specificHoursPattern = '/^\[\d{1,2}(\|\d{1,2}){0,23}\]$/';
      $specificHoursMinutesIntervalPattern = '/^\[\d{1,2}(\|\d{1,2}){0,23}\]\:[1-9]\d?$/';
      $specificHoursRangesPattern = '/^\[\d{1,2}\-\d{1,2}(\|\d{1,2}\-\d{1,2}){0,23}\]$/';
      $specificHoursRangesMinutesIntervalPattern = '/^\[\d{1,2}\-\d{1,2}(\|\d{1,2}\-\d{1,2}){0,23}\]:\d{1,2}$/';

      $allowedMinutesIntervals = [1, 2, 3, 4, 5, 6, 10, 12, 15, 20, 30];

      if(preg_match($anyHourPattern, $schedule) && $currentMinutes == 60)
      {
        $updatePermited = true;
      }

      else if(preg_match($anyHourSpecificMinuteIntervalPattern, $schedule))
      {
        $scheduleParts = preg_split('/\:/', $schedule);
        $minutesInterval = (int) $scheduleParts[1];

        if(in_array($minutesInterval, $allowedMinutesIntervals) && ($currentMinutes % $minutesInterval) == 0)
        {
          $updatePermited = true;
        }
      }

      else if(preg_match($specificHoursPattern, $schedule))
      {
        $specificHoursSchedule = preg_replace('/\[|\]/', '', $schedule);
        $specificHoursSchedule = preg_split('/\|/', $specificHoursSchedule);
        $specificHoursSchedule = array_unique($specificHoursSchedule);

        foreach($specificHoursSchedule as $key => $value)

        $specificHoursSchedule[$key] = (int) $value;

        if(in_array($currentHour, $specificHoursSchedule) && $currentMinutes == 60)

        $updatePermited = true;
      }

      else if(preg_match($specificHoursMinutesIntervalPattern, $schedule))
      {
        $specificHoursMinutesSchedule = preg_split('/\:/', $schedule);

        $specificHoursSchedule = preg_replace('/\[|\]/', '', $specificHoursMinutesSchedule[0]);
        $specificHoursScheduleParts = preg_split('/\|/', $specificHoursSchedule);
        $specificHoursScheduleParts = array_unique($specificHoursScheduleParts);
        $minutesInterval = (int) $specificHoursMinutesSchedule[1];

        foreach($specificHoursScheduleParts as $key => $value)
        {
          $specificHoursScheduleParts[$key] = (int) $value;
        }

        if(in_array($minutesInterval, $allowedMinutesIntervals) && in_array($currentHour, $specificHoursScheduleParts))

        $updatePermited = true;
      }

      else if(preg_match($specificHoursRangesPattern, $schedule))
      {
        $specificHoursRangeSchedule = preg_replace('/\[|\]/', '', $schedule);
        $specificHoursRangeSchedule = preg_split('/\|/', $specificHoursRangeSchedule);
        $specificHoursRangeSchedule = array_unique($specificHoursRangeSchedule);

        foreach($specificHoursRangeSchedule as $specificRange)
        {
          $specificRangeParts = explode('-', $specificRange);

          $lowerBound = (int) $specificRangeParts[0];
          $upperBound = (int) $specificRangeParts[1];

          if($lowerBound <= $currentHour && $currentHour <= $upperBound)
          {
            $updatePermited = true;

            break;
          }
        }
      }

      else if(preg_match($specificHoursRangesMinutesIntervalPattern, $schedule))
      {
        $specificHoursRangesMinutesIntervalSchedule = preg_split('/\:/', $schedule);

        $minutesInterval = (int) $specificHoursRangesMinutesIntervalSchedule[1];

        if(in_array($minutesInterval, $allowedMinutesIntervals) && ($currentMinutes % $minutesInterval) == 0)
        {
          $specificHoursRanges = preg_replace('/\[|\]/', '', $specificHoursRangesMinutesIntervalSchedule[0]);

          $specificHoursRangesParts = preg_split('/\|/', $specificHoursRanges);
          $specificHoursRangesParts = array_unique($specificHoursRangesParts);

          foreach($specificHoursRangesParts as $specificRange)
          {
            $specificRangeParts = explode('-', $specificRange);

            $lowerBound = (int) $specificRangeParts[0];
            $upperBound = (int) $specificRangeParts[1];

            if($lowerBound <= $currentHour && $currentHour <= $upperBound)
            {
              $updatePermited = true;

              break;
            }
          }
        }
      }

      return $updatePermited;
    }
}
