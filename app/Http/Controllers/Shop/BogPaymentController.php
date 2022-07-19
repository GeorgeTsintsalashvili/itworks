<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\BaseModel;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BogPaymentController extends Controller
{
    private $clientId = '556876';
    private $secretKey = '45364fghfghgfhfg5645fghfghfg';
    private $authAddress = 'https://ipay.ge/opay/api/v1/oauth2/token';
    private $bankAuthKey = '5fgghfg6767dfsds2309676';
    private $authJsonResponse = null;
    private $authResponse = null;

    private $requireOrderForCardPaymentAddress = 'https://ipay.ge/opay/api/v1/checkout/orders';
    private $cardPaymentInfoAddress = 'https://ipay.ge/opay/api/v1/checkout/orders/';
    private $cardPaymentSiteRedirectUri = '/shop/payment/orderInfo/bog/';

    private $installmentInfoAddress = 'https://installment.bog.ge/v1/installment/checkout/';
    private $requireOrderForInstallmentAddress = 'https://installment.bog.ge/v1/installment/checkout';
    private $installmentSuccessRedirectUri = '/shop/payment/installment/bog/success/';
    private $installmentFailureRedirectUri = '/shop/payment/installment/bog/failure/';
    private $installmentRejectionRedirectUri = '/shop/payment/installment/bog/rejection/';

    private function merchantAuth()
    {
      $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'Authorization' => 'Basic ' . base64_encode($this -> clientId . ':' . $this -> secretKey)
      ];

      $body['grant_type'] = 'client_credentials';

      $response = Http::withHeaders($headers) -> asForm() -> post($this -> authAddress, $body);

      $this -> authResponse = $response -> successful() ? $response -> json() : null;
    }

    public function requireOrder($order, $orderItems)
    {
      $this -> merchantAuth();

      if ($this -> authResponse && isset($this -> authResponse['access_token']))
      {
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this -> authResponse['access_token']
        ];

        $jsonData['intent'] = 'CAPTURE';
        $jsonData['items'] = $this -> getBankCompliantOrderItemsForCardPayment($orderItems);
        $jsonData['shop_order_id'] = $order -> id;
        $jsonData['redirect_url'] = url('/') . $this -> cardPaymentSiteRedirectUri . $order -> id;
        $jsonData['purchase_units'] = [
          [
            'amount' => [
              'currency_code' => 'GEL',
              'value' => $order -> order_price
            ]
          ]
        ];

        try
        {
          $client = new Client();

          $requireOrderResponse = $client -> request('POST', $this -> requireOrderForCardPaymentAddress, [
              'headers' => $headers,
              'body' => json_encode($jsonData)
          ]);

          $requireOrderResponseBody = json_decode($requireOrderResponse -> getBody());

          if (count($requireOrderResponseBody -> links) > 1)
          {
            $linksAddress = $requireOrderResponseBody -> links[1] -> href;

            \DB::table('orders') -> where('id', $order -> id) -> update(['bank_order_id' => $requireOrderResponseBody -> order_id]);

            return Redirect::to($linksAddress);
          }

          else throw new \Exception;
        }

        catch (ClientException $exception)
        {
          // $exception -> getResponse() -> getBody() -> getContents()

          return Redirect::to(route('bogTechnicalFailure'));
        }
      }

      return Redirect::to(route('bogTechnicalFailure'));
    }

    public function requireInstallmentForOrder($order)
    {
      $this -> merchantAuth();

      if ($this -> authResponse && isset($this -> authResponse['access_token']))
      {
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this -> authResponse['access_token']
        ];

        $url = url('/');

        $jsonData['intent'] = 'LOAN';
        $jsonData['installment_month'] = $order -> months;
        $jsonData['installment_type'] = 'STANDARD';
        $jsonData['cart_items'] = $this -> getBankCompliantOrderItemsForInstallment($order -> orderItems);
        $jsonData['shop_order_id'] = $order -> id;
        $jsonData['success_redirect_url'] = $url . $this -> installmentSuccessRedirectUri . $order -> id;
        $jsonData['fail_redirect_url'] = $url . $this -> installmentFailureRedirectUri . $order -> id;
        $jsonData['reject_redirect_url'] = $url . $this -> installmentRejectionRedirectUri . $order -> id;
        $jsonData['validate_items'] = false;
        $jsonData['locale'] = 'ka';
        $jsonData['purchase_units'] = [
          [
            'amount' => [
              'currency_code' => 'GEL',
              'value' => $order -> order_price
            ]
          ]
        ];

        try
        {
          $client = new Client();

          $requireOrderResponse = $client -> request('POST', $this -> requireOrderForInstallmentAddress, [
              'headers' => $headers,
              'body' => json_encode($jsonData)
          ]);

          $requireInstallmentOrderResponseBody = json_decode($requireOrderResponse -> getBody());

          if (count($requireInstallmentOrderResponseBody -> links) > 1 && $requireInstallmentOrderResponseBody -> status == 'CREATED')
          {
            $redirectToBankInstallmentPageLink = $requireInstallmentOrderResponseBody -> links[1] -> href;

            \DB::table('orders') -> where('id', $order -> id) -> update(['bank_order_id' => $requireInstallmentOrderResponseBody -> order_id]);

            return ['redirect' => $redirectToBankInstallmentPageLink];
          }

          else throw new \Exception;
        }

        catch (ClientException $exception)
        {
          // dd($exception -> getResponse() -> getBody() -> getContents());

          return ['error' => 'technical'];
        }
      }

      return ['error' => 'technical'];
    }

    private function getBankCompliantOrderItemsForCardPayment($orderItems)
    {
      $orderItemsForBank = [];

      foreach ($orderItems as $orderItem)
      {
        $bankOrderItem['description'] = $orderItem -> product_title;
        $bankOrderItem['amount'] = (string) ($orderItem -> order_item_price / $orderItem -> order_item_quantity);
        $bankOrderItem['quantity'] = (string) $orderItem -> order_item_quantity;
        $bankOrderItem['product_id'] = (string) $orderItem -> product_id;

        $orderItemsForBank[] = $bankOrderItem;
      }

      return $orderItemsForBank;
    }

    private function getBankCompliantOrderItemsForInstallment($orderItems)
    {
      $orderItemsForBank = [];
      $url = url('/');

      foreach ($orderItems as $orderItem)
      {
        $bankOrderItem['total_item_amount'] = (string) $orderItem -> order_item_price;
        $bankOrderItem['item_description'] = $orderItem -> product_title;
        $bankOrderItem['total_item_qty'] = (string) $orderItem -> order_item_quantity;
        $bankOrderItem['item_vendor_code'] = $orderItem -> product_category_id . '-' . $orderItem -> product_id;
        $bankOrderItem['product_image_url'] =  $url . $orderItem -> product_image;
        $bankOrderItem['item_site_detail_url'] = $url . $orderItem -> product_route;

        $orderItemsForBank[] = $bankOrderItem;
      }

      return $orderItemsForBank;
    }

    public function testCardGetPaymentInfo($bankOrderId)
    {
      $this -> getOrderInfo($this -> cardPaymentInfoAddress, $bankOrderId);
    }

    public function testInstallmentInfo($bankOrderId)
    {
      $this -> getOrderInfo($this -> installmentInfoAddress, $bankOrderId);
    }

    private function getOrderInfo($infoAddress, $bankOrderId)
    {
      $this -> merchantAuth();

      if ($this -> authResponse && isset($this -> authResponse['access_token']))
      {
        $headers = [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this -> authResponse['access_token']
        ];

        $jsonData['order_id'] = $bankOrderId;

        try
        {
          $client = new Client();

          $requireOrderResponse = $client -> request('GET', $infoAddress . $bankOrderId, [
              'headers' => $headers,
              'body' => json_encode($jsonData)
          ]);

          $parsedJsonData = json_decode($requireOrderResponse -> getBody());

          dd($parsedJsonData);
        }

        catch (ClientException $exception)
        {
          dd($exception -> getResponse() -> getBody() -> getContents());
        }
      }
    }

    public function paymentCallback(Request $request, $key)
    {
      $response['invalidInput'] = true;
      $response['orderPaid'] = false;
      $response['keysMatch'] = false;

      if ($this -> bankAuthKey === $key)
      {
        $response['keysMatch'] = true;

        $parameters = $request -> only(['status', 'order_id', 'shop_order_id', 'payment_method']);

        $rules = [
          'status' => 'required|string', // ikos
          'order_id' => 'required|string', // ikos
       //   'payment_hash' => 'required|string',
       //   'ipay_payment_id' => 'required|string',
       //   'status_description' => 'required|string',
          'shop_order_id' => 'required|string',//ikos
          'payment_method' => 'required|string', // ikos
         // 'card_type' => 'required|string'
        ];
        
        
     //   $json = json_encode($parameters);
        
     //   file_put_contents('test.txt', $json);

        $validator = \Validator::make($parameters, $rules);

        if (!$validator -> fails())
        {
          $response['invalidInput'] = false;

          $orderExists = \DB::table('orders') -> where('id', $parameters['shop_order_id'])
                                              -> where('bank_order_id', $parameters['order_id'])
                                              -> where('order_status', '!=', 'paid')
                                              -> count() != 0;

          if ($orderExists && $parameters['status'] == 'success')
          {
            $response['orderPaid'] = true;

            \DB::table('orders') -> where('id', $parameters['shop_order_id']) -> where('bank_order_id', $parameters['order_id']) -> update([
              'order_status' => 'paid',
              'paid_at' => date('Y-m-d H:i:s', time())
            ]);

            $smsProviderFormData = [
              'key' => '45456456fghgfhgf5645654hfghfg',
              'destination' => '591844448',
              'sender' => 'ITWorks.ge',
              'content' => 'გადახდილი შეკვეთის id: ' . $parameters['shop_order_id'],
              'urgent' => true
            ];

            $smsProviderAddress = 'https://smsoffice.ge/api/v2/send/';

            $responseBody = Http::asForm() -> post($smsProviderAddress, $smsProviderFormData) -> body();
          }
        }
      }

      return $response;
    }

    public function getBogClientId()
    {
      return $this -> clientId;
    }
}
