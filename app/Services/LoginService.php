<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class LoginService 
{
    public function validateLoginData(array $data){

        $validator = Validator::make($data,[
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($validator->fails()){
            return [
                'success' => false, 'errors' => $validator->errors()
            ];
        }

        return ['success' => true];
    }

    public function validatePassword($password, $hashedPassword){
        if (!\Hash::check($password, $hashedPassword)){
            return [
                'success' => false,
                'errors' => ['password' => 'Incorrect password.']
            ];
        }
        return ['success' => true];
    }
}