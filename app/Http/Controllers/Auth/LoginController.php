<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (auth()->attempt(array(
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ), true)) {
                if (in_array(4, Auth::user()->users_roles_id()) || in_array(1, Auth::user()->users_roles_id()) || in_array(5, Auth::user()->users_roles_id())) {
                    return redirect()->route('home');
                } else {
                    Auth::logout();
                    return redirect('/login')->withErrors(['email' => 'Only admin is allowed to login.']);
                }
            }
            return redirect()->back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        return response()->json(['error' => $validator->errors()->all()]);
    }
}
