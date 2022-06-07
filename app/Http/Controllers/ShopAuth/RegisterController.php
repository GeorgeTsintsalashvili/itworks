<?php

namespace App\Http\Controllers\ShopAuth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Shop\User; // modified
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use \App\Models\Shop\BaseModel;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/shop/email/verify'; // access through redirectPath()

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this -> middleware('guest:shopUser');
    }

    protected function guard()
    {
        return \Auth::guard('shopUser');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        $this -> validator($request -> all()) -> validate();

        event(new Registered($user = $this -> create($request -> all())));

        $this -> guard() -> login($user);

        return new JsonResponse(['registered' => !$this -> registered($request, $user)], 200);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'min:4', 'max:100'],
            'email' => ['required', 'string', 'email:filter', 'max:100', 'unique:shop_users'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
            'phone' => ['required', 'string', 'min:6', 'max:50'],
        ], $this -> validationErrorMessages());
    }

    protected function validationErrorMessages()
    {
        return [
            'name.required' => 'შეავსეთ სახელისა და გვარის ველი',
            'email.required' => 'შეავსეთ ელექტრონული ფოსტის ველი',
            'password.required' => 'შეავსეთ პაროლის ველები',
            'phone.required' => 'შეავსეთ ტელეფონის ველი',
            'email.email' => 'დაიცავით ელექტრონული ფოსტის ფორმატი',
            'email.unique' => 'ამ ელ. ფოსტით მომხმარებელი უკვე არის დარეგისტრირებული',
            'password.confirmed' => 'პაროლები უნდა ემთხვეოდეს ერთმანეთს',
            'password.min' => 'პაროლი უნდა შეიცავდეს მინიმუმ :min სიმბოლოს',
            'phone.min' => 'ტელეფონის ნომერი უნდა შეიცავდეს მინიმუმ :min სიმბოლოს',
            'name.min' => 'სახელი და გვარი უნდა შეიცავდეს მინიმუმ :min სიმბოლოს',
        ];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone']
        ]);

        return $user;
    }

    public function showRegistrationForm()
    {
      $generalData = BaseModel::getGeneralData();

      return view('shopAuth.register', ['generalData' => $generalData]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */

    protected function registered(Request $request, $user) // optional
    {
        //
    }
}
