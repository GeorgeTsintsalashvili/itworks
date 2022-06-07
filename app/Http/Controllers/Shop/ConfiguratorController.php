<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;
use PDF as DOMPDF;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\Configurator;

class ConfiguratorController extends Controllers\Controller
{
    public function index()
    {
      BaseModel::collectStatisticalData(Configurator::class);

      $generalData = BaseModel::getGeneralData();

      return View::make('contents.shop.configurator.index', ['generalData' => $generalData]);
    }

    public function generateDocument(Request $request)
    {
      $data['documentHtmlText'] = 'Configuration Is Empty';
      $data['documentName'] = 'itworks';

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['parts' => 'required|string',
                                                  'peripherals' => 'required|string',
                                                  'quantities' => 'required|string']);

      if (!$validator -> fails())
      {
        $configurationParameterValue = $parameters['parts'];
        $peripheralsParameterValue = $parameters['peripherals'];
        $quantitiesValue = $parameters['quantities'];

        $templateName = base_path() . '/resources/views/contents/shop/configurator/document.blade.php';

        if (file_exists($templateName))
        {
          $time = date('d-m-Y H:i:s');

          $quantitiesValueParts = explode(':', $quantitiesValue);
          $configurationParameterValueParts = explode(':', $configurationParameterValue);
          $peripheralsParameterValueParts = explode(':', $peripheralsParameterValue);

          if (count($configurationParameterValueParts) == 9 && count($peripheralsParameterValueParts) == 4 && count($quantitiesValueParts) == 3)
          {
            $stockTypes = \DB::table('stock_types') -> select(['id']) -> where('configuratorPart', 1) -> get();
            $stockTypesIdentifiers = [];

            foreach($stockTypes as $stockType) $stockTypesIdentifiers[] = $stockType -> id;

            if (count($stockTypesIdentifiers) != 0)
            {
              $processorFields = ['title', 'price', 'discount'];
              $motherboardFields = ['title', 'price', 'discount', 'ramSlots'];
              $memoryFields = ['title', 'price', 'discount', 'unitsInGroup', 'quantity'];
              $optionalPartsfields = ['title', 'price', 'discount', 'quantity'];

              // contact information data

              $email = 'არ არის მითითებული';
              $phone = 'არ არის მითითებული';
              $address = 'არ არის მითითებული';
              $schedule = 'არ არის მითითებული';

              // key logic

              $numberOfPartsSelectedByUser = 0;
              $assemblyPrice = 0;
              $overalPrice = 0;
              $overalOldPrice = 0;
              $overalOldPriceVisibility = 'none';
              $memoryUnitsProvidedByUser = 0;

              // parts identifiers

              $memoryId = abs((int) $configurationParameterValueParts[0]);
              $processorId = abs((int) $configurationParameterValueParts[1]);
              $motherboardId = abs((int) $configurationParameterValueParts[2]);
              $videoCardId = abs((int) $configurationParameterValueParts[3]);
              $powerSupplyId = abs((int) $configurationParameterValueParts[4]);
              $processorCoolerId = abs((int) $configurationParameterValueParts[5]);
              $caseId = abs((int) $configurationParameterValueParts[6]);
              $hardDiskDriveId = abs((int) $configurationParameterValueParts[7]);
              $solidStateDiskDriveId = abs((int) $configurationParameterValueParts[8]);

              // peripherals identifiers

              $monitorId = abs((int) $peripheralsParameterValueParts[0]);
              $headphoneId = abs((int) $peripheralsParameterValueParts[1]);
              $keyboardId = abs((int) $peripheralsParameterValueParts[2]);
              $computerMouseId = abs((int) $peripheralsParameterValueParts[3]);

              // system block parts titles

              $processorTitle = 'არ არის არჩეული';
              $motherboardTitle = 'არ არის არჩეული';
              $memoryTitle = 'არ არის არჩეული';
              $videoCardTitle = 'არ არის არჩეული';
              $powerSupplyTitle = 'არ არის არჩეული';
              $processorCoolerTitle = 'არ არის არჩეული';
              $caseTitle = 'არ არის არჩეული';
              $hardDiskDriveTitle = 'არ არის არჩეული';
              $solidStateDriveTitle = 'არ არის არჩეული';

              // peripherals titles

              $monitorTitle = 'არ არის არჩეული';
              $headphoneTitle = 'არ არის არჩეული';
              $keyboardTitle = 'არ არის არჩეული';
              $computerMouseTitle = 'არ არის არჩეული';

              // system block parts new prices

              $processorPrice = 0;
              $motherboardPrice = 0;
              $memoryPrice = 0;
              $videoCardPrice = 0;
              $powerSupplyPrice = 0;
              $processorCoolerPrice = 0;
              $casePrice = 0;
              $hardDiskDrivePrice = 0;
              $solidStateDrivePrice = 0;

              // peripherals new prices

              $monitorPrice = 0;
              $headphonePrice = 0;
              $keyboardPrice = 0;
              $computerMousePrice = 0;

              // system block parts old prices

              $processorOldPrice = 0;
              $motherboardOldPrice = 0;
              $memoryOldPrice = 0;
              $videoCardOldPrice = 0;
              $powerSupplyOldPrice = 0;
              $processorCoolerOldPrice = 0;
              $caseOldPrice = 0;
              $hardDiskDriveOldPrice = 0;
              $solidStateDriveOldPrice = 0;

              // peripherals old prices

              $monitorOldPrice = 0;
              $headphoneOldPrice = 0;
              $keyboardOldPrice = 0;
              $computerMouseOldPrice = 0;

              // parts discounts visibilities

              $processorDiscountVisibility = 'none';
              $motherboardDiscountVisibility = 'none';
              $memoryDiscountVisibility = 'none';
              $videoCardDiscountVisibility = 'none';
              $powerSupplyDiscountVisibility = 'none';
              $processorCoolerDiscountVisibility = 'none';
              $caseDiscountVisibility = 'none';
              $hardDiskDriveDiscountVisibility = 'none';
              $solidStateDriveDiscountVisibility = 'none';

              // peripherals discounts visibilities

              $monitorDiscountVisibility = 'none';
              $headphoneDiscountVisibility = 'none';
              $keyboardDiscountVisibility = 'none';
              $computerMouseDiscountVisibility = 'none';

              // quantities

              list($numOfMemoryModules, $numOfHardDiskDrives, $numOfSolidStateDrives) = array_map(function($value){ return abs((int) $value); }, $quantitiesValueParts);

              // select parts

              $processor = null;
              $motherboard = null;
              $memory = null;
              $videoCard = null;
              $powerSupply = null;
              $processorCooler = null;
              $case = null;
              $hardDiskDrive = null;
              $solidStateDrive = null;

              if ($processorId) $processor = \DB::table('processors') -> select($processorFields) -> where('id', '=', $processorId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($motherboardId) $motherboard = \DB::table('motherboards') -> select($motherboardFields) -> where('id', '=', $motherboardId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($memoryId) $memory = \DB::table('memory_modules') -> select($memoryFields) -> where('id', '=', $memoryId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($videoCardId) $videoCard = \DB::table('video_cards') -> select($optionalPartsfields) -> where('id', '=', $videoCardId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($powerSupplyId) $powerSupply = \DB::table('power_supplies') -> select($optionalPartsfields) -> where('id', '=', $powerSupplyId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($processorCoolerId) $processorCooler = \DB::table('processor_coolers') -> select($optionalPartsfields) -> where('id', '=', $processorCoolerId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($caseId) $case = \DB::table('computer_cases') -> select($optionalPartsfields) -> where('id', '=', $caseId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($hardDiskDriveId) $hardDiskDrive = \DB::table('hard_disk_drives') -> select($optionalPartsfields) -> where('id', '=', $hardDiskDriveId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($solidStateDiskDriveId) $solidStateDrive = \DB::table('solid_state_drives') -> select($optionalPartsfields) -> where('id', '=', $solidStateDiskDriveId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();

              // select peripherals

              $monitor = null;
              $headphone = null;
              $keyboard = null;
              $computerMouse = null;

              if ($monitorId) $monitor = \DB::table('monitors') -> select($optionalPartsfields) -> where('monitors.id', '=', $monitorId) -> where('visibility', 1) -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($headphoneId) $headphone = \DB::table('accessories') -> select($optionalPartsfields) -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId') -> where('accessories.id', '=', $headphoneId) -> where('visibility', 1) -> where('typeKey', '=', 'hdphn') -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($keyboardId) $keyboard = \DB::table('accessories') -> select($optionalPartsfields) -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId') -> where('accessories.id', '=', $keyboardId) -> where('visibility', 1) -> where('typeKey', '=', 'kbrd') -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();
              if ($computerMouseId) $computerMouse = \DB::table('accessories') -> select($optionalPartsfields) -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId') -> where('accessories.id', '=', $computerMouseId) -> where('visibility', 1) -> where('typeKey', '=', 'ms') -> whereIn('stockTypeId', $stockTypesIdentifiers) -> first();

              // sum key data

              $overalPrice = 0;
              $overalOldPrice = 0;

              // check parts

              if (!is_null($processor))
              {
                $processorTitle = $processor -> title;
                $processorPrice = $processor -> price - $processor -> discount;
                $processorOldPrice = $processor -> price;
                $processorDiscountVisibility = $processor -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $processorPrice;
                $overalOldPrice += $processorOldPrice;
              }

              if (!is_null($motherboard))
              {
                $motherboardTitle = $motherboard -> title;
                $motherboardPrice = $motherboard -> price - $motherboard -> discount;
                $motherboardOldPrice = $motherboard -> price;
                $motherboardDiscountVisibility = $motherboard -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $motherboardPrice;
                $overalOldPrice += $motherboardOldPrice;
              }

              if (!is_null($memory) && $numOfMemoryModules)
              {
                if ($numOfMemoryModules <= 20) // $memory -> quantity
                {
                  $memoryUnitsProvidedByUser = $memory -> unitsInGroup * $numOfMemoryModules;

                  if (!is_null($motherboard))
                  {
                    $memoryTitle = $memory -> title;
                    $memoryPrice = $numOfMemoryModules * ($memory -> price - $memory -> discount);
                    $memoryOldPrice = $numOfMemoryModules * $memory -> price;
                    $memoryDiscountVisibility = $memory -> discount == 0 ? 'none' : 'inline';

                    $numberOfPartsSelectedByUser += 1;
                    $overalPrice += $memoryPrice;
                    $overalOldPrice += $memoryOldPrice;
                  }
                }
              }

              if (!is_null($videoCard))
              {
                $videoCardTitle = $videoCard -> title;
                $videoCardPrice = $videoCard -> price - $videoCard -> discount;
                $videoCardOldPrice = $videoCard -> price;
                $videoCardDiscountVisibility = $videoCard -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $videoCardPrice;
                $overalOldPrice += $videoCardOldPrice;
              }

              if (!is_null($powerSupply))
              {
                $powerSupplyTitle = $powerSupply -> title;
                $powerSupplyPrice = $powerSupply -> price - $powerSupply -> discount;
                $powerSupplyOldPrice = $powerSupply -> price;
                $powerSupplyDiscountVisibility = $powerSupply -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $powerSupplyPrice;
                $overalOldPrice += $powerSupplyOldPrice;
              }

              if (!is_null($processorCooler))
              {
                $processorCoolerTitle = $processorCooler -> title;
                $processorCoolerPrice = $processorCooler -> price - $processorCooler -> discount;
                $processorCoolerOldPrice = $processorCooler -> price;
                $processorCoolerDiscountVisibility = $processorCooler -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $processorCoolerPrice;
                $overalOldPrice += $processorCoolerOldPrice;
              }

              if (!is_null($case))
              {
                $caseTitle = $case -> title;
                $casePrice = $case -> price - $case -> discount;
                $caseOldPrice = $case -> price;
                $caseDiscountVisibility = $case -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $casePrice;
                $overalOldPrice += $caseOldPrice;
              }

              if (!is_null($hardDiskDrive))
              {
                if ($hardDiskDrive && $numOfHardDiskDrives <= 20)
                {
                  $hardDiskDriveTitle = $hardDiskDrive -> title;
                  $hardDiskDrivePrice = $numOfHardDiskDrives * ($hardDiskDrive -> price - $hardDiskDrive -> discount);
                  $hardDiskDriveOldPrice = $numOfHardDiskDrives * $hardDiskDrive -> price;
                  $hardDiskDriveDiscountVisibility = $hardDiskDrive -> discount == 0 ? 'none' : 'inline';

                  $numberOfPartsSelectedByUser += 1;
                  $overalPrice += $hardDiskDrivePrice;
                  $overalOldPrice += $hardDiskDriveOldPrice;
                }
              }

              if (!is_null($solidStateDrive))
              {
                $solidStateDriveTitle = $solidStateDrive -> title;
                $solidStateDrivePrice = $numOfSolidStateDrives * ($solidStateDrive -> price - $solidStateDrive -> discount);
                $solidStateDriveOldPrice = $numOfSolidStateDrives * $solidStateDrive -> price;
                $solidStateDriveDiscountVisibility = $solidStateDrive -> discount == 0 ? 'none' : 'inline';

                $numberOfPartsSelectedByUser += 1;
                $overalPrice += $solidStateDrivePrice;
                $overalOldPrice += $solidStateDriveOldPrice;
              }

              // peripherals display

              $monitorDisplay = 'none';
              $headphoneDisplay = 'none';
              $keyboardDisplay = 'none';
              $computerMouseDisplay = 'none';

              // check peripherals

              if (!is_null($monitor))
              {
                $monitorTitle = $monitor -> title;
                $monitorPrice = $monitor -> price - $monitor -> discount;
                $monitorOldPrice = $monitor -> price;
                $monitorDiscountVisibility = $monitor -> discount == 0 ? 'none' : 'inline';

                $monitorDisplay = 'block';
                $overalPrice += $monitorPrice;
                $overalOldPrice += $monitorOldPrice;
              }

              if (!is_null($headphone))
              {
                $headphoneTitle = $headphone -> title;
                $headphonePrice = $headphone -> price - $headphone -> discount;
                $headphoneOldPrice = $headphone -> price;
                $headphoneDiscountVisibility = $headphone -> discount == 0 ? 'none' : 'inline';

                $headphoneDisplay = 'block';
                $overalPrice += $headphonePrice;
                $overalOldPrice += $headphoneOldPrice;
              }

              if (!is_null($keyboard))
              {
                  $keyboardTitle = $keyboard -> title;
                  $keyboardPrice = $keyboard -> price - $keyboard -> discount;
                  $keyboardOldPrice = $keyboard -> price;
                  $keyboardDiscountVisibility = $keyboard -> discount == 0 ? 'none' : 'inline';

                  $keyboardDisplay = 'block';
                  $overalPrice += $keyboardPrice;
                  $overalOldPrice += $keyboardOldPrice;
              }

              if (!is_null($computerMouse))
              {
                $computerMouseTitle = $computerMouse -> title;
                $computerMousePrice = $computerMouse -> price - $computerMouse -> discount;
                $computerMouseOldPrice = $computerMouse -> price;
                $computerMouseDiscountVisibility = $computerMouse -> discount == 0 ? 'none' : 'inline';

                $computerMouseDisplay = 'block';
                $overalPrice += $computerMousePrice;
                $overalOldPrice += $computerMouseOldPrice;
              }

              // determine key moments

              $assemblyPrice = $numberOfPartsSelectedByUser && $numberOfPartsSelectedByUser < 9 ? 50 : 0;
              $overalOldPriceVisibility = ($overalOldPrice - $overalPrice - $assemblyPrice) <= 0 ? 'none' : 'inline';

              // pdf document generation logic

              $htmlText = file_get_contents($templateName);
              $htmlText = str_replace('{address}', \URL::to('/'), $htmlText);
              $htmlText = str_replace('{time}', $time, $htmlText);

              // replace titles

              $titlesPlaceholders = ['{processorTitle}', '{motherboardTitle}', '{memoryTitle}', '{videoCardTitle}', '{powerSupplyTitle}', '{hardDiskDriveTitle}', '{solidStateDriveTitle}', '{processorCoolerTitle}', '{caseTitle}', '{memories}', '{hardDiskDrives}', '{solidStateDrives}', '{monitorTitle}', '{headphoneTitle}', '{keyboardTitle}', '{computerMouseTitle}'];
              $titlesToInsert = [$processorTitle, $motherboardTitle, $memoryTitle, $videoCardTitle, $powerSupplyTitle, $hardDiskDriveTitle, $solidStateDriveTitle, $processorCoolerTitle, $caseTitle, $memoryUnitsProvidedByUser, $numOfHardDiskDrives, $numOfSolidStateDrives, $monitorTitle, $headphoneTitle, $keyboardTitle, $computerMouseTitle];

              $htmlText = str_replace($titlesPlaceholders, $titlesToInsert, $htmlText);

              // replace prices

              $pricesPlaceholders = ['{processorPrice}', '{motherboardPrice}', '{memoryPrice}', '{videoCardPrice}', '{powerSupplyPrice}', '{hardDiskDrivePrice}', '{solidStateDrivePrice}', '{processorCoolerPrice}', '{casePrice}', '{monitorPrice}', '{headphonePrice}', '{keyboardPrice}', '{computerMousePrice}'];
              $pricesToInsert = [$processorPrice, $motherboardPrice, $memoryPrice, $videoCardPrice, $powerSupplyPrice, $hardDiskDrivePrice, $solidStateDrivePrice, $processorCoolerPrice, $casePrice, $monitorPrice, $headphonePrice, $keyboardPrice, $computerMousePrice];

              $htmlText = str_replace($pricesPlaceholders, $pricesToInsert, $htmlText);

              // replace old prices

              $oldPricesPlaceholders = ['{processorOldPrice}', '{motherboardOldPrice}', '{memoryOldPrice}', '{videoCardOldPrice}', '{powerSupplyOldPrice}', '{hardDiskDriveOldPrice}', '{solidStateDriveOldPrice}', '{processorCoolerOldPrice}', '{caseOldPrice}', '{monitorOldPrice}', '{headphoneOldPrice}', '{keyboardOldPrice}', '{computerMouseOldPrice}'];
              $oldPricesToInsert = [$processorOldPrice, $motherboardOldPrice, $memoryOldPrice, $videoCardOldPrice, $powerSupplyOldPrice, $hardDiskDriveOldPrice, $solidStateDriveOldPrice, $processorCoolerOldPrice, $caseOldPrice, $monitorOldPrice, $headphoneOldPrice, $keyboardOldPrice, $computerMouseOldPrice];

              $htmlText = str_replace($oldPricesPlaceholders, $oldPricesToInsert, $htmlText);

              // replace key prices

              $overalPrice = $overalPrice + $assemblyPrice;

              $keyPricesPlaceholders = ['{configurationPrice}', '{oldPrice}', '{assemblyPrice}', '{overalOldPriceVisibility}'];
              $keyPricesToInsert = [$overalPrice, $overalOldPrice, $assemblyPrice, $overalOldPriceVisibility];

              $htmlText = str_replace($keyPricesPlaceholders, $keyPricesToInsert, $htmlText);

              // replace discount visibilities

              $discountVisibilityPlaceholders = ['{processorDiscountVisibility}', '{motherboardDiscountVisibility}', '{memoryDiscountVisibility}', '{videoCardDiscountVisibility}', '{powerSupplyDiscountVisibility}', '{processorCoolerDiscountVisibility}', '{caseDiscountVisibility}', '{hardDiskDriveDiscountVisibility}', '{solidStateDriveDiscountVisibility}', '{monitorDiscountVisibility}', '{headphoneDiscountVisibility}', '{keyboardDiscountVisibility}', '{computerMouseDiscountVisibility}'];

              $discountVisibilitiesToInsert = [$processorDiscountVisibility, $motherboardDiscountVisibility, $memoryDiscountVisibility, $videoCardDiscountVisibility, $powerSupplyDiscountVisibility, $processorCoolerDiscountVisibility, $caseDiscountVisibility, $hardDiskDriveDiscountVisibility, $solidStateDriveDiscountVisibility, $monitorDiscountVisibility, $headphoneDiscountVisibility, $keyboardDiscountVisibility, $computerMouseDiscountVisibility];

              $htmlText = str_replace($discountVisibilityPlaceholders, $discountVisibilitiesToInsert, $htmlText);

              // replace display types of peripherals

              $paripheralsDisplayTypesPlaceholders = ['{monitorDisplay}', '{headphoneDisplay}', '{keyboardDisplay}', '{computerMouseDisplay}'];

              $paripheralsDisplayTypesToInsert = [$monitorDisplay, $headphoneDisplay, $keyboardDisplay, $computerMouseDisplay];

              $htmlText = str_replace($paripheralsDisplayTypesPlaceholders, $paripheralsDisplayTypesToInsert, $htmlText);

              // check contact information

              $contactInformationFieldsToSelect = ['email', 'phone', 'address', 'schedule'];
              $contactInformation = \DB::table('contacts') -> select($contactInformationFieldsToSelect) -> first();

              if (!is_null($contactInformation))
              {
                $email = $contactInformation -> email;
                $phone = $contactInformation -> phone;
                $companyAddress = $contactInformation -> address;
                $schedule = $contactInformation -> schedule;
              }

              $contactInformationPlaceholders = ['{email}', '{phone}', '{companyAddress}', '{schedule}'];
              $contactInformationToInsert = [$email, $phone, $companyAddress, $schedule];

              $htmlText = str_replace($contactInformationPlaceholders, $contactInformationToInsert, $htmlText);

              $data['documentHtmlText'] = $htmlText;
              $data['documentName'] = "itworks-" . substr(md5(mt_rand()), 0, 8) . ".pdf";
            }
          }
        }
      }

      $pdf = DOMPDF::loadHTML($data['documentHtmlText']) -> setPaper('a4', 'landscape');

      return $pdf -> stream($data['documentName']);
    }

    // get computer parts logic

    public function getProcessors(Request $request)
    {
      $data['series'] = \DB::table('cpu_series') -> get();
      $data['filter-parameter'] = 0;

      $fields = ['processors.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'clockSpeed', 'cores', 'socketTitle', 'size'];

      $query = \DB::table('processors') -> select($fields)
                                        -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processors.socketId')
                                        -> join('conditions', 'conditions.id', '=', 'processors.conditionId')
                                        -> join('stock_types', 'stock_types.id', '=', 'processors.stockTypeId')
                                        -> join('cpu_technology_processes', 'cpu_technology_processes.id', '=', 'processors.technologyProcessId')
                                        -> join('processors_and_chipsets', 'processors.id', '=', 'processors_and_chipsets.processorId')
                                        -> where('visibility', 1)
                                        -> where('processors.configuratorPart', '=', 1)
                                        -> where('cpu_sockets.configuratorPart', '=', 1)
                                        -> where('stock_types.configuratorPart', '=', 1);

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['filter-parameter' => 'required']);

      if (!$validator -> fails())
      {
        $filterParameterValue = abs((int) $parameters['filter-parameter']);

        if ($filterParameterValue != 0)
        {
          $query = $query -> where('seriesId', '=', $filterParameterValue);

          $data['filter-parameter'] = $filterParameterValue;
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> groupBy('processorId') -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getProcessors', ['data' => $data]);
    }

    public function getMotherboards(Request $request)
    {
      $solidStateDrivesFormFactors = \DB::table('solid_state_drives_form_factors') -> select(['id']) -> get();
      $memoryTypes = \DB::table('memory_modules_types') -> select(['id']) -> get();
      $formFactors = \DB::table('case_form_factors') -> select(['id']) -> get();
      $chipsets = \DB::table('chipsets') -> select(['id']) -> get();

      $data['manufacturers'] = \DB::table('motherboards_manufacturers') -> get();
      $data['filter-parameter'] = 0;

      $fields = ['motherboards.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'socketTitle', 'manufacturerTitle', 'formFactorTitle', 'typeTitle', 'chipsetTitle', 'maxMemory', 'ramSlots'];

      $query = \DB::table('motherboards') -> select($fields)
                                          -> join('conditions', 'conditions.id', '=', 'motherboards.conditionId')
                                          -> join('stock_types', 'stock_types.id', '=', 'motherboards.stockTypeId')
                                          -> join('cpu_sockets', 'cpu_sockets.id', '=', 'motherboards.socketId')
                                          -> join('chipsets', 'chipsets.id', '=', 'motherboards.chipsetId')
                                          -> join('memory_modules_types', 'memory_modules_types.id', '=', 'motherboards.memoryTypeId')
                                          -> join('case_form_factors', 'case_form_factors.id', '=', 'motherboards.formFactorId')
                                          -> join('solid_state_drives_and_motherboards', 'motherboards.id', '=', 'solid_state_drives_and_motherboards.motherboardId')
                                          -> join('motherboards_manufacturers', 'motherboards_manufacturers.id', '=', 'motherboards.manufacturerId')
                                          -> where('visibility', 1)
                                          -> where('motherboards.configuratorPart', '=', 1)
                                          -> where('cpu_sockets.configuratorPart', '=', 1)
                                          -> where('stock_types.configuratorPart', '=', 1);

      // request data validation

      $parameters = $request -> all();

      $filterValidator = \Validator::make($parameters, ['filter-parameter' => 'required']);
      $chipsetValidator = \Validator::make($parameters, ['chipset' => 'required']);

      if (!$filterValidator -> fails())
      {
        $manufacturerId = abs((int) $parameters['filter-parameter']);

        if ($manufacturerId != 0)
        {
          $query = $query -> where('manufacturerId', '=', $manufacturerId);

          $data['filter-parameter'] = $manufacturerId;
        }
      }

      if (!$chipsetValidator -> fails())
      {
        $chipset = $parameters['chipset'];

        if ($parameters['chipset'] !== '0')
        {
          $chipsetParts = array_map('intval', explode(':', $parameters['chipset']));

          if (count($chipsetParts) != 0 && !in_array(0, $chipsetParts))
          {
            $realChipsets = \DB::table('chipsets') -> select(['id']) -> get();

            $realChipsetsIdentifiers = [];

            foreach($realChipsets as $chipset) $realChipsetsIdentifiers[] = $chipset -> id;

            if (count($realChipsetsIdentifiers) != 0)
            {
              $chipsetParts = array_unique($chipsetParts);

              if (array_intersect($chipsetParts, $realChipsetsIdentifiers) == $chipsetParts)

              $query = $query -> whereIn('chipsetId', $chipsetParts);
            }
          }
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> groupBy('motherboardId') -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getMotherboards', ['data' => $data]);
    }

    public function getMemories(Request $request)
    {
      $data['partsExist'] = false;
      $data['filter-parameter'] = 0;

      $data['maxMemory'] = 0;
      $data['memorySlots'] = 0;

      $field = ['function' => 'distinct', 'argument' => 'capacity', 'alias' => 'capacity'];
      $data['capacities'] = \DB::table('memory_modules') -> select(\DB::raw('DISTINCT(`capacity`) AS `capacity`'))
                                                         -> where('visibility', 1)
                                                         -> where('configuratorPart', '=', 1)
                                                         -> orderBy('capacity', 'asc')
                                                         -> get();

      $fields = ['memory_modules.id', 'title', 'mainImage', 'price', 'discount', 'conditionTitle', 'quantity', 'stockTitle', 'typeTitle', 'frequency', 'capacity', 'unitsInGroup'];

      $query = \DB::table('memory_modules') -> select($fields)
                                            -> join('conditions', 'conditions.id', '=', 'memory_modules.conditionId')
                                            -> join('stock_types', 'stock_types.id', '=', 'memory_modules.stockTypeId')
                                            -> join('memory_modules_types', 'memory_modules_types.id', '=', 'memory_modules.memoryModuleTypeId')
                                            -> where('memory_modules.configuratorPart', 1)
                                            -> where('stock_types.configuratorPart', 1)
                                            -> where('visibility', 1);

      // request data validation

      $parameters = $request -> all();

      $filterValidator = \Validator::make($parameters, ['filter-parameter' => 'required|string']);
      $memoryValidator = \Validator::make($parameters, ['memory-type' => 'required|string',
                                                        'max-memory' => 'required|string',
                                                        'memory-slots' => 'required|string']);

      if (!$filterValidator -> fails())
      {
        $capacity = abs((int) $parameters['filter-parameter']);

        if ($capacity != 0)
        {
          $query = $query -> where('capacity', '=', $capacity);

          $data['filter-parameter'] = $capacity;
        }
      }

      if (!$memoryValidator -> fails())
      {
        $memoryType = abs((int) $parameters['memory-type']);
        $data['maxMemory'] = abs((int) $parameters['max-memory']);
        $data['memorySlots'] = abs((int) $parameters['memory-slots']);

        if ($memoryType != 0)
        {
          $realMemoryTypes = \DB::table('memory_modules_types') -> select(['id']) -> get();

          $realMemoryTypesIdentifiers = [];

          foreach($realMemoryTypes as $type) $realMemoryTypesIdentifiers[] = $type -> id;

          if (in_array($memoryType, $realMemoryTypesIdentifiers))

          $query = $query -> where('memoryModuleTypeId', '=', $memoryType);
        }

        if ($data['maxMemory'] != 0) $query = $query -> where('capacity', '<=', $data['maxMemory']) -> where('unitsInGroup', '<=', $data['memorySlots']);
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('capacity', 'desc') -> get();

      return View::make('contents.shop.configurator.getMemories', ['data' => $data]);
    }

    public function getProcessorCoolers(Request $request)
    {
      $data['partsExist'] = false;

      $fields = '`processor_coolers`.`id`,`title`,`mainImage`,`price`,`discount`,`quantity`,`conditionTitle`,`stockTitle`,`size`,GROUP_CONCAT(`socketTitle` SEPARATOR ", ") AS `socketTitle`';

      $query = \DB::table('processor_coolers') -> selectRaw($fields)
                                               -> join('conditions', 'conditions.id', '=', 'processor_coolers.conditionId')
                                               -> join('stock_types', 'stock_types.id', '=', 'processor_coolers.stockTypeId')
                                               -> join('processor_coolers_and_sockets', 'processor_coolers.id', '=', 'processor_coolers_and_sockets.processorCoolerId')
                                               -> join('cpu_sockets', 'cpu_sockets.id', '=', 'processor_coolers_and_sockets.socketId')
                                               -> where('visibility', 1)
                                               -> where('processor_coolers.configuratorPart', '=', 1)
                                               -> where('cpu_sockets.configuratorPart', '=', 1);

      // request data validation

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['cpu-socket' => 'required']);

      if (!$validator -> fails())
      {
        $cpuSocketId = abs((int) $parameters['cpu-socket']);

        if ($cpuSocketId != 0)
        {
          $realSockets = \DB::table('cpu_sockets') -> select(['id']) -> where('configuratorPart', '=', 1) -> get();

          $realSocketsIdentifiers = [];

          foreach($realSockets as $socket) $realSocketsIdentifiers[] = $socket -> id;

          if (in_array($cpuSocketId, $realSocketsIdentifiers)) $query = $query -> where('socketId', '=', $cpuSocketId);
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> groupBy('processorCoolerId') -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getProcessorCoolers', ['data' => $data]);
    }

    public function getCases(Request $request)
    {
      $data['partsExist'] = false;

      $fields = ['computer_cases.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle'];

      $query = \DB::table('computer_cases') -> select($fields)
                                            -> join('conditions', 'conditions.id', '=', 'computer_cases.conditionId')
                                            -> join('stock_types', 'stock_types.id', '=', 'computer_cases.stockTypeId')
                                            -> join('cases_and_form_factors', 'computer_cases.id', '=', 'cases_and_form_factors.caseId')
                                            -> where('visibility', 1)
                                            -> where('computer_cases.configuratorPart', '=', 1);

      // request data validation

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['form-factor' => 'required']);

      if (!$validator -> fails())
      {
        $formFactor = abs((int) $parameters['form-factor']);

        if ($formFactor != 0)
        {
          $realFormFactors = \DB::table('case_form_factors') -> select(['id']) -> get();

          $realFormFactorsIdentifiers = [];

          foreach($realFormFactors as $formFactorType) $realFormFactorsIdentifiers[] = $formFactorType -> id;

          if (in_array($formFactor, $realFormFactorsIdentifiers))
          {
            $query = $query -> where('formFactorId', '=', $formFactor);
          }
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> groupBy('computer_cases.id') -> orderBy('price', 'desc') -> get();

      if ($data['partsExist'])
      {
        foreach($data['products'] as $key => $product)
        {
          $supportedFormFactors = \DB::table('cases_and_form_factors') -> select(['formFactorTitle'])
                                                                       -> join('case_form_factors', 'case_form_factors.id', '=', 'cases_and_form_factors.formFactorId')
                                                                       -> where('caseId', '=', $product -> id)
                                                                       -> get();
          $suppportedFormFactorsFullTitle = '';
          $supportedFormFactorsTitles = [];

          foreach($supportedFormFactors as $supportedFormFactor) $supportedFormFactorsTitles[] = $supportedFormFactor -> formFactorTitle;

          if (count($supportedFormFactorsTitles) != 0) $suppportedFormFactorsFullTitle = implode(', ', $supportedFormFactorsTitles);

          $data['products'][$key] -> formFactorTitle = $suppportedFormFactorsFullTitle;
        }
      }

      return View::make('contents.shop.configurator.getCases', ['data' => $data]);
    }

    public function getPowerSupplies(Request $request)
    {
      $data['partsExist'] = false;

      $fields = ['power_supplies.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'power'];

      $query = \DB::table('power_supplies') -> select($fields)
                                            -> join('conditions', 'conditions.id', '=', 'power_supplies.conditionId')
                                            -> join('stock_types', 'stock_types.id', '=', 'power_supplies.stockTypeId')
                                            -> where('visibility', 1)
                                            -> where('power_supplies.configuratorPart', '=', 1);

      // request data validation

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['required-power' => 'required']);

      if (!$validator -> fails())
      {
        $requiredPower = abs((int) $parameters['required-power']);

        if ($requiredPower != 0) $query = $query -> where('power', '>=', $requiredPower);
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getPowerSupplies', ['data' => $data]);
    }

    public function getVideoCards(Request $request)
    {
      $data['partsExist'] = false;
      $data['filter-parameter'] = 0;

      $data['memories'] = \DB::table('video_cards') -> selectRaw('DISTINCT(`memory`) AS `memory`')
                                                    -> where('visibility', 1)
                                                    -> where('configuratorPart', '=', 1)
                                                    -> orderBy('memory', 'asc')
                                                    -> get();

      $fields = ['video_cards.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'memory', 'memoryBandwidth', 'typeTitle', 'gpuTitle', 'minPower'];

      $query = \DB::table('video_cards') -> select($fields)
                                         -> join('conditions', 'conditions.id', '=', 'video_cards.conditionId')
                                         -> join('stock_types', 'stock_types.id', '=', 'video_cards.stockTypeId')
                                         -> join('video_cards_memory_types', 'video_cards_memory_types.id', '=', 'video_cards.memoryTypeId')
                                         -> join('gpu_manufacturers', 'gpu_manufacturers.id', '=', 'video_cards.gpuManufacturerId')
                                         -> where('visibility', 1)
                                         -> where('video_cards.configuratorPart', '=', 1);

      // request data validation logic

      $parameters = $request -> all();

      $filterValidator = \Validator::make($parameters, ['filter-parameter' => 'required']);
      $powerValidator = \Validator::make($parameters, ['required-power' => 'required']);

      if (!$filterValidator -> fails())
      {
        $filterParameterValue = abs((int) $parameters['filter-parameter']);

        if ($filterParameterValue != 0)
        {
          $query = $query -> where('memory', '=', $filterParameterValue);

          $data['filter-parameter'] = $filterParameterValue;
        }
      }

      if (!$powerValidator -> fails())
      {
        $requiredPower = abs((int) $parameters['required-power']);

        if ($requiredPower != 0) $query = $query -> where('minPower', '<=', $requiredPower);
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getVideoCards', ['data' => $data]);
    }

    public function getHardDiskDrives(Request $request)
    {
      $data['partsExist'] = false;
      $data['filter-parameter'] = 0;

      $data['capacities'] = \DB::table('hard_disk_drives') -> selectRaw('DISTINCT(`capacity`) AS `capacity`')
                                                           -> where('visibility', 1)
                                                           -> where('configuratorPart', '=', 1)
                                                           -> orderBy('capacity', 'asc')
                                                           -> get();

      $fields = ['hard_disk_drives.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'capacity', 'rpm', 'formFactorTitle'];

      $query = \DB::table('hard_disk_drives') -> select($fields)
                                              -> join('conditions', 'conditions.id', '=', 'hard_disk_drives.conditionId')
                                              -> join('stock_types', 'stock_types.id', '=', 'hard_disk_drives.stockTypeId')
                                              -> join('hard_disk_drives_form_factors', 'hard_disk_drives_form_factors.id', '=', 'hard_disk_drives.formFactorId')
                                              -> where('visibility', 1)
                                              -> where('hard_disk_drives.configuratorPart', '=', 1);

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['filter-parameter' => 'required']);

      if (!$validator -> fails())
      {
        $filterParameterValue = abs((int) $parameters['filter-parameter']);

        if ($filterParameterValue != 0)
        {
          $query = $query -> where('capacity', '=', $filterParameterValue);

          $data['filter-parameter'] = $filterParameterValue;
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getHardDiskDrives', ['data' => $data]);
    }

    public function getSolidStateDrives(Request $request)
    {
      $data['partsExist'] = false;
      $data['filter-parameter'] = 0;

      $data['capacities'] = \DB::table('solid_state_drives') -> selectRaw('DISTINCT(`capacity`) AS `capacity`')
                                                             -> where('visibility', 1)
                                                             -> where('configuratorPart', '=', 1)
                                                             -> orderBy('capacity', 'asc')
                                                             -> get();

      $fields = ['solid_state_drives.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'capacity', 'formFactorTitle', 'onboard'];

      $query = \DB::table('solid_state_drives') -> select($fields)
                                                -> join('conditions', 'conditions.id', '=', 'solid_state_drives.conditionId')
                                                -> join('stock_types', 'stock_types.id', '=', 'solid_state_drives.stockTypeId')
                                                -> join('solid_state_drives_form_factors', 'solid_state_drives_form_factors.id', '=', 'solid_state_drives.formFactorId')
                                                -> where('visibility', 1)
                                                -> where('solid_state_drives.configuratorPart', '=', 1);

      // request data validation logic

      $parameters = $request -> all();

      $filterValidator = \Validator::make($parameters, ['filter-parameter' => 'required']);

      $typeValidator = \Validator::make($parameters, ['ssd-type' => 'required']);

      if (!$filterValidator -> fails())
      {
        $filterParameterValue = abs((int) $parameters['filter-parameter']);

        if ($filterParameterValue != 0)
        {
          $query = $query -> where('capacity', '=', $filterParameterValue);

          $data['filter-parameter'] = $filterParameterValue;
        }
      }

      if (!$typeValidator -> fails())
      {
        $ssdType = $parameters['ssd-type'];

        if ($parameters['ssd-type'] !== "0")
        {
          $ssdTypesParts = array_map('intval', explode(':', $ssdType));

          if (count($ssdTypesParts) != 0 && !in_array(0, $ssdTypesParts))
          {
            $realSsdTypes = \DB::table('solid_state_drives_form_factors') -> select(['id']) -> get();

            $realSsdTypesIdentifiers = [];

            foreach($realSsdTypes as $ssdType) $realSsdTypesIdentifiers[] = $ssdType -> id;

            $ssdTypesParts = array_unique($ssdTypesParts);

            if (array_intersect($ssdTypesParts, $realSsdTypesIdentifiers) == $ssdTypesParts)

            $query = $query -> whereIn('formFactorId', $ssdTypesParts);
          }
        }
      }

      $data['partsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getSolidStateDrives', ['data' => $data]);
    }

    public function getMonitors(Request $request)
    {
      $data['filter-parameter'] = 0;

      $fields = ['monitors.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle', 'manufacturerTitle'];

      $query = \DB::table('monitors') -> select($fields)
                                      -> join('conditions', 'conditions.id', '=', 'monitors.conditionId')
                                      -> join('stock_types', 'stock_types.id', '=', 'monitors.stockTypeId')
                                      -> join('monitors_manufacturers', 'monitors_manufacturers.id', '=', 'monitors.monitorManufacturerId')
                                      -> where('visibility', 1);

      // request data validation

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['filter-parameter' => 'required']);

      if (!$validator -> fails())
      {
        $manufacturerId = abs((int) $parameters['filter-parameter']);

        if ($manufacturerId)
        {
          $data['filter-parameter'] = $manufacturerId;

          $query = $query -> where('monitorManufacturerId', $manufacturerId);
        }
      }

      $data['manufacturers'] = \DB::table('monitors_manufacturers') -> get();
      $data['productsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getMonitors', ['data' => $data]);
    }

    public function getHeadphones()
    {
      $fields = ['accessories.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle'];

      $query = \DB::table('accessories') -> select($fields)
                                         -> join('conditions', 'conditions.id', '=', 'accessories.conditionId')
                                         -> join('stock_types', 'stock_types.id', '=', 'accessories.stockTypeId')
                                         -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId')
                                         -> where('typeKey', 'hdphn')
                                         -> where('visibility', 1);

      $data['productsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getHeadphones', ['data' => $data]);
    }

    public function getKeyboards()
    {
      $fields = ['accessories.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle'];

      $query = \DB::table('accessories') -> select($fields)
                                         -> join('conditions', 'conditions.id', '=', 'accessories.conditionId')
                                         -> join('stock_types', 'stock_types.id', '=', 'accessories.stockTypeId')
                                         -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId')
                                         -> where('typeKey', 'kbrd')
                                         -> where('visibility', 1);

      $data['productsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getKeyboards', ['data' => $data]);
    }

    public function getComputerMice()
    {
      $fields = ['accessories.id', 'title', 'mainImage', 'price', 'discount', 'quantity', 'conditionTitle', 'stockTitle'];

      $query = \DB::table('accessories') -> select($fields)
                                         -> join('conditions', 'conditions.id', '=', 'accessories.conditionId')
                                         -> join('stock_types', 'stock_types.id', '=', 'accessories.stockTypeId')
                                         -> join('accessories_types', 'accessories_types.id', '=', 'accessories.accessoryTypeId')
                                         -> where('typeKey', 'ms')
                                         -> where('visibility', 1);

      $data['productsExist'] = $query -> count() != 0;
      $data['products'] = $query -> orderBy('price', 'desc') -> get();

      return View::make('contents.shop.configurator.getComputerMice', ['data' => $data]);
    }

    // select computer part logic

    public function selectProcessor(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // compatibility data

      $data['chipset'] = null;
      $data['socket'] = null;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'id', 'socketId'];

           $computerPart = \DB::table('processors') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
             $data['socket'] = $computerPart -> socketId;

             $supportedChipsets = \DB::table('processors_and_chipsets') -> select(['chipsetId']) -> where('processorId', '=', $partId) -> get();
             $chipsetsIdentifiers = [];

             foreach($supportedChipsets as $chipset) $chipsetsIdentifiers[] = $chipset -> chipsetId;

             $data['chipset'] = implode(":", $chipsetsIdentifiers);
           }
         }
      }

      return $data;
    }

    public function selectMotherboard(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // compatibility data

      $data['memory-type'] = null;
      $data['max-memory'] = null;
      $data['memory-slots'] = null;
      $data['form-factor'] = null;
      $data['ssd-type'] = null;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['motherboards.id', 'price', 'discount', 'formFactorId', 'maxMemory', 'ramSlots', 'memoryTypeId'];

           $computerPart = \DB::table('motherboards') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
             $data['form-factor'] = $computerPart -> formFactorId;
             $data['memory-slots'] = $computerPart -> ramSlots;
             $data['max-memory'] = $computerPart -> maxMemory;
             $data['memory-type'] = $computerPart -> memoryTypeId;

             $supportedSolidStateDrivesTypes = \DB::table('solid_state_drives_and_motherboards') -> select(['solidStateDriveTypeId']) -> where('motherboardId', '=', $partId) -> get();
             $solidStateDrivesTypesIdentifiers = [];

             foreach($supportedSolidStateDrivesTypes as $ssdType) $solidStateDrivesTypesIdentifiers[] = $ssdType -> solidStateDriveTypeId;

             $data['ssd-type'] = implode(":", $solidStateDrivesTypesIdentifiers);
           }
         }
      }

      return $data;
    }

    public function selectMemory(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required',
                                                  'quantity' => 'required']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);
         $quantity = abs((int) $parameters['quantity']);

         if ($partId && $quantity && $quantity <= 20)
         {
           $fieldsToSelect = ['price', 'discount', 'id'];

           $computerPart = \DB::table('memory_modules') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price * $quantity;
             $data['discount'] = $computerPart -> discount * $quantity;
           }
         }
      }

      return $data;
    }

    public function selectProcessorCooler(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'id'];

           $computerPart = \DB::table('processor_coolers') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }

    public function selectCase(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'id'];

           $computerPart = \DB::table('computer_cases') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }

    public function selectPowerSupply(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // compatibility data

      $data['required-power'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'id', 'power'];

           $computerPart = \DB::table('power_supplies') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
             $data['required-power'] = $computerPart -> power;
           }
         }
      }

      return $data;
    }

    public function selectVideoCard(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // compatibility data

      $data['required-power'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'id', 'minPower'];

           $computerPart = \DB::table('video_cards') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
             $data['required-power'] = $computerPart -> minPower;
           }
         }
      }

      return $data;
    }

    public function selectHardDiskDrive(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string',
                                                  'quantity' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);
         $quantity = abs((int) $parameters['quantity']);

         if ($partId && $quantity && $quantity <= 20)
         {
           $fieldsToSelect = ['price', 'discount', 'id', 'quantity'];

           $computerPart = \DB::table('hard_disk_drives') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price * $quantity;
             $data['discount'] = $computerPart -> discount * $quantity;
           }
         }
      }

      return $data;
    }

    public function selectSolidStateDrive(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string',
                                                  'quantity' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);
         $quantity  = abs((int) $parameters['quantity']);

         if ($partId != 0)
         {
           $fieldsToSelect = ['price', 'discount', 'quantity', 'id'];

           $computerPart = \DB::table('solid_state_drives') -> select($fieldsToSelect) -> where('id', '=', $partId) -> where('visibility', 1) -> where('configuratorPart', '=', 1) -> first();

           if (!is_null($computerPart) && $quantity <= 20)
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price * $quantity;
             $data['discount'] = $computerPart -> discount * $quantity;
           }
         }
      }

      return $data;
    }

    public function selectMonitor(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId)
         {
           $fieldsToSelect = ['monitors.id', 'price', 'discount'];

           $computerPart = \DB::table('monitors') -> select($fieldsToSelect)
                                                     -> where('monitors.id', '=', $partId)
                                                     -> where('visibility', 1)
                                                     -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }

    public function selectHeadphone(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId)
         {
           $fieldsToSelect = ['accessories.id', 'price', 'discount'];

           $computerPart = \DB::table('accessories') -> select($fieldsToSelect)
                                                     -> where('accessories.id', '=', $partId)
                                                     -> where('visibility', 1)
                                                     -> where('typeKey', 'hdphn')
                                                     -> join('accessories_types', 'accessories.accessoryTypeId', 'accessories_types.id')
                                                     -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }

    public function selectKeyboard(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId)
         {
           $fieldsToSelect = ['accessories.id', 'price', 'discount'];

           $computerPart = \DB::table('accessories') -> select($fieldsToSelect)
                                                     -> where('accessories.id', '=', $partId)
                                                     -> where('visibility', 1)
                                                     -> where('typeKey', 'kbrd')
                                                     -> join('accessories_types', 'accessories.accessoryTypeId', 'accessories_types.id')
                                                     -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }

    public function selectComputerMouse(Request $request)
    {
      $data['partExists'] = false;
      $data['price'] = 0;
      $data['discount'] = 0;

      // request data validation logic

      $parameters = $request -> all();

      $validator = \Validator::make($parameters, ['part-id' => 'required|string']);

      if (!$validator -> fails())
      {
         $partId = abs((int) $parameters['part-id']);

         if ($partId)
         {
           $fieldsToSelect = ['accessories.id', 'price', 'discount'];

           $computerPart = \DB::table('accessories') -> select($fieldsToSelect)
                                                     -> where('accessories.id', '=', $partId)
                                                     -> where('visibility', 1)
                                                     -> where('typeKey', 'ms')
                                                     -> join('accessories_types', 'accessories.accessoryTypeId', 'accessories_types.id')
                                                     -> first();

           if (!is_null($computerPart))
           {
             $data['partExists'] = true;
             $data['price'] = $computerPart -> price;
             $data['discount'] = $computerPart -> discount;
           }
         }
      }

      return $data;
    }
}
