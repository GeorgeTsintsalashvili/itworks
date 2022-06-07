<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Models\ControlPanel\User;

class UserController extends Controller
{
    public function changePassword(Request $request)
    {
      $parameters = $request -> only(['current-password', 'new-password', 'new-confirm-password']);

      $rules = [
          'current-password' => ['required', 'string', new MatchOldPassword],
          'new-password' => ['required', 'string', 'min:8'],
          'new-confirm-password' => ['required', 'string', 'min:8', 'same:new-password']
      ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        User::find(auth() -> user() -> id) -> update(['password' => Hash::make($parameters['new-password'])]);

        return ['success' => true];
      }

      return ['success' => false];
    }

    public function updateData(Request $request)
    {
      $parameters = $request -> only(['name', 'email', 'notification']);

      $rules = ['name' => 'required|string|min:1|max:200',
                'email' => 'required|string|email|max:200',
                'notification' => 'required|string|min:1|max:2000'];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        User::find(auth() -> user() -> id) -> update(\Arr::except($parameters, ['notification']));

        $visibility = $request -> filled('notification-enabled') ? 1 : 0;

        \DB::table('notification') -> update(['text' => $parameters['notification'], 'visibility' => $visibility]);

        return ['success' => true];
      }

      return ['success' => false];
    }
}
