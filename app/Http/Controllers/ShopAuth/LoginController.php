<?php

namespace App\Http\Controllers\ShopAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\Models\Shop\User;
use App\Models\Shop\BaseModel;

class LoginController extends Controller
{
    use AuthenticatesUsers {

         logout as userLogout;
    }

    public function __construct()
    {
        $this -> middleware('guest:shopUser') -> except('logout');
    }

    protected function guard()
    {
        return \Auth::guard('shopUser');
    }

    public function logout(Request $request)
    {
        $this -> userLogout($request);

        return redirect() -> route('home');
    }

    protected function login(Request $request)
    {
      $rules = ['email' => 'required|email:filter', 'password' => 'required|string|min:8', 'remember' => 'required|string'];
      $inputData = $request -> only(['email', 'password', 'remember']);
      $validator = \Validator::make($inputData, $rules);
      $response = ['error' => 0, 'redirect' => '/'];

      if (!$validator -> fails())
      {
        $parameters = ['email' => $request -> input('email'), 'password' => $request -> input('password')];

        $remember = (int) $inputData['remember'];

        if (!\Auth::guard('shopUser') -> attempt($parameters, $remember))
        {
           $response = ['error' => 2];
        }

        else
        {
          $shopUser = User::find(\Auth::guard('shopUser') -> user() -> id);

          $shopUser -> last_session_ip = $_SERVER['REMOTE_ADDR'];

          $shopUser -> save();
        }
      }

      else $response = ['error' => 1];

      return $response;
    }

    public function showLoginForm()
    {
      $generalData = BaseModel::getGeneralData();

      return view('shopAuth.login', ['generalData' => $generalData]);
    }
}
