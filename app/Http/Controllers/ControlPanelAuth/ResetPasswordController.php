<?php

namespace App\Http\Controllers\ControlPanelAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Support\Facades\Validator; // added
use Illuminate\Http\JsonResponse; // added
use Illuminate\Http\Request; // added

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

    protected $redirectTo = '/controlPanel';

    public function __construct()
    {
      $this -> middleware('guest:controlPanelUser');
    }

    protected function guard()
    {
      return \Auth::guard('controlPanelUser');
    }

    public function broker()
    {
      return \Password::broker('controlPanelUsers');
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
      $input = $request -> only(['email', 'password', 'password_confirmation', 'token']);

      $validator = Validator::make($input, $this -> rules(), $this -> validationErrorMessages());

      if (!$validator -> fails())
      {
        $response = $this -> broker() -> reset(
            $this -> credentials($request), function ($user, $password) {
              $this -> resetPassword($user, $password);
            }
        );

        if ($response !== \Password::PASSWORD_RESET)
        {
          $errors = [
            'passwords.token' => 'ტოკენი არაა სწორი ან ამოეწურა მოქმედების ვადა',
            'passwords.user' => 'ამ ელ. ფოსტით მომხმარებელი არ არსებობს',
          ];

          $validator -> getMessageBag() -> add('resetUnable', $errors[$response] ?? 'პაროლის შეცვლა არ შესრულდა');
        }
      }

      return $validator -> messages() -> toJson();
    }

    public function showResetForm(Request $request, $token = null)
    {
      $email = $request -> input('email');

      return view('controlPanelAuth.passwords.reset', ['token' => $token, 'email' => !is_array($email) ? $email : null]);
    }
}
