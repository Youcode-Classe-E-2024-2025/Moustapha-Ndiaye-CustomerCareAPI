<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoginService;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService){
        $this->loginService = $loginService;
    }

    public function login(Request $request){
        // validate login data
        $result = $this->loginService->validateLoginData($request->all());

        if (!$result['success']){
            return response()->json($result, 422);
        }

        // attempt login
        $loginResult = $this->loginService->attemptLogin($request->only('email', 'password'));

        if (!$loginResult['success']){
            return response()->json($loginResult, 401);
        }

        // authentificate user 
        Auth::login($loginResult['user']);

        // return response()->json(
        //     [
        //         'success' => true,
        //         'message' => 'Login Successful',
        //         'user' => $loginResult['user']
        //     ]
        // );
         // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin');
        }

        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard')->with('success', 'Welcome Agent');
        }

        // Default redirect for 'client'
        return redirect()->route('client.dashboard')->with('success', 'Welcome Client');

    }
}
