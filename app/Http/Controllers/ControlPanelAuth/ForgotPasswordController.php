<?php

namespace App\Http\Controllers\ControlPanelAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Support\Facades\Validator; // added
use Illuminate\Http\Request; // added
use Illuminate\Http\JsonResponse; // added

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
      $this -> middleware('guest');
    }

    public function broker()
    {
      return \Password::broker('controlPanelUsers');
    }

    protected function sendResetLinkEmail(Request $request)
    {
      $input = $request -> only('email');

      $validator = Validator::make($input, [
        'email' => 'required|max:100|email:filter|exists:control_panel_users'
      ], $this -> validationErrorMessages());

      if (!$validator -> fails())
      {
        $response = \Password::broker('controlPanelUsers') -> sendResetLink($input);

        if ($response !== \Password::RESET_LINK_SENT)
        {
          $validator -> getMessageBag() -> add('unableToSend', 'აღდგენის ლინკი არ გაიგზავნა, გთხოვთ დაიცადოთ');
        }
      }

      return $validator -> messages() -> toJson();
    }

    protected function validationErrorMessages()
    {
        return [
            'email.required' => 'შეავსეთ ელექტრონული ფოსტის ველი',
            'email.email' => 'დაიცავით ელექტრონული ფოსტის ფორმატი',
            'email.max' => 'ელ. ფოსტა უნდა შეიცავდეს მაქსიმუმ :max სიმბოლოს',
            'email.exists' => 'ამ ფოსტით მომხმარებელი არ არსებობს',
        ];
    }

    public function showLinkRequestForm()
    {
      return view('controlPanelAuth.passwords.email');
    }
}
