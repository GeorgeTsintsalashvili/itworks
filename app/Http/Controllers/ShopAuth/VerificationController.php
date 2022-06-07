<?php

namespace App\Http\Controllers\ShopAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\Shop\User;
use Illuminate\Http\JsonResponse;
use App\Models\Shop\BaseModel;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */

    protected $redirectTo = '/shop/email/verified';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this -> middleware('shopAuth');
        $this -> middleware('signed.shopUser') -> only('verify');
        $this -> middleware('throttle:6,1') -> only('verify', 'resend');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */

    public function resend(Request $request)
    {
        if ($request -> user('shopUser') -> hasVerifiedEmail())
        {
          return $request -> wantsJson() ? new JsonResponse(['emailAlreadyVerified' => true], 202) : redirect($this -> redirectPath());
        }

        $request -> user('shopUser') -> sendEmailVerificationNotification();

        return $request -> wantsJson() ? new JsonResponse(['verificationLinkIsResent' => true], 202) : back() -> with('resent', true);
    }

    public function verify(Request $request)
    {
        $user = User::find($request -> id);

        if ($request -> route('id') != $user -> getKey())
        {
          throw new AuthorizationException;
        }

        if ($user -> markEmailAsVerified())
        {
          event(new Verified($user));
        }

        return redirect($this -> redirectPath()) -> with('verified', true);
    }

    public function show()
    {
      if (!\Auth::guard('shopUser') -> user() -> hasVerifiedEmail())
      {
        $generalData = BaseModel::getGeneralData();

        return view('shopAuth.verify', ['generalData' => $generalData]);
      }

      return redirect(route('home'));
    }

    public function showVerificationSuccessPage() // custom method
    {
      $generalData = BaseModel::getGeneralData();

      return view('shopAuth.verificationSuccess', ['generalData' => $generalData]);
    }

    public function showInvalidSignaturePage()
    {
      $generalData = BaseModel::getGeneralData();

      return view('shopAuth.invalidSignature', ['generalData' => $generalData]);
    }
}
