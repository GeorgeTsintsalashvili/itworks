<?php

namespace App\Http\Controllers\ControlPanelAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Validator; // added
use Illuminate\Http\Request; // added
use Illuminate\Http\JsonResponse; // added

class LoginController extends Controller
{
    use AuthenticatesUsers {

         logout as userLogout;
    }

    public function __construct()
    {
        $this -> middleware('guest') -> except('logout');
    }

    protected function guard()
    {
        return \Auth::guard('controlPanelUser');
    }

    public function logout(Request $request)
    {
        $this -> userLogout($request);

        return redirect() -> route('login');
    }

    protected function login(Request $request)
    {
      $parameters = $request -> only(['email', 'password', 'remember']);

      $validator = $this -> validator($parameters);

      if (!$validator -> fails())
      {
        $authParameters = [
          'email' => $parameters['email'],
          'password' => $parameters['password']
        ];

        $remember = (int) $parameters['remember'];

        if (!auth() -> attempt($authParameters, $remember != 0))
        {
          $validator -> getMessageBag() -> add('invalidUser', 'ამ მონაცემებით მომხმარებელი არ არსებობს');
        }
      }

      return $validator -> messages() -> toJson();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email:filter', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'remember' => ['required', 'integer']
        ], $this -> validationErrorMessages());
    }

    protected function validationErrorMessages()
    {
        return [
            'email.required' => 'შეავსეთ ელექტრონული ფოსტის ველი',
            'password.required' => 'შეავსეთ პაროლის ველი',
            'email.email' => 'დაიცავით ელექტრონული ფოსტის ფორმატი',
            'email.max' => 'ელ. ფოსტა უნდა შეიცავდეს მაქსიმუმ :max სიმბოლოს',
            'password.min' => 'პაროლი უნდა შეიცავდეს მინიმუმ :min სიმბოლოს',
            'password.max' => 'პაროლი უნდა შეიცავდეს მაქსიმუმ :max სიმბოლოს',
        ];
    }

    public function showLoginForm()
    {
      return view('controlPanelAuth.login');
    }
}
