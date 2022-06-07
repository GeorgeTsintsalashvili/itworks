<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// shop customer routes

Route::namespace('Shop') -> group(function(){

  // shopping cart routes

  Route::prefix('/shop/cart') -> group(function(){

      Route::get('/', 'ShoppingCartController@index') -> middleware(['shopAuth', 'verified.shopUser']) -> name('shoppingCart');
      Route::post('add', 'ShoppingCartController@add') -> middleware(['cartAction', 'verified.shopUser']) -> name('addCartItem');
      Route::post('remove', 'ShoppingCartController@remove') -> middleware(['cartAction', 'verified.shopUser']) -> name('removeCartItem');
      Route::post('removeAll', 'ShoppingCartController@removeAll') -> middleware(['cartAction', 'verified.shopUser']) -> name('removeCartItems');
      Route::post('changeQuantity', 'ShoppingCartController@changeQuantity') -> middleware(['cartAction', 'verified.shopUser']) -> name('changeCartItemQuantity');
  });

  // user routes

  Route::prefix('/shop/user') -> middleware(['shopAuth', 'verified.shopUser']) -> group(function(){

      Route::post('changePassword', 'UserController@changePassword') -> name('changeShopUserPassword');
      Route::post('changeUserInfo', 'UserController@changeUserInfo') -> name('changeShopUserInfo');
  });

  // payment callback routes

  Route::post('/payment/callback/bog/{key}', 'BogPaymentController@paymentCallback');

  // order and payment routes

  Route::prefix('/shop') -> where(['orderId' => '^\d{10,20}$']) -> middleware(['shopAuth', 'verified.shopUser']) -> group(function(){

      Route::get('messages', 'UserController@messages') -> name('shopUserMessages');
      Route::get('orders', 'UserController@orders') -> name('shopUserOrders');
      Route::get('order/prepare', 'UserController@showPrepareForm') -> name('showOrderPrepare');
      Route::get('order/delete/{orderId}', 'UserController@deleteOrder') -> name('deleteOrder');
      Route::get('order/success/{orderId}', 'UserController@showOrderSuccess') -> name('showOrderSuccess');
      Route::get('order/{orderId}', 'UserController@showOrder') -> name('showOrder');
      Route::get('order/cancel/{orderId}', 'UserController@cancelOrder') -> name('cancelOrder');
      Route::post('order/place', 'UserController@placeOrder') -> name('placeOrder');
      Route::post('orders/list', 'UserController@ordersList') -> name('shopUserOrdersList');

      // bog payment routes

      Route::get('purchaseOrder/{orderId}', 'UserController@purchaseOrder');
      Route::post('requireBogInstallment', 'UserController@requireBogInstallment');
  });

  // search routes

  Route::post('/products/getLiveSearchResults', 'ProductController@getLiveSearchResults');
  Route::get('/search', 'ProductController@search');

  // configurator routes

  Route::prefix('configurator') -> group(function(){

    Route::get('/', 'ConfiguratorController@index');
    Route::get('document', 'ConfiguratorController@generateDocument');

    Route::post('getProcessors', 'ConfiguratorController@getProcessors');
    Route::post('getMotherboards', 'ConfiguratorController@getMotherboards');
    Route::post('getMemories', 'ConfiguratorController@getMemories');
    Route::post('getProcessorCoolers', 'ConfiguratorController@getProcessorCoolers');
    Route::post('getCases', 'ConfiguratorController@getCases');
    Route::post('getPowerSupplies', 'ConfiguratorController@getPowerSupplies');
    Route::post('getVideoCards', 'ConfiguratorController@getVideoCards');
    Route::post('getHardDiskDrives', 'ConfiguratorController@getHardDiskDrives');
    Route::post('getSolidStateDrives', 'ConfiguratorController@getSolidStateDrives');
    Route::post('getMonitors', 'ConfiguratorController@getMonitors');
    Route::post('getHeadphones', 'ConfiguratorController@getHeadphones');
    Route::post('getKeyboards', 'ConfiguratorController@getKeyboards');
    Route::post('getComputerMice', 'ConfiguratorController@getComputerMice');

    Route::post('selectProcessor', 'ConfiguratorController@selectProcessor');
    Route::post('selectMotherboard', 'ConfiguratorController@selectMotherboard');
    Route::post('selectMemory', 'ConfiguratorController@selectMemory');
    Route::post('selectProcessorCooler', 'ConfiguratorController@selectProcessorCooler');
    Route::post('selectCase', 'ConfiguratorController@selectCase');
    Route::post('selectPowerSupply', 'ConfiguratorController@selectPowerSupply');
    Route::post('selectVideoCard', 'ConfiguratorController@selectVideoCard');
    Route::post('selectHardDiskDrive', 'ConfiguratorController@selectHardDiskDrive');
    Route::post('selectSolidStateDrive', 'ConfiguratorController@selectSolidStateDrive');
    Route::post('selectMonitor', 'ConfiguratorController@selectMonitor');
    Route::post('selectHeadphone', 'ConfiguratorController@selectHeadphone');
    Route::post('selectKeyboard', 'ConfiguratorController@selectKeyboard');
    Route::post('selectComputerMouse', 'ConfiguratorController@selectComputerMouse');

  });

  // index routes

  Route::get('/', 'HomeController@index') -> name('home');
  Route::get('/accessories', 'AccessoryController@index') -> name('acc');
  Route::get('/computers', 'ComputerController@index') -> name('sb');
  Route::get('/processors', 'ProcessorController@index') -> name('cpu');
  Route::get('/motherboards', 'MotherboardController@index') -> name('mb');
  Route::get('/memoryModules', 'MemoryModuleController@index') -> name('mm');
  Route::get('/monitors', 'MonitorController@index') -> name('monitors');
  Route::get('/laptops', 'LaptopController@index') -> name('laptops');
  Route::get('/videoCards', 'VideoCardController@index') -> name('vc');
  Route::get('/hardDiskDrives', 'HardDiskDriveController@index') -> name('hdd');
  Route::get('/solidStateDrives', 'SolidStateDriveController@index') -> name('ssd');
  Route::get('/computerCases', 'ComputerCaseController@index') -> name('cases');
  Route::get('/powerSupplies', 'PowerSupplyController@index') -> name('ps');
  Route::get('/processorCoolers', 'ProcessorCoolerController@index') -> name('pc');
  Route::get('/caseCoolers', 'CaseCoolerController@index') -> name('cc');
  Route::get('/opticalDiscDrives', 'OpticalDiscDriveController@index') -> name('odd');
  Route::get('/networkDevices', 'NetworkDeviceController@index') -> name('nd');
  Route::get('/peripherals', 'PeripheralController@index') -> name('peripherals');
  Route::get('/uninterruptiblePowerSupplies', 'UninterruptiblePowerSupplyController@index') -> name('ups');
  Route::get('/notebookChargers', 'NotebookChargerController@index') -> name('nc');

  // load routes

  Route::post('/products/loadSearchResults', 'ProductController@getList') -> name('psrLoad');
  Route::post('/accessories/load', 'AccessoryController@getList') -> name('accLoad');
  Route::post('/computers/load', 'ComputerController@getList') -> name('compLoad');
  Route::post('/processors/load', 'ProcessorController@getList') -> name('cpuLoad');
  Route::post('/motherboards/load', 'MotherboardController@getList') -> name('mbLoad');
  Route::post('/memoryModules/load', 'MemoryModuleController@getList') -> name('mmLoad');
  Route::post('/monitors/load', 'MonitorController@getList') -> name('monitorsLoad');
  Route::post('/laptops/load', 'LaptopController@getList') -> name('laptopsLoad');
  Route::post('/videoCards/load', 'VideoCardController@getList') -> name('vcLoad');
  Route::post('/hardDiskDrives/load', 'HardDiskDriveController@getList') -> name('hddLoad');
  Route::post('/solidStateDrives/load', 'SolidStateDriveController@getList') -> name('ssdLoad');
  Route::post('/computerCases/load', 'ComputerCaseController@getList') -> name('casesLoad');
  Route::post('/powerSupplies/load', 'PowerSupplyController@getList') -> name('psLoad');
  Route::post('/processorCoolers/load', 'ProcessorCoolerController@getList') -> name('pcLoad');
  Route::post('/caseCoolers/load', 'CaseCoolerController@getList') -> name('ccLoad');
  Route::post('/opticalDiscDrives/load', 'OpticalDiscDriveController@getList') -> name('oddLoad');
  Route::post('/networkDevices/load', 'NetworkDeviceController@getList') -> name('ndLoad');
  Route::post('/peripherals/load', 'PeripheralController@getList') -> name('perLoad');
  Route::post('/uninterruptiblePowerSupplies/load', 'UninterruptiblePowerSupplyController@getList') -> name('upsLoad');
  Route::post('/notebookChargers/load', 'NotebookChargerController@getList') -> name('ncLoad');

  // routes for seo

  Route::prefix('/index') -> where(['page' => '^[1-9]\d{0,9}$']) -> group(function(){

      Route::get('/processors/{page}', 'ProcessorController@index');
      Route::get('/motherboards/{page}', 'MotherboardController@index');
      Route::get('/videoCards/{page}', 'VideoCardController@index');
      Route::get('/memoryModules/{page}', 'MemoryModuleController@index');
      Route::get('/powerSupplies/{page}', 'PowerSupplyController@index');
      Route::get('/hardDiskDrives/{page}', 'HardDiskDriveController@index');
      Route::get('/solidStateDrives/{page}', 'SolidStateDriveController@index');
      Route::get('/computerCases/{page}', 'ComputerCaseController@index');
      Route::get('/processorCoolers/{page}', 'ProcessorCoolerController@index');
      Route::get('/caseCoolers/{page}', 'CaseCoolerController@index');
      Route::get('/opticalDiscDrives/{page}', 'OpticalDiscDriveController@index');

      Route::get('/computers/{page}', 'ComputerController@index');
      Route::get('/laptops/{page}', 'LaptopController@index');
      Route::get('/monitors/{page}', 'MonitorController@index');
      Route::get('/notebookChargers/{page}', 'NotebookChargerController@index');
      Route::get('/uninterruptiblePowerSupplies/{page}', 'UninterruptiblePowerSupplyController@index');

      Route::where(['categoryId' => '^([1-9]\d{0,9}|0)$']) -> group(function(){
        Route::get('/accessories/{categoryId}/{page}', 'AccessoryController@index');
        Route::get('/networkDevices/{categoryId}/{page}', 'NetworkDeviceController@index');
        Route::get('/peripherals/{categoryId}/{page}', 'PeripheralController@index');
      });
  });

  // category routes

  Route::group(['where' => ['categoryId' => '^([1-9]\d{0,9}|0)$']], function(){

      Route::get('/accessories/category/{categoryId}', 'AccessoryController@index') -> name('accByType');
      Route::get('/networkDevices/category/{categoryId}', 'NetworkDeviceController@index') -> name('ndByType');
      Route::get('/peripherals/category/{categoryId}', 'PeripheralController@index') -> name('perByType');
  });

  // contact routes

  Route::get('/contact', 'ContactController@index');
  Route::post('/contact/sendMessage', 'ContactController@sendMessage');

  // view routes

  Route::get('/accessories/{id}', 'AccessoryController@view');
  Route::get('/computers/{id}', 'ComputerController@view');
  Route::get('/processors/{id}', 'ProcessorController@view');
  Route::get('/motherboards/{id}', 'MotherboardController@view');
  Route::get('/monitors/{id}', 'MonitorController@view');
  Route::get('/laptops/{id}', 'LaptopController@view');
  Route::get('/memoryModules/{id}', 'MemoryModuleController@view');
  Route::get('/videoCards/{id}', 'VideoCardController@view');
  Route::get('/hardDiskDrives/{id}', 'HardDiskDriveController@view');
  Route::get('/solidStateDrives/{id}', 'SolidStateDriveController@view');
  Route::get('/computerCases/{id}', 'ComputerCaseController@view');
  Route::get('/caseCoolers/{id}', 'CaseCoolerController@view');
  Route::get('/powerSupplies/{id}', 'PowerSupplyController@view');
  Route::get('/opticalDiscDrives/{id}', 'OpticalDiscDriveController@view');
  Route::get('/processorCoolers/{id}', 'ProcessorCoolerController@view');
  Route::get('/networkDevices/{id}', 'NetworkDeviceController@view');
  Route::get('/peripherals/{id}', 'PeripheralController@view');
  Route::get('/uninterruptiblePowerSupplies/{id}', 'UninterruptiblePowerSupplyController@view');
  Route::get('/notebookChargers/{id}', 'NotebookChargerController@view');

  // home page ajax request routes

  Route::get('/computersForHomePage/{id}', 'ComputerController@getComputersForHomePage') -> name('homePageComputers');
  Route::get('/accessoriesForHomePage/{id}', 'AccessoryController@getAccessoriesForHomePage') -> name('homePageAccessories');

});

// shop user auth routes

Route::get('/shop/password/reset/{token}', 'ShopAuth\ResetPasswordController@showResetForm') -> name('shop.password.reset'); // done
Route::post('/shop/password/reset', 'ShopAuth\ResetPasswordController@reset') -> name('shop.password.update'); // done

// Route::get('/shop/password/confirm', 'ShopAuth\ConfirmPasswordController@showConfirmForm') -> name('shop.password.confirm.show'); // enable when password confirmation required
// Route::post('/shop/password/confirm', 'ShopAuth\ConfirmPasswordController@confirm') -> name('shop.password.confirm'); // enable when password confirmation required

Route::post('/shop/logout', 'ShopAuth\LoginController@logout') -> name('shop.logout'); // done
Route::get('/shop/logout', 'ShopAuth\LoginController@logout'); // done

Route::post('/shop/login', 'ShopAuth\LoginController@login') -> name('shop.login'); // done
// Route::get('/shop/login', 'ShopAuth\LoginController@showLoginForm');

Route::post('/shop/password/email', 'ShopAuth\ForgotPasswordController@sendResetLinkEmail') -> name('shop.password.email'); // done
// Route::get('/shop/password/reset', 'ShopAuth\ForgotPasswordController@showLinkRequestForm') -> name('shop.password.request');

// Route::post('/shop/register', 'ShopAuth\RegisterController@register') -> name('shop.register'); // uncomment when finished

Route::post('/shop/register', function(){
  return "Registration is temporarily unavailable";
}) -> name('shop.register'); // remove when finished

// Route::get('/shop/register', 'ShopAuth\RegisterController@showRegistrationForm');

Route::get('/shop/email/verify', 'ShopAuth\VerificationController@show') -> name('shop.verification.notice'); // done
Route::get('/shop/email/verify/{id}/{hash}', 'ShopAuth\VerificationController@verify') -> name('shop.verification.verify'); // done
Route::get('/shop/email/resend', 'ShopAuth\VerificationController@resend') -> name('shop.verification.resend'); // done

Route::get('/shop/email/verified', 'ShopAuth\VerificationController@showVerificationSuccessPage') -> name('shop.successfulVerification');// custom route
Route::get('/shop/email/verification/invalidSiganture', 'ShopAuth\VerificationController@showInvalidSignaturePage') -> name('shop.invalidSignature');// custom route

// control panel auth routes (namespace changed from Auth to ControlPanelAuth)

Route::get('/password/reset/{token}', 'ControlPanelAuth\ResetPasswordController@showResetForm') -> name('password.reset');
Route::post('/password/reset', 'ControlPanelAuth\ResetPasswordController@reset') -> name('password.update');

Route::get('/password/reset', 'ControlPanelAuth\ForgotPasswordController@showLinkRequestForm') -> name('password.request');
Route::post('/password/email', 'ControlPanelAuth\ForgotPasswordController@sendResetLinkEmail') -> name('password.email');

Route::get('/password/confirm', 'ControlPanelAuth\ConfirmPasswordController@showConfirmForm') -> name('password.confirm');
Route::post('/password/confirm', 'ControlPanelAuth\ConfirmPasswordController@confirm');

Route::post('/logout', 'ControlPanelAuth\LoginController@logout') -> name('logout');
Route::get('/logout', 'ControlPanelAuth\LoginController@logout');

Route::post('/login', 'ControlPanelAuth\LoginController@login') -> name('login');
Route::get('/login', 'ControlPanelAuth\LoginController@showLoginForm');

// Route::post('/register', 'ControlPanelAuth\RegisterController@register') -> name('register'); // added for test
// Route::get('/register', 'ControlPanelAuth\RegisterController@showRegistrationForm'); // added for test
Route::get('/email/verify', 'ControlPanelAuth\VerificationController@show') -> name('verification.notice'); // added for test
Route::get('/email/verify/{id}/{hash}', 'ControlPanelAuth\VerificationController@verify') -> name('verification.verify'); // added for test
Route::get('/email/resend', 'ControlPanelAuth\VerificationController@resend') -> name('verification.resend'); // added for test

// control panel routes

Route::get('/controlPanel/getBogCartPaymentInfo/{bankOrderId}', 'Shop\BogPaymentController@testCardGetPaymentInfo') -> middleware(['controlPanelAuth', 'verified']) -> name('card.bog'); // test route
Route::get('/controlPanel/getBogInstallmentInfo/{bankOrderId}', 'Shop\BogPaymentController@testInstallmentInfo') -> middleware(['controlPanelAuth', 'verified']) -> name('installment.bog'); // test route

Route::middleware(['controlPanelAuth', 'verified']) -> namespace('ControlPanel') -> prefix('controlPanel') -> group(function(){

      // order routes

      Route::prefix('orders') -> where(['shopOrderId' => '^\d{10,20}$']) -> group(function(){

          Route::post('/', 'ShopOrdersController@index') -> name('cpOrders');
          Route::get('/edit/{shopOrderId}', 'ShopOrdersController@edit') -> name('editCpanelOrder');
          Route::get('/destroy/{shopOrderId}', 'ShopOrdersController@destroy') -> name('destroyCpanelOrder');
          Route::post('/update/{shopOrderId}', 'ShopOrdersController@update') -> name('updateCpanelOrder');
      });

      // invoice routes

      Route::prefix('invoice') -> group(function(){

          Route::match(['get', 'post'], '/', 'InvoiceController@index') -> name('invoice');
          Route::match(['get', 'post'], 'display', 'InvoiceController@display') -> name('displayInvoice');
          Route::match(['get', 'post'], 'send', 'InvoiceController@send') -> name('sendInvoice');
      });

      // warranty routes

      Route::match(['get', 'post'], 'warranty', 'WarrantyController@index') -> name('warranty');
      Route::match(['get', 'post'], 'displayWarranty', 'WarrantyController@displayWarranty') -> name('displayWarranty');

      // control panel user routes

      Route::post('changePassword', 'UserController@changePassword') -> name('passwordChange');
      Route::post('updateData', 'UserController@updateData') -> name('dataUpdate');

      // control panel home route

      Route::get('/', 'HomeController@index') -> name('controlPanelHome');

      // analytics routes

      Route::prefix('analytics') -> group(function(){

         // various routes

         Route::post('/', 'AnalyticsController@index') -> name('useranalytics');
         Route::post('require', 'AnalyticsController@requireData') -> name('useranalyticsrequire');
         Route::post('destroy', 'AnalyticsController@destroyData') -> name('useranalyticsdestroy');
      });

      // statements routes

      Route::prefix('statements') -> group(function(){

         // index route

         Route::post('/', 'StatementController@index') -> name('userstatements');

         // store routes

         Route::post('storeStatement', 'StatementController@storeStatement') -> name('statementStore');
         Route::post('storeCategory', 'StatementController@storeCategory') -> name('categoryStore');

         // update routes

         Route::post('updateStatement', 'StatementController@updateStatement') -> name('statementUpdate');
         Route::post('updateCategory', 'StatementController@updateCategory') -> name('categoryUpdate');
         Route::post('updateSession', 'StatementController@updateSessionCookie') -> name('sessionCookieUpdate');

         // destroy routes

         Route::get('destroyStatement/{id}', 'StatementController@destroyStatement') -> name('statementDestroy');
         Route::get('destroyCategory{id}', 'StatementController@destroyCategory') -> name('categoryDestroy');
      });

      // contact routes

      Route::post('contact', 'ContactController@index') -> name('cpusercontact');
      Route::post('contact/update', 'ContactController@update') -> name('contactUpdate');

      // slides routes

      Route::prefix('slides') -> group(function(){

          Route::post('/', 'SlideController@index') -> name('cpuserslides');
          Route::post('updateOrder', 'SlideController@updateOrder') -> name('updateSlideOrder');
          Route::post('store', 'SlideController@store') -> name('storeSlides');
          Route::get('destroy/{id}', 'SlideController@destroy') -> name('destroySlide');
      });

      // shop parameters routes

      Route::prefix('parameters') -> group(function(){

          // general parameters routes

          Route::prefix('general') -> group(function(){

              // index route

              Route::post('/', 'HomeController@parameters') -> name('paramsgeneral');

              // destroy routes

              Route::prefix('destroy') -> group(function(){

                   Route::get('stockType/{id}', 'HomeController@destroyStockType') -> name('stockTypeDestroy');
                   Route::get('conditionType/{id}', 'HomeController@destroyConditionType') -> name('condTypeDestroy');
              });

              // update routes

              Route::prefix('update') -> group(function(){

                   Route::post('stockType', 'HomeController@updateStockType') -> name('stockTypeUpdate');
                   Route::post('conditionType', 'HomeController@updateConditionType') -> name('condTypeUpdate');
                   Route::post('priceRanges', 'HomeController@updatePriceRanges') -> name('priceRangesUpdate');
              });

              // store routes

              Route::prefix('store') -> group(function(){

                   Route::post('stockType', 'HomeController@storeStockType') -> name('stockTypeStore');
                   Route::post('conditionType', 'HomeController@storeConditionType') -> name('condTypeStore');
              });
          });

          // processors routes

          Route::prefix('processors') -> group(function(){

               Route::post('/', 'ProcessorController@parameters') -> name('paramscpu');

               // destroy routes

               Route::prefix('destroy') -> group(function(){

                    Route::get('socket/{id}', 'ProcessorController@destroySocket') -> name('socketDestroy');
                    Route::get('chipset/{id}', 'ProcessorController@destroyChipset') -> name('chipsetDestroy');
                    Route::get('system/{id}', 'ProcessorController@destroySystem') -> name('systemDestroy');
                    Route::get('technologyProcess/{id}', 'ProcessorController@destroyTechnologyProcess') -> name('tcpDestroy');
               });

               // update routes

               Route::prefix('update') -> group(function(){

                    Route::post('socket', 'ProcessorController@updateSocket') -> name('socketUpdate');
                    Route::post('chipset', 'ProcessorController@updateChipset') -> name('chipsetUpdate');
                    Route::post('system', 'ProcessorController@updateSystem') -> name('systemUpdate');
                    Route::post('technologyProcess', 'ProcessorController@updateTechnologyProcess') -> name('tcpUpdate');
               });

               // store routes

               Route::prefix('store') -> group(function(){

                    Route::post('socket', 'ProcessorController@storeSocket') -> name('socketStore');
                    Route::post('chipset', 'ProcessorController@storeChipset') -> name('chipsetStore');
                    Route::post('system', 'ProcessorController@storeSystem') -> name('systemStore');
                    Route::post('technologyProcess', 'ProcessorController@storeTechnologyProcess') -> name('tcpStore');
               });
          });

          // memory modules routes

          Route::prefix('memoryModules') -> group(function(){

              Route::post('/', 'MemoryModuleController@parameters') -> name('paramsram');
              Route::post('updateMemoryModuleType', 'MemoryModuleController@updateMemoryModuleType') -> name('mmtypeUpdate');
              Route::get('destroyMemoryModuleType/{id}', 'MemoryModuleController@destroyMemoryModuleType') -> name('mmtypeDestroy');
              Route::post('storeMemoryModuleType', 'MemoryModuleController@storeMemoryModuleType') -> name('mmtypeStore');
          });

          // motherboards routes

          Route::prefix('motherboards') -> group(function(){

            // index route

            Route::post('/', 'MotherboardController@parameters') -> name('paramsmtb');

            // destroy routes

            Route::prefix('destroy') -> group(function(){

                 Route::get('motherboardManufacturer/{id}', 'MotherboardController@destroyMotherboardManufacturer') -> name('mtbManufacturerDestroy');
                 Route::get('motherboardFormFactor/{id}', 'MotherboardController@destroyMotherboardFormFactor') -> name('mtbFormFactorDestroy');
            });

            // update routes

            Route::prefix('update') -> group(function(){

                 Route::post('motherboardManufacturer', 'MotherboardController@updateMotherboardManufacturer') -> name('mtbManufacturerUpdate');
                 Route::post('motherboardFormFactor', 'MotherboardController@updateMotherboardFormFactor') -> name('mtbFormFactorUpdate');
            });

            // store routes

            Route::prefix('store') -> group(function(){

                 Route::post('manufacturer', 'MotherboardController@storeMotherboardManufacturer') -> name('mtbManufacturerStore');
                 Route::post('formFactor', 'MotherboardController@storeMotherboardFormFactor') -> name('mtbFormFactorStore');
            });

          });

          // accessories routes

          Route::prefix('accessories') -> group(function(){

               Route::post('/', 'AccessoryController@parameters') -> name('paramsacc');
               Route::post('store', 'AccessoryController@storeAccessoryType') -> name('accTypeStore');
               Route::post('update', 'AccessoryController@updateAccessoryType') -> name('accTypeUpdate');
               Route::get('destroy/{id}', 'AccessoryController@destroyAccessoryType') -> name('accTypeDestroy');
          });

          // network devices routes

          Route::prefix('networkDevices') -> group(function(){

               Route::post('/', 'NetworkDeviceController@parameters') -> name('paramsnd');
               Route::post('store', 'NetworkDeviceController@storeNetworkDeviceType') -> name('ndTypeStore');
               Route::post('update', 'NetworkDeviceController@updateNetworkDeviceType') -> name('ndTypeUpdate');
               Route::get('destroy/{id}', 'NetworkDeviceController@destroyNetworkDeviceType') -> name('ndTypeDestroy');
          });

          // peripherals routes

          Route::prefix('peripherals') -> group(function(){

               Route::post('/', 'PeripheralController@parameters') -> name('paramsperipherals');
               Route::post('store', 'PeripheralController@storePeripheralType') -> name('peripheralTypeStore');
               Route::post('update', 'PeripheralController@updatePeripheralType') -> name('peripheralTypeUpdate');
               Route::get('destroy/{id}', 'PeripheralController@destroyPeripheralType') -> name('peripheralTypeDestroy');
          });

          // notebooks chargers manufacturers

          Route::prefix('notebookChargers') -> group(function(){

               Route::post('/', 'NotebookChargerController@parameters') -> name('paramsncm');
               Route::post('store', 'NotebookChargerController@storeNotebookChargerManufacturer') -> name('ncmStore');
               Route::post('update', 'NotebookChargerController@updateNotebookChargerManufacturer') -> name('ncmUpdate');
               Route::get('destroy/{id}', 'NotebookChargerController@destroyNotebookChargerManufacturer') -> name('ncmDestroy');
          });

          // monitors manufacturers

          Route::prefix('monitors') -> group(function(){

               Route::post('/', 'MonitorController@parameters') -> name('paramsmonitor');
               Route::post('store', 'MonitorController@storeMonitorManufacturer') -> name('monitorManufacturerStore');
               Route::post('update', 'MonitorController@updateMonitorManufacturer') -> name('monitorManufacturerUpdate');
               Route::get('destroy/{id}', 'MonitorController@destroyMonitorManufacturer') -> name('monitorManufacturerDestroy');
          });

          // laptop systems

          Route::prefix('laptops') -> group(function(){

               Route::post('/', 'LaptopController@parameters') -> name('paramslaptop');
               Route::post('store', 'LaptopController@storeLaptopSystem') -> name('laptopSystemStore');
               Route::post('update', 'LaptopController@updateLaptopSystem') -> name('laptopSystemUpdate');
               Route::get('destroy/{id}', 'LaptopController@destroyLaptopSystem') -> name('laptopSystemDestroy');
          });

          // optical disc drives types

          Route::prefix('opticalDiscDrives') -> group(function(){

               Route::post('/', 'OpticalDiscDriveController@parameters') -> name('paramsodd');
               Route::post('store', 'OpticalDiscDriveController@storeOpticalDiscDriveType') -> name('oddtStore');
               Route::post('update', 'OpticalDiscDriveController@updateOpticalDiscDriveType') -> name('oddtUpdate');
               Route::get('destroy/{id}', 'OpticalDiscDriveController@destroyOpticalDiscDriveType') -> name('oddtDestroy');
          });

          // hard disk drives form factors routes

          Route::prefix('hardDiskDrives') -> group(function(){

               Route::post('/', 'HardDiskDriveController@parameters') -> name('paramshdd');
               Route::post('store', 'HardDiskDriveController@storeHardDiskDriveFormFactor') -> name('hddffStore');
               Route::post('update', 'HardDiskDriveController@updateHardDiskDriveFormFactor') -> name('hddffUpdate');
               Route::get('destroy/{id}', 'HardDiskDriveController@destroyHardDiskDriveFormFactor') -> name('hddffDestroy');
          });

          // solid state drive form factors routes

          Route::prefix('solidStateDrives') -> group(function(){

               Route::post('/', 'SolidStateDriveController@parameters') -> name('paramsssd');

               Route::post('storeFormFactor', 'SolidStateDriveController@storeSolidStateDriveFormFactor') -> name('ssdffStore');
               Route::post('updateFormFactor', 'SolidStateDriveController@updateSolidStateDriveFormFactor') -> name('ssdffUpdate');
               Route::get('destroyFormFactor/{id}', 'SolidStateDriveController@destroySolidStateDriveFormFactor') -> name('ssdffDestroy');

               Route::post('storeTechnology', 'SolidStateDriveController@storeSolidStateDriveTechnology') -> name('ssdTcStore');
               Route::post('updateTechnology', 'SolidStateDriveController@updateSolidStateDriveTechnology') -> name('ssdTcUpdate');
               Route::get('destroyTechnology/{id}', 'SolidStateDriveController@destroySolidStateDriveTechnology') -> name('ssdTcDestroy');
          });

          // video card parameters routes

          Route::prefix('videoCards') -> group(function(){

               Route::post('/', 'VideoCardController@parameters') -> name('paramsvc');

               // destroy routes

               Route::prefix('destroy') -> group(function(){

                    Route::get('videoCardManufacturer/{id}', 'VideoCardController@destroyVideoCardManufacturer') -> name('vcManufacturerDestroy');
                    Route::get('videoCardMemoryType/{id}', 'VideoCardController@destroyVideoCardMemoryType') -> name('vcMemoryTypeDestroy');
                    Route::get('graphicsType/{id}', 'VideoCardController@destroyGraphicsType') -> name('graphicsTypeDestroy');
                    Route::get('graphicalProcessorManufacturer/{id}', 'VideoCardController@destroyGraphicalProcessorManufacturer') -> name('gpuManufacturerDestroy');
               });

               // update routes

               Route::prefix('update') -> group(function(){

                    Route::post('videoCardManufacturer', 'VideoCardController@updateVideoCardManufacturer') -> name('vcManufacturerUpdate');
                    Route::post('videoCardMemoryType', 'VideoCardController@updateVideoCardMemoryType') -> name('vcMemoryTypeUpdate');
                    Route::post('graphicsType', 'VideoCardController@updateGraphicsType') -> name('graphicsTypeUpdate');
                    Route::post('graphicalProcessorManufacturer', 'VideoCardController@updateGraphicalProcessorManufacturer') -> name('gpuManufacturerUpdate');
               });

               // store routes

               Route::prefix('store') -> group(function(){

                    Route::post('videoCardManufacturer', 'VideoCardController@storeVideoCardManufacturer') -> name('vcManufacturerStore');
                    Route::post('videoCardMemoryType', 'VideoCardController@storeVideoCardMemoryType') -> name('vcMemoryTypeStore');
                    Route::post('graphicsType', 'VideoCardController@storeGraphicsType') -> name('graphicsTypeStore');
                    Route::post('graphicalProcessorManufacturer', 'VideoCardController@storeGraphicalProcessorManufacturer') -> name('gpuManufacturerStore');
               });
          });
      });

      // list routes

      Route::post('accessories', 'AccessoryController@index') -> name('cpuseracc');
      Route::post('computers', 'ComputerController@index') -> name('cpusersb');
      Route::post('processors', 'ProcessorController@index') -> name('cpusercpu');
      Route::post('motherboards', 'MotherboardController@index') -> name('cpusermb');
      Route::post('memoryModules', 'MemoryModuleController@index') -> name('cpusermm');
      Route::post('monitors', 'MonitorController@index') -> name('cpusermonitors');
      Route::post('laptops', 'LaptopController@index') -> name('cpuserlaptops');
      Route::post('videoCards', 'VideoCardController@index') -> name('cpuservc');
      Route::post('hardDiskDrives', 'HardDiskDriveController@index') -> name('cpuserhdd');
      Route::post('solidStateDrives', 'SolidStateDriveController@index') -> name('cpuserssd');
      Route::post('computerCases', 'ComputerCaseController@index') -> name('cpusercases');
      Route::post('powerSupplies', 'PowerSupplyController@index') -> name('cpuserps');
      Route::post('processorCoolers', 'ProcessorCoolerController@index') -> name('cpuserpc');
      Route::post('caseCoolers', 'CaseCoolerController@index') -> name('cpusercc');
      Route::post('opticalDiscDrives', 'OpticalDiscDriveController@index') -> name('cpuserodd');
      Route::post('networkDevices', 'NetworkDeviceController@index') -> name('cpusernd');
      Route::post('peripherals', 'PeripheralController@index') -> name('cpuserperipherals');
      Route::post('uninterruptiblePowerSupplies', 'UninterruptiblePowerSupplyController@index') -> name('cpuserups');
      Route::post('notebookChargers', 'NotebookChargerController@index') -> name('cpusernc');
      Route::post('templates', 'ComputerController@templates') -> name('cpusertemplates');

      // base data update routes

      Route::post('accessoriesBaseUpdate', 'AccessoryController@updateBaseData') -> name('accUpdateBase');
      Route::post('computersBaseUpdate', 'ComputerController@updateBaseData') -> name('sbUpdateBase');
      Route::post('processorsBaseUpdate', 'ProcessorController@updateBaseData') -> name('cpuUpdateBase');
      Route::post('motherboardsBaseUpdate', 'MotherboardController@updateBaseData') -> name('mbUpdateBase');
      Route::post('memoryModulesBaseUpdate', 'MemoryModuleController@updateBaseData') -> name('mmUpdateBase');
      Route::post('monitorsBaseUpdate', 'MonitorController@updateBaseData') -> name('monitorUpdateBase');
      Route::post('laptopsBaseUpdate', 'LaptopController@updateBaseData') -> name('laptopUpdateBase');
      Route::post('videoCardsBaseUpdate', 'VideoCardController@updateBaseData') -> name('vcUpdateBase');
      Route::post('hardDiskDrivesBaseUpdate', 'HardDiskDriveController@updateBaseData') -> name('hddUpdateBase');
      Route::post('solidStateDrivesBaseUpdate', 'SolidStateDriveController@updateBaseData') -> name('ssdUpdateBase');
      Route::post('computerCasesBaseUpdate', 'ComputerCaseController@updateBaseData') -> name('caseUpdateBase');
      Route::post('powerSuppliesBaseUpdate', 'PowerSupplyController@updateBaseData') -> name('psUpdateBase');
      Route::post('processorCoolersBaseUpdate', 'ProcessorCoolerController@updateBaseData') -> name('pcUpdateBase');
      Route::post('caseCoolersBaseUpdate', 'CaseCoolerController@updateBaseData') -> name('ccUpdateBase');
      Route::post('opticalDiscDrivesBaseUpdate', 'OpticalDiscDriveController@updateBaseData') -> name('oddUpdateBase');
      Route::post('networkDevicesBaseUpdate', 'NetworkDeviceController@updateBaseData') -> name('ndUpdateBase');
      Route::post('peripheralsBaseUpdate', 'PeripheralController@updateBaseData') -> name('peripheralUpdateBase');
      Route::post('uninterruptiblePowerSuppliesBaseUpdate', 'UninterruptiblePowerSupplyController@updateBaseData') -> name('upsUpdateBase');
      Route::post('notebookChargersBaseUpdate', 'NotebookChargerController@updateBaseData') -> name('ncUpdateBase');
      Route::post('updateHeaderTemplateTitle', 'ComputerController@updateHeaderTemplateTitle') -> name('headerTemplateTitleUpdateBase');
      Route::post('updateFooterTemplateTitle', 'ComputerController@updateFooterTemplateTitle') -> name('footerTemplateTitleUpdateBase');

      // record store routes

      Route::post('storeComputer', 'ComputerController@store') -> name('sbStore');
      Route::post('storeAccessory', 'AccessoryController@store') -> name('accStore');
      Route::post('storeProcessor', 'ProcessorController@store') -> name('cpuStore');
      Route::post('storeMotherboard', 'MotherboardController@store') -> name('mbStore');
      Route::post('storeMemoryModule', 'MemoryModuleController@store') -> name('mmStore');
      Route::post('storeMonitor', 'MonitorController@store') -> name('monitorStore');
      Route::post('storeLaptop', 'LaptopController@store') -> name('laptopStore');
      Route::post('storeVideoCard', 'VideoCardController@store') -> name('vcStore');
      Route::post('storeHardDiskDrive', 'HardDiskDriveController@store') -> name('hddStore');
      Route::post('storeComputerCase', 'ComputerCaseController@store') -> name('caseStore');
      Route::post('storeSolidStateDrive', 'SolidStateDriveController@store') -> name('ssdStore');
      Route::post('storePowerSupply', 'PowerSupplyController@store') -> name('psStore');
      Route::post('storeProcessorCooler', 'ProcessorCoolerController@store') -> name('pcStore');
      Route::post('storeCaseCooler', 'CaseCoolerController@store') -> name('ccStore');
      Route::post('storeOpticalDiscDrive', 'OpticalDiscDriveController@store') -> name('oddStore');
      Route::post('storeNetworkDevice', 'NetworkDeviceController@store') -> name('ndStore');
      Route::post('storePeripheral', 'PeripheralController@store') -> name('peripheralStore');
      Route::post('storeUninterruptiblePowerSupply', 'UninterruptiblePowerSupplyController@store') -> name('upsStore');
      Route::post('storeNotebookCharger', 'NotebookChargerController@store') -> name('ncStore');
      Route::post('storeHeaderTemplate', 'ComputerController@storeComputerHeaderTemplate') -> name('headerTemplateStore');
      Route::post('storeFooterTemplate', 'ComputerController@storeComputerFooterTemplate') -> name('footerTemplateStore');
      Route::post('createSystemCopy', 'ComputerController@createSystemCopy') -> name('copySystem');

      // record update routes

      Route::post('updateComputer', 'ComputerController@update') -> name('sbUpdate');
      Route::post('updateAccessory', 'AccessoryController@update') -> name('accUpdate');
      Route::post('updateProcessor', 'ProcessorController@update') -> name('cpuUpdate');
      Route::post('updateMotherboard', 'MotherboardController@update') -> name('mbUpdate');
      Route::post('updateMemoryModule', 'MemoryModuleController@update') -> name('mmUpdate');
      Route::post('updateMonitor', 'MonitorController@update') -> name('monitorUpdate');
      Route::post('updateLaptop', 'LaptopController@update') -> name('laptopUpdate');
      Route::post('updateVideoCard', 'VideoCardController@update') -> name('vcUpdate');
      Route::post('updateHardDiskDrive', 'HardDiskDriveController@update') -> name('hddUpdate');
      Route::post('updateComputerCase', 'ComputerCaseController@update') -> name('caseUpdate');
      Route::post('updateSolidStateDrive', 'SolidStateDriveController@update') -> name('ssdUpdate');
      Route::post('updatePowerSupply', 'PowerSupplyController@update') -> name('psUpdate');
      Route::post('updateProcessorCooler', 'ProcessorCoolerController@update') -> name('pcUpdate');
      Route::post('updateCaseCooler', 'CaseCoolerController@update') -> name('ccUpdate');
      Route::post('updateOpticalDiscDrive', 'OpticalDiscDriveController@update') -> name('oddUpdate');
      Route::post('updateNetworkDevice', 'NetworkDeviceController@update') -> name('ndUpdate');
      Route::post('updatePeripheral', 'PeripheralController@update') -> name('peripheralUpdate');
      Route::post('updateUninterruptiblePowerSupply', 'UninterruptiblePowerSupplyController@update') -> name('upsUpdate');
      Route::post('updateNotebookCharger', 'NotebookChargerController@update') -> name('ncUpdate');
      Route::post('updateTemplateHeader', 'ComputerController@updateTemplateHeader') -> name('headerTemplateUpdate');
      Route::post('updateTemplateFooter', 'ComputerController@updateTemplateFooter') -> name('footerTemplateUpdate');

      // edit page routes

      Route::get('accessoryEdit/{id}', 'AccessoryController@edit') -> name('accEdit');
      Route::get('computerEdit/{id}', 'ComputerController@edit') -> name('sbEdit');
      Route::get('processorEdit/{id}', 'ProcessorController@edit') -> name('cpuEdit');
      Route::get('motherboardEdit/{id}', 'MotherboardController@edit') -> name('mbEdit');
      Route::get('memoryModuleEdit/{id}', 'MemoryModuleController@edit') -> name('mmEdit');
      Route::get('monitorEdit/{id}', 'MonitorController@edit') -> name('monitorsEdit');
      Route::get('laptopEdit/{id}', 'LaptopController@edit') -> name('laptopsEdit');
      Route::get('videoCardEdit/{id}', 'VideoCardController@edit') -> name('vcEdit');
      Route::get('hardDiskDriveEdit/{id}', 'HardDiskDriveController@edit') -> name('hddEdit');
      Route::get('solidStateDriveEdit/{id}', 'SolidStateDriveController@edit') -> name('ssdEdit');
      Route::get('computerCaseEdit/{id}', 'ComputerCaseController@edit') -> name('casesEdit');
      Route::get('powerSupplyEdit/{id}', 'PowerSupplyController@edit') -> name('psEdit');
      Route::get('processorCoolerEdit/{id}', 'ProcessorCoolerController@edit') -> name('pcEdit');
      Route::get('caseCoolerEdit/{id}', 'CaseCoolerController@edit') -> name('ccEdit');
      Route::get('opticalDiscDriveEdit/{id}', 'OpticalDiscDriveController@edit') -> name('oddEdit');
      Route::get('networkDeviceEdit/{id}', 'NetworkDeviceController@edit') -> name('ndEdit');
      Route::get('peripheralEdit/{id}', 'PeripheralController@edit') -> name('peripheralsEdit');
      Route::get('uninterruptiblePowerSupplyEdit/{id}', 'UninterruptiblePowerSupplyController@edit') -> name('upsEdit');
      Route::get('notebookChargerEdit/{id}', 'NotebookChargerController@edit') -> name('ncEdit');
      Route::get('templateHeaderEdit/{id}', 'ComputerController@editHeaderTemplate') -> name('headerTemplateEdit');
      Route::get('templateFooterEdit/{id}', 'ComputerController@editFooterTemplate') -> name('footerTemplateEdit');

      // record destroy routes

      Route::get('accessoryDestroy/{id}', 'AccessoryController@destroy') -> name('accDestroy');
      Route::get('computerDestroy/{id}', 'ComputerController@destroy') -> name('sbDestroy');
      Route::get('processorDestroy/{id}', 'ProcessorController@destroy') -> name('cpuDestroy');
      Route::get('motherboardDestroy/{id}', 'MotherboardController@destroy') -> name('mbDestroy');
      Route::get('memoryModuleDestroy/{id}', 'MemoryModuleController@destroy') -> name('mmDestroy');
      Route::get('monitorDestroy/{id}', 'MonitorController@destroy') -> name('monitorsDestroy');
      Route::get('laptopDestroy/{id}', 'LaptopController@destroy') -> name('laptopsDestroy');
      Route::get('videoCardDestroy/{id}', 'VideoCardController@destroy') -> name('vcDestroy');
      Route::get('hardDiskDriveDestroy/{id}', 'HardDiskDriveController@destroy') -> name('hddDestroy');
      Route::get('solidStateDriveDestroy/{id}', 'SolidStateDriveController@destroy') -> name('ssdDestroy');
      Route::get('computerCaseDestroy/{id}', 'ComputerCaseController@destroy') -> name('casesDestroy');
      Route::get('powerSupplyDestroy/{id}', 'PowerSupplyController@destroy') -> name('psDestroy');
      Route::get('processorCoolerDestroy/{id}', 'ProcessorCoolerController@destroy') -> name('pcDestroy');
      Route::get('caseCoolerDestroy/{id}', 'CaseCoolerController@destroy') -> name('ccDestroy');
      Route::get('opticalDiscDriveDestroy/{id}', 'OpticalDiscDriveController@destroy') -> name('oddDestroy');
      Route::get('networkDeviceDestroy/{id}', 'NetworkDeviceController@destroy') -> name('ndDestroy');
      Route::get('peripheralDestroy/{id}', 'PeripheralController@destroy') -> name('peripheralsDestroy');
      Route::get('uninterruptiblePowerSupplyDestroy/{id}', 'UninterruptiblePowerSupplyController@destroy') -> name('upsDestroy');
      Route::get('notebookChargerDestroy/{id}', 'NotebookChargerController@destroy') -> name('ncDestroy');
      Route::get('templateHeaderChargerDestroy/{id}', 'ComputerController@destroyHeaderTemplate') -> name('headerTemplateDestroy');
      Route::get('templateFooterChargerDestroy/{id}', 'ComputerController@destroyFooterTemplate') -> name('footerTemplateDestroy');

      // upload carousel image routes

      Route::post('uploadComputerImage', 'ComputerController@uploadImage') -> name('sbImageUpload');
      Route::post('uploadAccessoryImage', 'AccessoryController@uploadImage') -> name('accImageUpload');
      Route::post('uploadProcessorImage', 'ProcessorController@uploadImage') -> name('cpuImageUpload');
      Route::post('uploadMotherboardImage', 'MotherboardController@uploadImage') -> name('mbImageUpload');
      Route::post('uploadMemoryModuleImage', 'MemoryModuleController@uploadImage') -> name('mmImageUpload');
      Route::post('uploadMonitorImage', 'MonitorController@uploadImage') -> name('monitorImageUpload');
      Route::post('uploadLaptopImage', 'LaptopController@uploadImage') -> name('laptopImageUpload');
      Route::post('uploadVideoCardImage', 'VideoCardController@uploadImage') -> name('vcImageUpload');
      Route::post('uploadHardDiskDriveImage', 'HardDiskDriveController@uploadImage') -> name('hddImageUpload');
      Route::post('uploadComputerCaseImage', 'ComputerCaseController@uploadImage') -> name('caseImageUpload');
      Route::post('uploadSolidStateDriveImage', 'SolidStateDriveController@uploadImage') -> name('ssdImageUpload');
      Route::post('uploadPowerSupplyImage', 'PowerSupplyController@uploadImage') -> name('psImageUpload');
      Route::post('uploadProcessorCoolerImage', 'ProcessorCoolerController@uploadImage') -> name('pcImageUpload');
      Route::post('uploadCaseCoolerImage', 'CaseCoolerController@uploadImage') -> name('ccImageUpload');
      Route::post('uploadOpticalDiscDriveImage', 'OpticalDiscDriveController@uploadImage') -> name('oddImageUpload');
      Route::post('uploadNetworkDeviceImage', 'NetworkDeviceController@uploadImage') -> name('ndImageUpload');
      Route::post('uploadPeripheralImage', 'PeripheralController@uploadImage') -> name('peripheralImageUpload');
      Route::post('uploadUninterruptiblePowerSupplyImage', 'UninterruptiblePowerSupplyController@uploadImage') -> name('upsImageUpload');
      Route::post('uploadNotebookChargerImage', 'NotebookChargerController@uploadImage') -> name('ncImageUpload');

      // update main image routes

      Route::post('updateComputerImage', 'ComputerController@updateImage') -> name('sbImageUpdate');
      Route::post('updateAccessoryImage', 'AccessoryController@updateImage') -> name('accImageUpdate');
      Route::post('updateProcessorImage', 'ProcessorController@updateImage') -> name('cpuImageUpdate');
      Route::post('updateMotherboardImage', 'MotherboardController@updateImage') -> name('mbImageUpdate');
      Route::post('updateMemoryModuleImage', 'MemoryModuleController@updateImage') -> name('mmImageUpdate');
      Route::post('updateMonitorImage', 'MonitorController@updateImage') -> name('monitorImageUpdate');
      Route::post('updateLaptopImage', 'LaptopController@updateImage') -> name('laptopImageUpdate');
      Route::post('updateVideoCardImage', 'VideoCardController@updateImage') -> name('vcImageUpdate');
      Route::post('updateHardDiskDriveImage', 'HardDiskDriveController@updateImage') -> name('hddImageUpdate');
      Route::post('updateComputerCaseImage', 'ComputerCaseController@updateImage') -> name('caseImageUpdate');
      Route::post('updateSolidStateDriveImage', 'SolidStateDriveController@updateImage') -> name('ssdImageUpdate');
      Route::post('updatePowerSupplyImage', 'PowerSupplyController@updateImage') -> name('psImageUpdate');
      Route::post('updateProcessorCoolerImage', 'ProcessorCoolerController@updateImage') -> name('pcImageUpdate');
      Route::post('updateCaseCoolerImage', 'CaseCoolerController@updateImage') -> name('ccImageUpdate');
      Route::post('updateOpticalDiscDriveImage', 'OpticalDiscDriveController@updateImage') -> name('oddImageUpdate');
      Route::post('updateNetworkDeviceImage', 'NetworkDeviceController@updateImage') -> name('ndImageUpdate');
      Route::post('updatePeripheralImage', 'PeripheralController@updateImage') -> name('peripheralImageUpdate');
      Route::post('updateUninterruptiblePowerSupplyImage', 'UninterruptiblePowerSupplyController@updateImage') -> name('upsImageUpdate');
      Route::post('updateNotebookChargerImage', 'NotebookChargerController@updateImage') -> name('ncImageUpdate');

      // carousel image destroy routes

      Route::get('destroyAccessoryImage/{id}', 'AccessoryController@destroyImage') -> name('accImgDestroy');
      Route::get('destroyComputerImage/{id}', 'ComputerController@destroyImage') -> name('sbImgDestroy');
      Route::get('destroyProcessorImage/{id}', 'ProcessorController@destroyImage') -> name('cpuImgDestroy');
      Route::get('destroyMotherboardImage/{id}', 'MotherboardController@destroyImage') -> name('mbImgDestroy');
      Route::get('destroyMemoryModuleImage/{id}', 'MemoryModuleController@destroyImage') -> name('mmImgDestroy');
      Route::get('destroyMonitorImage/{id}', 'MonitorController@destroyImage') -> name('monitorImgDestroy');
      Route::get('destroyLaptopImage/{id}', 'LaptopController@destroyImage') -> name('laptopImgDestroy');
      Route::get('destroyVideoCardImage/{id}', 'VideoCardController@destroyImage') -> name('vcImgDestroy');
      Route::get('destroyHardDiskDriveImage/{id}', 'HardDiskDriveController@destroyImage') -> name('hddImgDestroy');
      Route::get('destroySolidStateDriveImage/{id}', 'SolidStateDriveController@destroyImage') -> name('ssdImgDestroy');
      Route::get('destroyComputerCaseImage/{id}', 'ComputerCaseController@destroyImage') -> name('casesImgDestroy');
      Route::get('destroyPowerSupplyImage/{id}', 'PowerSupplyController@destroyImage') -> name('psImgDestroy');
      Route::get('destroyProcessorCoolerImage/{id}', 'ProcessorCoolerController@destroyImage') -> name('pcImgDestroy');
      Route::get('destroyCaseCoolerImage/{id}', 'CaseCoolerController@destroyImage') -> name('ccImgDestroy');
      Route::get('destroyOpticalDiscDriveImage/{id}', 'OpticalDiscDriveController@destroyImage') -> name('oddImgDestroy');
      Route::get('destroyNetworkDeviceImage/{id}', 'NetworkDeviceController@destroyImage') -> name('ndImgDestroy');
      Route::get('destroyPeripheralImage/{id}', 'PeripheralController@destroyImage') -> name('peripheralImgDestroy');
      Route::get('destroyUninterruptiblePowerSupplyImage/{id}', 'UninterruptiblePowerSupplyController@destroyImage') -> name('upsImgDestroy');
      Route::get('destroyNotebookChargerImage/{id}', 'NotebookChargerController@destroyImage') -> name('ncImgDestroy');
});
