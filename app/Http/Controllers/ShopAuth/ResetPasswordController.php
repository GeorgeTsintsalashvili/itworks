<?php

namespace App\Http\Controllers\ShopAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use \App\Models\Shop\BaseModel;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

    protected $redirectTo = '/';

    public function __construct()
    {
      $this -> middleware('guest:shopUser');
    }

    protected function guard()
    {
      return \Auth::guard('shopUser');
    }

    public function broker()
    {
      return \Password::broker('shopUsers');
    }

    protected function rules()
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email:filter',
            'password' => 'required|confirmed|min:8',
        ];
    }

    protected function validationErrorMessages()
    {
        return [
            'email.required' => 'შეავსეთ ელექტრონული ფოსტის ველი',
            'password.required' => 'შეავსეთ პაროლის ველები',
            'token.required' => 'პაროლის გადაყენების ტოკენი არის ცარიელი',
            'email.email' => 'დაიცავით ელექტრონული ფოსტის ფორმატი',
            'password.confirmed' => 'პაროლები უნდა ემთხვეოდეს ერთმანეთს',
            'password.min' => 'პაროლი უნდა შედგებოდეს მინიმუმ :min სიმბოლოსგან'
        ];
    }

    public function reset(Request $request)
    {
      $request -> validate($this -> rules(), $this -> validationErrorMessages());

      $response = $this -> broker() -> reset(
          $this -> credentials($request), function ($user, $password) {
            $this -> resetPassword($user, $password);
          }
      );

      if ($response == \Password::PASSWORD_RESET)
      {
        return new JsonResponse(['passwordChanged' => trans($response), 'redirect' => '/'], 200);
      }

      return new JsonResponse(['errors' => ['key' => [trans($response)]]], 422);
    }

    public function showResetForm(Request $request, $token = null)
    {
      $generalData = BaseModel::getGeneralData();

      $email = $request -> input('email');

      return view('shopAuth.passwords.reset', ['token' => $token,
                                               'email' => is_string($email) ? $email : null,
                                               'generalData' => $generalData]);
    }
}
