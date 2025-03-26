<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginService 
{
    // Validate login data (email and password)
    public function validateLoginData(array $data)
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false, 
                'errors' => $validator->errors()
            ];
        }

        return ['success' => true];
    }

    // Check if the provided password matches the hashed password
    public function validatePassword($password, $hashedPassword)
    {
        if (!\Hash::check($password, $hashedPassword)) {
            return [
                'success' => false,
                'errors' => ['password' => 'Incorrect password.']
            ];
        }

        return ['success' => true];
    }

    // Attempt to log in by checking email and password
    public function attemptLogin(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return [
                'success' => false,
                'errors' => ['email' => 'User not found.']
            ];
        }

        // Validate password
        $passwordResult = $this->validatePassword($credentials['password'], $user->password);
        if (!$passwordResult['success']) {
            return $passwordResult;
        }

        return [
            'success' => true,
            'user' => $user
        ];
    }
}
