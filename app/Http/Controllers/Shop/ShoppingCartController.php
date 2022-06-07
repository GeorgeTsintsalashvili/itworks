<?php

namespace App\Http\Controllers\Shop;

use \App\Http\Controllers as Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\View;
use \App\Helpers\Paginator;

use \App\Models\Shop\BaseModel;
use \App\Models\Shop\ShoppingCart;

class ShoppingCartController extends Controllers\Controller
{
    public function index()
    {
      $generalData = BaseModel::getGeneralData();

      $userId = \Auth::guard('shopUser') -> user() -> id;
      $shoppingCart = \DB::table('shopping_carts') -> select(['id', 'total_price', 'total_discount', 'total_quantity']) -> where('shop_user_id', $userId) -> first();

      $data['shoppingCartIsNotEmpty'] = false;
      $data['totalDiscount'] = 0;
      $data['totalPrice'] = 0;
      $data['numberOfProducts'] = 0;

      if (!empty($shoppingCart))
      {
        $data['totalPrice'] = $shoppingCart -> total_price - $shoppingCart -> total_discount;
        $data['totalDiscount'] = $shoppingCart -> total_discount;
        $data['products'] = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> get();
        $data['numberOfProducts'] = $shoppingCart -> total_quantity;
        $data['shoppingCartIsNotEmpty'] = $data['numberOfProducts'] != 0;
      }

      return View::make('contents.shop.shoppingCart.index', ['contentData' => $data,
                                                             'generalData' => $generalData]);
    }

    public function add(Request $request)
    {
      $data['productAdded'] = false;
      $data['productExistsInShoppingCart'] = false;
      $data['productId'] = -1;
      $data['productExists'] = false;
      $data['cartIsFull'] = false;

      $cartItemsLimit = 40;

      $tablesData = \DB::table('tables') -> get();

      if (!$tablesData -> isEmpty())
      {
        // request data validation logic

        $parameters = $request -> only(['quantity', 'product-id', 'category-id']);

        $validator = \Validator::make($parameters, ['quantity' => 'required|string',
                                                    'product-id' => 'required|string',
                                                    'category-id' => 'required|string']);

        if (!$validator -> fails())
        {
          $productId = abs((int) $parameters['product-id']);
          $quantity = abs((int) $parameters['quantity']);
          $categoryId = $parameters['category-id'];
          $tableData = \DB::table('tables') -> select(['name', 'address_prefix']) -> where('alias', $categoryId) -> first();

          if ($productId && $quantity && $tableData)
          {
            $product = \DB::table($tableData -> name) -> select(['quantity', 'id', 'mainImage', 'price', 'discount', 'title'])
                                                      -> where('id', '=', $productId)
                                                      -> where('quantity', '!=', 0)
                                                      -> first();

            if (!empty($product))
            {
              $productQuantityIsAllowed = $quantity <= $product -> quantity;

              $data['productId'] = $product -> id;
              $data['productExists'] = true;

              if ($productQuantityIsAllowed)
              {
                $userId = \Auth::guard('shopUser') -> user() -> id;

                $shopUserCart = \DB::table('shopping_carts') -> where('shop_user_id', $userId) -> first();

                if (empty($shopUserCart))
                {
                  \DB::table('shopping_carts') -> insert(['shop_user_id' => $userId]);
                }

                $shoppingCartId = \DB::table('shopping_carts') -> select(['id']) -> where('shop_user_id', $userId) -> first() -> id;
                $shoppingCartItemsQuantity = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCartId) -> count();

                if ($shoppingCartItemsQuantity < $cartItemsLimit)
                {
                  $shoppingCartItemCount = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCartId)
                                                                             -> where('product_id', $productId)
                                                                             -> where('product_category_id', $categoryId)
                                                                             -> count();

                  if ($shoppingCartItemCount)
                  {
                    $data['productExistsInShoppingCart'] = true;
                  }

                  else
                  {
                    $productImage = '/images/'.$tableData -> address_prefix.'/main/preview/'.$product -> mainImage;
                    $productRoute = '/'.$tableData -> address_prefix.'/'.$product -> id;

                    \DB::table('shopping_cart_items') -> insert(['shopping_cart_id' => $shoppingCartId,
                                                                 'cart_item_price' => $product -> price * $quantity,
                                                                 'cart_item_discount' => $product -> discount * $quantity,
                                                                 'cart_item_quantity' => $quantity,
                                                                 'quantity_limit' => $product -> quantity,
                                                                 'product_id' => $product -> id,
                                                                 'product_title' => $product -> title,
                                                                 'product_route' => $productRoute,
                                                                 'product_image' => $productImage,
                                                                 'product_category_id' => $categoryId]);

                    $totalPrice = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCartId) -> sum('cart_item_price');
                    $totalDiscount = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCartId) -> sum('cart_item_discount');
                    $totalQuantity = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCartId) -> sum('cart_item_quantity');

                    \DB::table('shopping_carts') -> where('id', $shoppingCartId) -> update(['total_price' => $totalPrice,
                                                                                            'total_discount' => $totalDiscount,
                                                                                            'total_quantity' => $totalQuantity]);

                    $data['productAdded'] = true;
                  }
                }

                else $data['cartIsFull'] = true;
              }
            }
          }
        }
      }

      return response(json_encode($data)) -> header('Content-Type', 'application/json');
    }

    public function remove(Request $request)
    {
      $data['productDeleted'] = false;
      $data['numberOfProductsLeft'] = 0;
      $data['totalPrice'] = 0;
      $data['totalQuantity'] = 0;
      $data['totalDiscount'] = 0;

      $parameters = $request -> only(['product-id', 'category-id']);

      $validator = \Validator::make($parameters, ['product-id' => 'required|string',
                                                  'category-id' => 'required|string']);

      if (!$validator -> fails())
      {
        $paramCategoryId = trim($parameters['category-id']);

        if ($paramCategoryId)
        {
          $tableDataByCategory = \DB::table('tables') -> select(['alias', 'name']) -> where('alias', $paramCategoryId) -> first();

          if (!is_null($tableDataByCategory))
          {
            $paramProductId = abs((int) $parameters['product-id']);

            if ($paramProductId)
            {
              $shoppingCartProduct = \DB::table($tableDataByCategory -> name) -> select(['quantity', 'price']) -> where('id', '=', $paramProductId) -> first();

              if (!is_null($shoppingCartProduct))
              {
                $userId = \Auth::guard('shopUser') -> user() -> id;
                $shoppingCart = \DB::table('shopping_carts') -> select(['id']) -> where('shop_user_id', $userId) -> first();

                if (!is_null($shoppingCart))
                {
                  \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id)
                                                    -> where('product_id', $paramProductId)
                                                    -> where('product_category_id', $paramCategoryId)
                                                    -> delete();

                  $totalPrice = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_price');
                  $totalDiscount = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_discount');
                  $totalQuantity = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_quantity');

                  \DB::table('shopping_carts') -> where('shop_user_id', $userId) -> update(['total_price' => $totalPrice,
                                                                                            'total_discount' => $totalDiscount,
                                                                                            'total_quantity' => $totalQuantity]);

                  $data['productDeleted'] = true;
                  $data['numberOfProductsLeft'] = $totalQuantity;
                  $data['totalPrice'] = $totalPrice - $totalDiscount;
                  $data['totalDiscount'] = $totalDiscount;
                  $data['totalQuantity'] = $totalQuantity;
                }
              }
            }
          }
        }
      }

      return response(json_encode($data)) -> header('Content-Type', 'application/json');
    }

    public function removeAll(Request $request)
    {
      $data['productsDeleted'] = false;

      $userId = \Auth::guard('shopUser') -> user() -> id;

      $shoppingCart = \DB::table('shopping_carts') -> select(['id']) -> where('shop_user_id', $userId) -> first();

      if (!is_null($shoppingCart))
      {
        \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> delete();
        \DB::table('shopping_carts') -> where('shop_user_id', $userId) -> update(['total_price' => 0, 'total_discount' => 0, 'total_quantity' => 0]);

        $data['productsDeleted'] = true;
      }

      return response(json_encode($data)) -> header('Content-Type', 'application/json');
    }

    public function changeQuantity(Request $request)
    {
      $data['quantityChanged'] = false;
      $data['totalPrice'] = 0;
      $data['totalDiscount'] = 0;
      $data['totalQuantity'] = 0;
      $data['productNewPrice'] = 0;
      $data['productDiscount'] = 0;

      // request data validation logic

      $parameters = $request -> only(['quantity', 'product-id', 'category-id']);

      $validator = \Validator::make($parameters, ['quantity' => 'required|string',
                                                  'product-id' => 'required|string',
                                                  'category-id' => 'required|string']);

      if (!$validator -> fails())
      {
        $paramCategoryId = trim($parameters['category-id']);
        $paramProductId = abs((int) $parameters['product-id']);
        $paramQuantity = abs((int) $parameters['quantity']);

        if ($paramProductId && $paramQuantity && $paramCategoryId)
        {
          $tableDataByCategory = \DB::table('tables') -> select(['alias', 'name']) -> where('alias', $paramCategoryId) -> first();

          if (!is_null($tableDataByCategory))
          {
            $tableName = $tableDataByCategory -> name;
            $columns = ['quantity', 'price', 'discount'];

            $product = \DB::table($tableName) -> select($columns) -> where('id', '=', $paramProductId) -> first();

            if (!is_null($product))
            {
              if ($paramQuantity <= $product -> quantity)
              {
                $data['productNewPrice'] = $paramQuantity * ($product -> price - $product -> discount);
                $data['productDiscount'] = $paramQuantity * $product -> discount;

                $userId = \Auth::guard('shopUser') -> user() -> id;
                $shoppingCart = \DB::table('shopping_carts') -> select(['id']) -> where('shop_user_id', $userId) -> first();

                if (!is_null($shoppingCart))
                {
                  \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id)
                                                    -> where('product_id', $paramProductId)
                                                    -> where('product_category_id', $paramCategoryId)
                                                    -> update(['cart_item_price' => $product -> price * $paramQuantity,
                                                               'cart_item_discount' => $data['productDiscount'],
                                                               'cart_item_quantity' => $paramQuantity]);

                  $totalPrice = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_price');
                  $totalDiscount = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_discount');
                  $totalQuantity = \DB::table('shopping_cart_items') -> where('shopping_cart_id', $shoppingCart -> id) -> sum('cart_item_quantity');

                  \DB::table('shopping_carts') -> where('shop_user_id', $userId) -> update(['total_price' => $totalPrice,
                                                                                            'total_discount' => $totalDiscount,
                                                                                            'total_quantity' => $totalQuantity]);

                  $data['quantityChanged'] = true;
                  $data['totalPrice'] = $totalPrice - $totalDiscount;
                  $data['totalDiscount'] = $totalDiscount;
                  $data['totalQuantity'] = $totalQuantity;
                }
              }
            }
          }
        }
      }

      return response(json_encode($data)) -> header('Content-Type', 'application/json');
    }
}
