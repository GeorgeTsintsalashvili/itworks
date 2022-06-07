<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Shop\BaseModel;
use App\Models\Shop\User;
use App\Rules\MatchShopUserOldPassword;
use App\Rules\NaturalNumber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    // orders

    public function orders(Request $request)
    {
      $bog = new BogPaymentController();

      $userId = \Auth::guard('shopUser') -> user() -> id;
      $time = time();

      $expiredOrdersQuery = \DB::table('orders') -> join('order_statuses', 'order_status_name', '=', 'order_status')
                                                 -> where('shop_user_id', $userId)
                                                 -> where('payment_deadline', '<', $time)
                                                 -> where('order_status_name', 'confirmed');

      if ($expiredOrdersQuery -> count())
      {
        $expiredOrdersQuery -> update(['order_status' => 'expired']);
      }

      $generalData = BaseModel::getGeneralData();
      $data['bogClientId'] = $bog -> getBogClientId();
      $data['itemsToView'] = 5;
      $columns = ['orders.id', 'payment_deadline', 'order_status_name', 'order_price', 'total_quantity', 'deliver', 'payment_method_title', 'order_placement_date', 'delivery_address', 'customer_name', 'customer_phone', 'order_status_plural_title', 'order_status_title', 'order_status_color', 'allow_delete', 'payment_method_id', 'bank_order_id', 'paid_at'];
      $ordersQuery = \DB::table('orders') -> select($columns)
                                          -> join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
                                          -> join('order_statuses', 'order_status_name', '=', 'order_status')
                                          -> where('shop_user_id', $userId)
                                          -> orderBy('created_at', 'desc');

      $data['orders'] = $ordersQuery -> limit($data['itemsToView']) -> get();
      $data['numOfOrders'] = $ordersQuery -> count();
      $data['orderStatuses'] = \DB::table('order_statuses') -> get();

      if ($data['numOfOrders'])
      {
        $data['orders'] -> each(function($order, $key){

          $order -> order_items = \DB::table('order_items') -> where('order_id', $order -> id) -> get();

          if ($order -> order_status_name == 'confirmed')
          {
            $paymentMethodParts = explode('.', $order -> payment_method_id);
            $order -> payment_method_class = $paymentMethodParts[0];
            $order -> payment_method_provider = $paymentMethodParts[1];
          }

        });
      }

      return view('contents.shop.user.orders', ['contentData' => $data,
                                                'generalData' => $generalData]);
    }

    public function ordersList(Request $request)
    {
      $maxItemsToView = 20;

      $parameters = $request -> only(['curPage', 'itemsToView', 'orderNum', 'ordersType']);
      $validationRules = [
        'curPage' => ['required', new NaturalNumber],
        'itemsToView' => ['required', new NaturalNumber],
        'orderNum' => ['required', new NaturalNumber],
        'ordersType' => ['required', 'string', 'min:1', 'max:300']
      ];

      $validator = \Validator::make($parameters, $validationRules);

      if (!$validator -> fails())
      {
        $data['payload'] = null;

        $curPage = (int) $parameters['curPage'];
        $itemsToView = (int) $parameters['itemsToView'];
        $orderNum = (int) $parameters['orderNum'];

        $userId = \Auth::guard('shopUser') -> user() -> id;

        $ordersQuery = \DB::table('orders') -> select(['orders.id', 'payment_deadline', 'order_status_name', 'payment_method_id', 'order_price', 'total_quantity', 'deliver', 'payment_method_title', 'order_placement_date', 'delivery_address', 'customer_name', 'customer_phone', 'order_status_title', 'order_status_color', 'allow_delete'])
                                            -> join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
                                            -> join('order_statuses', 'order_status_name', '=', 'order_status')
                                            -> where('shop_user_id', $userId);

        if ($parameters['ordersType'] != 'all')
        {
          $orderStatusExists = \DB::table('order_statuses') -> where('order_status_name', $parameters['ordersType']) -> count();

          if ($orderStatusExists)
          {
            $ordersQuery = $ordersQuery -> where('order_status_name', $parameters['ordersType']);
          }
        }

        $data['numOfOrders'] = $ordersQuery -> count();

        if ($data['numOfOrders'])
        {
          $itemsToView = $itemsToView <= $maxItemsToView ? $itemsToView : $maxItemsToView;
          $maxPage = ceil($data['numOfOrders'] / $itemsToView);
          $curPage = $curPage <= $maxPage ? $curPage : $maxPage;

          switch ($orderNum)
          {
            case 1: $ordersQuery = $ordersQuery -> orderBy('created_at', 'desc'); break;
            case 2: $ordersQuery = $ordersQuery -> orderBy('created_at', 'asc'); break;
            case 3: $ordersQuery = $ordersQuery -> orderBy('order_price', 'desc'); break;
            case 4: $ordersQuery = $ordersQuery -> orderBy('order_price', 'asc'); break;
          }

          $orders = $ordersQuery -> skip(($curPage - 1) * $itemsToView) -> take($itemsToView) -> get();

          $orders -> each(function($order, $key){

            $order -> order_items = \DB::table('order_items') -> where('order_id', $order -> id) -> get();

            if ($order -> order_status_name == 'confirmed')
            {
              $paymentMethodParts = explode('.', $order -> payment_method_id);
              $order -> payment_method_class = $paymentMethodParts[0];
              $order -> payment_method_provider = $paymentMethodParts[1];
              $order -> payment_datetime = date('Y-m-d H:i:s', $order -> payment_deadline);
            }

          });

          $data['payload'] = \View::make('contents.shop.user.ordersList', ['orders' => $orders]) -> render();

          return $data;
        }

        return [
          'noOrdersExist' => true,
          'payload' => \View::make('contents.shop.user.noOrders') -> render()
        ];
      }

      return [
        'invalidInput' => $parameters['ordersType']
      ];
    }

    public function showOrder($orderId)
    {
      $generalData = BaseModel::getGeneralData();

      $userId = \Auth::guard('shopUser') -> user() -> id;

      $data['order'] = \DB::table('orders') -> select(['orders.id', 'order_price', 'total_quantity', 'deliver', 'payment_method_title', 'order_placement_date', 'delivery_address', 'customer_name', 'customer_phone', 'order_status_title', 'order_status_color'])
                                            -> join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
                                            -> join('order_statuses', 'order_status_name', '=', 'order_status')
                                            -> where('shop_user_id', $userId)
                                            -> where('orders.id', $orderId)
                                            -> first();

      if ($data['order'])
      {
        $data['order'] -> order_items = \DB::table('order_items') -> where('order_id', $data['order'] -> id) -> get();

        return view('contents.shop.user.order', ['contentData' => $data]);
      }

      return redirect(route('shoppingCart'));
    }

    public function showPrepareForm(Request $request)
    {
      $generalData = BaseModel::getGeneralData();

      $data['shopUser'] = \Auth::guard('shopUser') -> user();
      $data['cartIsEmpty'] = $data['shopUser'] -> cartItemsQuantity() == 0;

      if (!$data['cartIsEmpty'])
      {
        $data['shoppingCart'] = $data['shopUser'] -> shoppingCart();

        $data['installmentPaymentMethodOptions'] = \DB::table('payment_methods') -> where('id', 'like', 'installment%') -> get();
        $data['cardPaymentMethodOptions'] = \DB::table('payment_methods') -> where('id', 'like', 'card%') -> get();
        $data['invoicePaymentMethodOptions'] = \DB::table('payment_methods') -> where('id', 'like', 'invoice%') -> get();

        $data['shoppingCart'] -> total_price = $data['shoppingCart'] -> total_price - $data['shoppingCart'] -> total_discount;

        return view('contents.shop.user.orderPrepareForm', ['contentData' => $data,
                                                            'generalData' => $generalData]);
      }

      return redirect(route('shoppingCart'));
    }

    public function placeOrder(Request $request)
    {
      $response['orderPlaced'] = false;
      $response['pendingOrdersLimitReached'] = true;
      $response['installmentAllowed'] = true;

      $pendingOrdersLimit = 5;
      $userId = \Auth::guard('shopUser') -> user() -> id;
      $numOfPendingOrders = \DB::table('orders') -> where('shop_user_id', $userId) -> where('order_status', 'pending') -> count();

      if ($numOfPendingOrders <= $pendingOrdersLimit)
      {
        $response['pendingOrdersLimitReached'] = false;

        $parameters = $request -> only(['name', 'phone', 'delivery-address', 'delivery-method', 'payment-method']);

        $orderDataValidationRules = [
          'name' => ['required', 'string', 'min:2', 'max:200'],
          'phone' => ['required', 'string', 'regex:/^\+?\d{4,50}$/'],
          'delivery-address' => ['nullable', 'string', 'max:500'],
          'delivery-method' => ['required', 'integer', 'min:0', 'max:1'],
          'payment-method' => ['required', 'string', 'min:3', 'max:200']
        ];

        $orderDataValidator = \Validator::make($parameters, $orderDataValidationRules);

        if (!$orderDataValidator -> fails())
        {
          $paymentMethodExists = \DB::table('payment_methods') -> where('id', $parameters['payment-method']) -> count() != 0;

          if ($paymentMethodExists)
          {
            $data['shopUser'] = \Auth::guard('shopUser') -> user();
            $data['cartIsEmpty'] = $data['shopUser'] -> cartItemsQuantity() == 0;

            if (!$data['cartIsEmpty'])
            {
              $shoppingCart = $data['shopUser'] -> shoppingCart();

              $deliveryMethod = (int) $parameters['delivery-method'];
              $deliveryAddressFieldIsEmpty = empty($parameters['delivery-address']);
              $deliveryPrice = $deliveryMethod && !$deliveryAddressFieldIsEmpty ? 10 : 0;

              $paymentMethodParts = explode('.', $parameters['payment-method']);
              $paymentMethodClass = $paymentMethodParts[0];
              $paymentMethodName = $paymentMethodParts[1];

              if ($deliveryPrice == 0)
              {
                $deliveryMethod = 0;
              }

              $orderPrice = $shoppingCart -> total_price + $deliveryPrice - $shoppingCart -> total_discount;

              if ($paymentMethodClass == 'installment' && $orderPrice < 500)
              {
                $response['installmentAllowed'] = false;
              }

              else
              {
                $orderDeliveryAddress = $deliveryPrice ? $parameters['delivery-address'] : null;
                $orderId = sprintf('%d%d', $data['shopUser'] -> id, time());

                \DB::table('orders') -> insert([
                  'id' => $orderId,
                  'shop_user_id' => $data['shopUser'] -> id,
                  'customer_name' => $parameters['name'],
                  'customer_phone' => $parameters['phone'],
                  'order_price' => $orderPrice,
                  'delivery_address' => $orderDeliveryAddress,
                  'deliver' => $deliveryMethod,
                  'payment_method_id' => $parameters['payment-method'],
                  'total_quantity' => $shoppingCart -> total_quantity,
                  'order_placement_date' => date('Y-m-d H:i:s'),
                  'payment_deadline' => time() + (24 * 3600)
                ]);

                $orderItems = [];
                $shoppingCartItems = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> get();

                foreach($shoppingCartItems as $cartItem)
                {
                  $orderItems[] = ['order_id' => $orderId,
                                   'order_item_price' => $cartItem -> cart_item_price,
                                   'order_item_quantity' => $cartItem -> cart_item_quantity,
                                   'product_id' => $cartItem -> product_id,
                                   'product_title' => $cartItem -> product_title,
                                   'product_image' => $cartItem -> product_image,
                                   'product_category_id' => $cartItem -> product_category_id,
                                   'product_route' => $cartItem -> product_route];
                }

                \DB::table('order_items') -> insert($orderItems);
                \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> delete();
                \DB::table('shopping_carts') -> where('id', $shoppingCart -> id) -> update(['total_price' => 0, 'total_discount' => 0, 'total_quantity' => 0]);

                $response['orderPlaced'] = true;
                $response['orderId'] = $orderId;
              }
            }
          }
        }
      }

      return $response;
    }

    public function showOrderSuccess($orderId)
    {
      $generalData = BaseModel::getGeneralData();
      $userId = $shopUser = \Auth::guard('shopUser') -> user() -> id;

      $data['order'] = \DB::table('orders') -> select(['orders.id', 'order_price', 'total_quantity', 'deliver', 'payment_method_title', 'order_placement_date', 'delivery_address', 'customer_name', 'customer_phone', 'order_status'])
                                            -> join('payment_methods', 'payment_methods.id', '=', 'orders.payment_method_id')
                                            -> where('shop_user_id', $userId)
                                            -> where('orders.id', $orderId)
                                            -> first();

      if ($data['order'] && $data['order'] -> order_status == 'pending')
      {
        $data['order'] -> order_items = \DB::table('order_items') -> where('order_id', $data['order'] -> id) -> get();

        return view('contents.shop.user.orderSuccess', ['contentData' => $data,
                                                        'generalData' => $generalData]);
      }

      return redirect(route('shoppingCart'));
    }

    public function deleteOrder($orderId)
    {
      $response['deleted'] = false;

      $shopUserId = \Auth::guard('shopUser') -> user() -> id;

      $data['order'] = \DB::table('orders') -> select(['order_status'])
                                            -> where('shop_user_id', $shopUserId)
                                            -> where('orders.id', $orderId)
                                            -> first();

      if ($data['order'] && $data['order'] -> order_status != 'paid')
      {
        \DB::table('order_items') -> where('order_id', $orderId) -> delete();
        \DB::table('orders') -> where('id', $orderId) -> delete();

        $response['deleted'] = true;
      }

      return $response;
    }

    public function cancelOrder($orderId)
    {
      $response['canceled'] = false;

      $shopUserId = \Auth::guard('shopUser') -> user() -> id;

      $data['order'] = \DB::table('orders') -> select(['order_status'])
                                            -> where('shop_user_id', $shopUserId)
                                            -> where('orders.id', $orderId)
                                            -> first();

      if ($data['order'] && $data['order'] -> order_status == 'pending')
      {
        \DB::table('orders') -> where('id', $orderId) -> update(['order_status' => 'canceled']);

        $response['canceled'] = true;
      }

      return $response;
    }

    // messages

    public function messages(Request $request)
    {
      $generalData = BaseModel::getGeneralData();
      $data['numOfMessages'] = 0;

      $shopUser = \Auth::guard('shopUser') -> user();
      $messageBox = $shopUser -> messageBox();

      if ($messageBox)
      {
        //
      }

      return view('contents.shop.user.messages', ['contentData' => $data,
                                                  'generalData' => $generalData]);
    }

    // user data

    public function changeUserInfo(Request $request)
    {
      $response = ['userInfoChanged' => false];

      $userInfoParameters = $request -> only(['name', 'phone', 'address']);

      $userInfoRules = [
        'name' => ['required', 'string', 'min:3', 'max:200'],
        'phone' => ['required', 'string', 'regex:/^\+?\d{4,50}$/'],
        'address' => ['nullable', 'string', 'min:3', 'max:500']
      ];

      $userInfoValidator = \Validator::make($userInfoParameters, $userInfoRules);

      if (!$userInfoValidator -> fails())
      {
        \DB::table('shop_users') -> where('id', \Auth::guard('shopUser') -> user() -> id) -> update($userInfoParameters);

        $response['userInfoChanged'] = true;

        session() -> regenerate();
      }

      return $response;
    }

    public function changePassword(Request $request)
    {
      $response = ['oldPasswordIsValid' => false,
                   'passwordChanged' => false,
                   'passwordsMatch' => false];

      $oldPasswordParameters = $request -> only(['old-password']);

      $oldPasswordRulesRules = [
        'old-password' => ['required', 'string', new MatchShopUserOldPassword]
      ];

      $oldPasswordValidator = \Validator::make($oldPasswordParameters, $oldPasswordRulesRules);

      if (!$oldPasswordValidator -> fails())
      {
        $response['oldPasswordIsValid'] = true;

        $newPasswordParameters = $request -> only(['new-password', 'new-password-confirmation']);

        $newPasswordRules = [
          'new-password' => ['required', 'string', 'min:8', 'max:50'],
          'new-password-confirmation' => ['required', 'string', 'min:8', 'max:50', 'same:new-password']
        ];

        $newPasswordValidator = \Validator::make($newPasswordParameters, $newPasswordRules);

        if (!$newPasswordValidator -> fails())
        {
          $response['passwordsMatch'] = true;

          User::find(\Auth::guard('shopUser') -> user() -> id) -> update(['password' => Hash::make($newPasswordParameters['new-password'])]);

          $response['passwordChanged'] = true;

          session() -> regenerate();
        }
      }

      return $response;
    }

    // purchase order

    public function purchaseOrder($orderId)
    {
      $order = \DB::table('orders') -> select(['id', 'total_quantity', 'order_price', 'order_status', 'order_status', 'payment_method_id', 'payment_deadline'])
                                    -> where('id', $orderId)
                                    -> first();

      if ($order)
      {
        $timeLeft = $order -> payment_deadline - time();

        if ($timeLeft > 0)
        {
          if ($order -> order_status == 'confirmed')
          {
            $paymentMethod = $order -> payment_method_id;

            if ($paymentMethod == 'card.bog')
            {
              $bogPayment = new BogPaymentController;

              $orderItems = \db::table('order_items') -> select('product_category_id', 'product_id', 'product_title', 'order_item_quantity', 'order_item_price')
                                                      -> where('order_id', $order -> id)
                                                      -> get();

              if (!$orderItems -> isEmpty())
              {
                return $bogPayment -> requireOrder($order, $orderItems);
              }

              else return Redirect::to('/shop/payment/failure?reason=noProductsExist');
            }

            else return Redirect::to('/shop/payment/failure?reason=invalidPaymentMethod');
          }

          else return Redirect::to('/shop/payment/failure?reason=notConfirmed');
        }

        else return Redirect::to('/shop/payment/failure?reason=deadlineExpired');
      }

      else return Redirect::to('/shop/payment/failure?reason=invalidOrderId');
    }

    public function requireBogInstallment(Request $request)
    {
      $parameters = $request -> only(['month', 'amount', 'orderId']);

      $rules = [
        'orderId' => ['required', 'regex:/^\d{10,20}$/'],
        'month' => ['required', 'regex:/^(3|6|9|12|24|36|48)$/'],
      ];

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $userId = \Auth::guard('shopUser') -> user() -> id;

        $order = \DB::table('orders') -> select(['id', 'order_status', 'order_price'])
                                      -> where('payment_method_id', 'installment.bog')
                                      -> where('id', $parameters['orderId'])
                                      -> where('order_status', 'confirmed')
                                      -> where('shop_user_id', $userId)
                                      -> first();

        if ($order)
        {
          $bogPayment = new BogPaymentController;

          $columns = ['order_item_price', 'product_title', 'order_item_quantity', 'product_category_id', 'product_id', 'product_image', 'product_route'];

          $order -> months = $parameters['month'];
          $order -> orderItems = \DB::table('order_items') -> select($columns) -> where('order_id', $order -> id) -> get();

          return $bogPayment -> requireInstallmentForOrder($order);
        }
      }

      return ['error' => 'invalid input'];
    }
}
