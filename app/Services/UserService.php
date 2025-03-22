<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    
    public function registerUser(array $data) {
        // datas vadation
        $validator = validator::make($data, [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);

        // check validator
        if ($validator->fails()){
            return [
                'success' => false, 'errors' => $validator->errors()
            ];
        }

        // creation of user
        $user = User::create(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])

            ]
        );

        return [
            'success' => true,
            'user' => $user
        ];

    }
}