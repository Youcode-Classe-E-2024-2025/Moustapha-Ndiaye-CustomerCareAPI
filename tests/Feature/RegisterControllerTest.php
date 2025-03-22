<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService; 


class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(){
        $data = [
            'name' => 'qwerty',
            'email' => 'qwerty@exemple.com',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123'
        ];

        // post request similation
        $response = $this->post(route('registrationUser.store'), $data);

        // redirect to 
        $response->assertRedirect(route('login'));

        //check creation of user
        $this->assertDatabaseHas('users', [
            'email' => 'qwerty@exemple.com'
        ]);
    }


    public function test_it_redirects_back_with_errors_when_invalid_data_is_provided(){
        $data = [
            'name' => 'qwerty',
            'email' => 'qwerty@exemple.com',
            'password' => 'qwerty1231234',
            'password_confirmation' => 'qwerty13'
        ];

        // post request similation
        $response = $this->post(route('registrationUser.store'), $data);

        // redirect to 
        $response->assertRedirect(route('register'));

        //check creation of user
        $response->assertSessionHasErrors(['password']);
    }
}
