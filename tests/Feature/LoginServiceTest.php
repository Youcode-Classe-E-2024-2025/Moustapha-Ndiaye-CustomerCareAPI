<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LoginService;
use Illuminate\Support\Facades\Validator;

class LoginServiceTest extends TestCase
{
    protected $loginService;

    // Set up the test by initializing the LoginService instance
    public function setUp(): void
    {
        parent::setUp();

        // Instantiate the LoginService
        $this->loginService = new LoginService();
    }

    // Test for valid login data
    public function test_validateLoginData_valid_data()
    {
        // Valid data
        $data = [
            'email' => 'user@example.com',
            'password' => 'password123'
        ];

        // Call the validateLoginData method
        $result = $this->loginService->validateLoginData($data);

        // Assert that the validation is successful
        $this->assertTrue($result['success']);
    }

    // Test for invalid email in login data
    public function test_validateLoginData_invalid_email()
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'qwerty123',
        ];

        // Call the validateLoginData method
        $result = $this->loginService->validateLoginData($data);

        // Assert that validation failed
        $this->assertFalse($result['success']);

        // Convert the errors to an array and assert that the 'email' key exists
        $errors = $result['errors']->toArray();
        $this->assertArrayHasKey('email', $errors);
    }


    // Test for correct password validation
    public function test_validatePassword_correct_password()
    {
        // Hash the password
        $hashedPassword = \Hash::make('password123');
        $password = 'password123';

        // Call the validatePassword method
        $result = $this->loginService->validatePassword($password, $hashedPassword);

        // Assert that the password is correct
        $this->assertTrue($result['success']);
    }

    // Test for incorrect password validation
    public function test_validatePassword_incorrect_password()
    {
        // Hash the correct password
        $hashedPassword = \Hash::make('password123');
        $password = 'wrongpassword';

        // Call the validatePassword method
        $result = $this->loginService->validatePassword($password, $hashedPassword);

        // Assert that the password is incorrect
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('password', $result['errors']);
        
    }
}
