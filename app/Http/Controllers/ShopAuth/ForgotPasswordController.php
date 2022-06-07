<?php

namespace App\Http\Controllers\ShopAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
      $this -> middleware('guest:shopUser');
    }

    public function broker()
    {
      return \Password::broker('shopUsers');
    }

    protected function sendResetLinkEmail(Request $request)
    {
      $input = $request -> only('email');

      $validator = \Validator::make($input, [
        'email' => 'required|email|exists:shop_users'
      ]);

      if ($validator -> fails())
      {
        return response(['errorCode' => 1], 200);
      }

      $response = \Password::broker('shopUsers') -> sendResetLink($input);

      $errorCode = $response == \Password::RESET_LINK_SENT ? 0 : 2;

      if ($errorCode == 0)
      {
        session() -> regenerate();
      }

      return response(['errorCode' => $errorCode], 200);
    }

    public function showLinkRequestForm()
    {
      return view('shopAuth.passwords.email');
    }
}
