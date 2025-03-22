<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_success(){
        // datas for testing 
        $data = [
            'name' => 'amdy mstfa',
            'email' => 'amdymstfa@gmail.com',
            'password' => 'blabla',
            'password_confirmation' => 'blabla'
        ];

        // use service 
        $service = new UserService() ;
        $result = $service->registerUser($data);

        // check succes of recording a user 
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('amdy mstfa', $result['user']->name);
        $this->assertEquals('amdymstfa@gmail.com', $result['user']->email);

    }

    public function test_user_registration_failure_due_to_missing_fields(){
        // datas for testing 
        $data = [
            'name' => 'amdy mstfa',
            'password' => 'blabla',
        ];

        // use service 
        $service = new UserService() ;
        $result = $service->registerUser($data);

        // check succes of recording a user 
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('email', $result['errors']->toArray());

    }
}
