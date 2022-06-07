<?php

namespace App\Models\Shop;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Notifications\ShopUserResetPasswordNotification;
use App\Notifications\ShopUserVerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
  use Notifiable;

  /**
   * Send the password reset notification.
   *
   * @param  string  $token
   * @return void
   */

  protected $guard = 'shopUser';
  protected $table = 'shop_users';

  public function sendPasswordResetNotification($token)
  {
      $this -> notify(new ShopUserResetPasswordNotification($token));
  }

  public function sendEmailVerificationNotification() // customized
  {
    // handle Swift_TransportException exception

    try
    {
      $this -> notify(new ShopUserVerifyEmailNotification());
    }

    catch(\Exception $e)
    {
      if (\Request::route() -> getName() == 'shop.register')
      {
        \DB::table('shop_users') -> where('email', request('email')) -> delete();
      }

      return abort(response() -> json(['mailServerFault' => true], 419));
    }
  }

  public function cartItemsQuantity() // eloquent method
  {
    $shoppingCart = ShoppingCart::where('shop_user_id', $this -> id) -> first();

    return $shoppingCart ? $shoppingCart -> total_quantity : 0;
  }

  public function shoppingCart()
  {
    return ShoppingCart::where('shop_user_id', $this -> id) -> first();
  }

  public function orders()
  {
    return Order::where('shop_user_id', $this -> id) -> get();
  }

  public function messageBox()
  {
    return MessageBox::where('shop_user_id', $this -> id) -> first();
  }

  protected $fillable = [
      'name', 'email', 'phone', 'password',
  ];

  protected $hidden = [
      'password', 'remember_token'
  ];

  protected $casts = [
      'email_verified_at' => 'datetime',
  ];
}
