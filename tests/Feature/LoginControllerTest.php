<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\LoginService;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $loginService;
    protected $loginController;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock LoginService
        $this->loginService = Mockery::mock(LoginService::class);
        $this->loginController = new LoginController($this->loginService);
    }

    public function test_login_success()
    {
        // Create a fake user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Mock validation success
        $this->loginService
            ->shouldReceive('validateLoginData')
            ->once()
            ->andReturn(['success' => true]);

        // Mock login attempt success
        $this->loginService
            ->shouldReceive('attemptLogin')
            ->once()
            ->andReturn(['success' => true, 'user' => $user]);

        // Create a request
        $request = Request::create('/login', 'POST', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        // Call login method
        $response = $this->loginController->login($request);

        // Assert response is success
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('success', $response->getData(true));
        $this->assertTrue($response->getData(true)['success']);
    }

    public function test_login_invalid_data()
    {
        // Mock validation failure
        $this->loginService
            ->shouldReceive('validateLoginData')
            ->once()
            ->andReturn(['success' => false, 'errors' => ['email' => 'Invalid email format']]);

        // Create request with invalid data
        $request = Request::create('/login', 'POST', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        // Call login method
        $response = $this->loginController->login($request);

        // Assert validation error response
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('success', $response->getData(true));
        $this->assertFalse($response->getData(true)['success']);
        $this->assertArrayHasKey('email', $response->getData(true)['errors']);
    }

    public function test_login_invalid_credentials()
    {
        // Mock validation success
        $this->loginService
            ->shouldReceive('validateLoginData')
            ->once()
            ->andReturn(['success' => true]);

        // Mock login attempt failure
        $this->loginService
            ->shouldReceive('attemptLogin')
            ->once()
            ->andReturn(['success' => false, 'errors' => ['password' => 'Incorrect password']]);

        // Create request with wrong password
        $request = Request::create('/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]);

        // Call login method
        $response = $this->loginController->login($request);

        // Assert authentication failure response
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('success', $response->getData(true));
        $this->assertFalse($response->getData(true)['success']);
        $this->assertArrayHasKey('password', $response->getData(true)['errors']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
