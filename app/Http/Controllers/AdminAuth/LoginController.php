<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Session;
use Log;

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

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        $this->middleware('admin.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    { 
        return Auth::guard('admin');
    }

    protected function redirectTo()
    {
        if (trim(Auth::guard('admin')->user()->role) == 'super_admin') {
            return '/admin/dashboard';

        } else if (trim(Auth::guard('admin')->user()->role) == 'admin') {
            return '/admin/dashboard';

        } else if (trim(Auth::guard('admin')->user()->role) == 'inventory') {
            return '/admin/inventorydashboard';

        }else if (empty(trim(Auth::guard('admin')->user()->role))){
            return '/admin/unassigned_role';

        } else {
            return '/admin/dashboard';
        }
    }
}
