<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Rules\NaturalNumber;

class ShopOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $parameters = $request -> only([
        'order-status',
        'payment-method',
        'list-page',
        'search-query'
      ]);

      $rules = [
        'order-status' => [
          'nullable',
          'string',
          'max:100'
        ],
        'payment-method' => [
          'nullable',
          'string',
          'max:100'
        ],
        'list-page' => [
          'required',
          new NaturalNumber
        ],
        'search-query' => [
          'nullable',
          'string',
          'regex:/^([1-9]\d{9,20})$/'
        ]
      ];

      $listCurrentPage = 1;
      $numOfItemsToView = 20;
      $data['selectedOrderId'] = null;
      $data['selectedOrderStatus'] = null;
      $data['selectedPaymentMethod'] = null;

      $validator = \Validator::make($parameters, $rules);

      if (!$validator -> fails())
      {
        $data['selectedOrderStatus'] = $parameters['order-status'] ?? null;
        $data['selectedPaymentMethod'] = $parameters['payment-method'] ?? null;
        $data['selectedOrderId'] = $parameters['search-query'] ?? null;
        $listCurrentPage = (int) $parameters['list-page'];
      }

      $ordersQueryBuilder = \DB::table('orders');

      if ($data['selectedOrderStatus'])
      {
        $ordersQueryBuilder = $ordersQueryBuilder -> where('order_status', $data['selectedOrderStatus']);
      }

      if ($data['selectedPaymentMethod'])
      {
        $ordersQueryBuilder = $ordersQueryBuilder -> where('payment_method_id', $data['selectedPaymentMethod']);
      }

      if ($data['selectedOrderId'])
      {
        $ordersQueryBuilder = $ordersQueryBuilder -> where('id', $data['selectedOrderId']);
      }

      $totalNumOfItems = $ordersQueryBuilder -> count();
      $data['paginator'] = \Paginator::build($totalNumOfItems, 2, $numOfItemsToView, $listCurrentPage, 2, 2);
      $itemsToSkip = ($data['paginator'] -> currentPage - 1) * $numOfItemsToView;

      $data['orders'] = $ordersQueryBuilder -> orderBy('created_at', 'desc') -> skip($itemsToSkip) -> take($numOfItemsToView) -> get();
      $data['orderStatuses'] = \DB::table('order_statuses') -> get();
      $data['paymentMethods'] = \DB::table('payment_methods') -> get();
      $data['paginationKey'] = 'list-page';
      $data['orderStatusKey'] = 'order-status';
      $data['paymentMethodKey'] = 'payment-method';

      return \View::make('contents.controlPanel.orders.index') -> with(['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($shopOrderId)
    {
      $data['shopOrder'] = \DB::table('orders') -> where('id', $shopOrderId) -> first();

      if ($data['shopOrder'])
      {
        $data['shopOrderItems'] = \DB::table('order_items') -> where('order_id', $shopOrderId) -> get();
        $data['shopUser'] = \DB::table('shop_users') -> where('id', $data['shopOrder'] -> shop_user_id) -> first();
        $data['paymentMethod'] = \DB::table('payment_methods') -> select(['payment_method_title', 'id']) -> where('id', $data['shopOrder'] -> payment_method_id) -> first();
        $data['paymentProvider'] = explode('.', $data['paymentMethod'] -> id)[1];
        $data['orderStatuses'] = \DB::table('order_statuses') -> get();
        $data['paymentMethods'] = \DB::table('payment_methods') -> get();
      }

      return \View::make('contents.controlPanel.orders.edit') -> with(['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $shopOrderId)
    {
        $response['updated'] = false;

        $parameters = $request -> only(['order-status', 'payment-method', 'order-price']);
        $rules = [
          'order-status' => 'required|string|min:2|max:100|exists:order_statuses,order_status_name',
          'payment-method' => 'required|string|min:2|max:100|exists:payment_methods,id',
          'order-price' => 'required|numeric'
        ];

        $validator = \Validator::make($parameters, $rules);

        if (!$validator -> fails())
        {
          \DB::table('orders') -> where('id', $shopOrderId) -> update([
            'order_status' => $parameters['order-status'],
            'payment_method_id' => $parameters['payment-method'],
            'order_price' => $parameters['order-price'],
            'payment_deadline' => time() + 86400
          ]);

          $response['updated'] = true;
        }

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($shopOrderId)
    {
        //
    }
}
