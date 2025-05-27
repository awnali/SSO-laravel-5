<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\MyBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SSOAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user in the local database
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_home_page_redirects_when_not_authenticated()
    {
        $response = $this->get('/home');
        
        $response->assertRedirect('/login');
    }

    public function test_successful_sso_login()
    {
        // Mock successful SSO server responses
        Http::fake([
            'localhost:8000/api/server*' => Http::sequence()
                ->push(['success' => true], 200) // attach
                ->push(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'], 200) // login
                ->push(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'], 200), // getUserInfo
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();
    }

    public function test_failed_sso_login()
    {
        // Mock failed SSO server response
        Http::fake([
            'localhost:8000/api/server*' => Http::sequence()
                ->push(['success' => true], 200) // attach
                ->push(['error' => 'Invalid credentials'], 401), // login failure
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_logout_functionality()
    {
        // First login
        $user = User::where('email', 'test@example.com')->first();
        $this->actingAs($user);

        // Mock SSO server logout response
        Http::fake([
            'localhost:8000/api/server*' => Http::response(['success' => true], 200),
        ]);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_auto_login_when_sso_session_exists()
    {
        // Mock SSO server response indicating user is already logged in
        Http::fake([
            'localhost:8000/api/server*' => Http::response([
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com'
            ], 200),
        ]);

        // Visit a protected route
        $response = $this->get('/home');

        // Should be redirected to home after auto-login
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    public function test_dashboard_access_after_login()
    {
        $user = User::where('email', 'test@example.com')->first();
        $this->actingAs($user);

        $response = $this->get('/home');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertSee('Test User');
    }

    public function test_login_validation_errors()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_with_invalid_email_format()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_remember_me_functionality()
    {
        // Mock successful SSO server responses
        Http::fake([
            'localhost:8000/api/server*' => Http::sequence()
                ->push(['success' => true], 200) // attach
                ->push(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'], 200) // login
                ->push(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'], 200), // getUserInfo
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();
        
        // Check if remember token is set
        $user = Auth::user();
        $this->assertNotNull($user->remember_token);
    }

    public function test_middleware_protection()
    {
        // Test that auth middleware protects routes
        $response = $this->get('/home');
        $response->assertRedirect('/login');

        // Test that guest middleware redirects authenticated users
        $user = User::where('email', 'test@example.com')->first();
        $this->actingAs($user);
        
        $response = $this->get('/login');
        $response->assertRedirect('/home');
    }

    public function test_sso_broker_error_handling()
    {
        // Mock SSO server error response
        Http::fake([
            'localhost:8000/api/server*' => Http::response(['error' => 'Server error'], 500),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
    }

    public function test_user_creation_from_sso_data()
    {
        // Delete the existing test user to test creation
        User::where('email', 'newuser@example.com')->delete();

        // Mock successful SSO server responses for new user
        Http::fake([
            'localhost:8000/api/server*' => Http::sequence()
                ->push(['success' => true], 200) // attach
                ->push(['id' => 2, 'name' => 'New User', 'email' => 'newuser@example.com'], 200) // login
                ->push(['id' => 2, 'name' => 'New User', 'email' => 'newuser@example.com'], 200), // getUserInfo
        ]);

        $response = $this->post('/login', [
            'email' => 'newuser@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated();
        
        // Verify user was created locally
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);
    }
}