<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;


class RegisterController extends Controller
{   
    protected $userService ;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }
     
    public function registrationUser(Request $request) : RedirectResponse {
        // call the service 
        $result = $this->userService->registerUser($request->all());

        // handle sucess request
        if ($result['success']){
            return redirect()->route('login')->with('sucess', 'user created sucessfully');
        }

        return redirect()->route('register')->withErrors($result['errors']);
    }
}
