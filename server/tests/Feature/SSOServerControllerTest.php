<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class SSOServerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_server_responds_to_unknown_command()
    {
        $response = $this->get('/api/server');
        
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Unknown command']);
    }

    public function test_server_responds_to_invalid_command()
    {
        $response = $this->get('/api/server?command=invalid_command');
        
        $response->assertStatus(404);
        $response->assertJson(['error' => 'Unknown command']);
    }

    public function test_attach_command_with_valid_broker()
    {
        $response = $this->get('/api/server?command=attach&broker=broker1&token=test_token');
        
        $response->assertStatus(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function test_attach_command_with_invalid_broker()
    {
        $response = $this->get('/api/server?command=attach&broker=invalid_broker&token=test_token');
        
        $response->assertStatus(403);
    }

    public function test_login_command_with_valid_credentials()
    {
        // First attach a broker
        $attachResponse = $this->get('/api/server?command=attach&broker=broker1&token=test_token');
        $attachResponse->assertStatus(200);

        // Then attempt login
        $response = $this->post('/api/server', [
            'command' => 'login',
            'username' => 'test@example.com',
            'password' => 'password',
            'broker' => 'broker1',
            'token' => 'test_token'
        ]);
        
        $response->assertStatus(200);
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($this->testUser->email, $responseData['email']);
    }

    public function test_login_command_with_invalid_credentials()
    {
        // First attach a broker
        $attachResponse = $this->get('/api/server?command=attach&broker=broker1&token=test_token');
        $attachResponse->assertStatus(200);

        // Then attempt login with wrong password
        $response = $this->post('/api/server', [
            'command' => 'login',
            'username' => 'test@example.com',
            'password' => 'wrong_password',
            'broker' => 'broker1',
            'token' => 'test_token'
        ]);
        
        $response->assertStatus(401);
    }

    public function test_userinfo_command_without_login()
    {
        // First attach a broker
        $attachResponse = $this->get('/api/server?command=attach&broker=broker1&token=test_token');
        $attachResponse->assertStatus(200);

        // Try to get user info without logging in
        $response = $this->get('/api/server?command=userInfo&broker=broker1&token=test_token');
        
        $response->assertStatus(204); // No content when not logged in
    }

    public function test_complete_sso_flow()
    {
        $token = 'test_token_' . uniqid();
        
        // Step 1: Attach broker
        $attachResponse = $this->get("/api/server?command=attach&broker=broker1&token={$token}");
        $attachResponse->assertStatus(200);

        // Step 2: Login
        $loginResponse = $this->post('/api/server', [
            'command' => 'login',
            'username' => 'test@example.com',
            'password' => 'password',
            'broker' => 'broker1',
            'token' => $token
        ]);
        $loginResponse->assertStatus(200);

        // Step 3: Get user info
        $userInfoResponse = $this->get("/api/server?command=userInfo&broker=broker1&token={$token}");
        $userInfoResponse->assertStatus(200);
        
        $userData = json_decode($userInfoResponse->getContent(), true);
        $this->assertEquals($this->testUser->email, $userData['email']);
        $this->assertEquals($this->testUser->name, $userData['name']);

        // Step 4: Logout
        $logoutResponse = $this->post('/api/server', [
            'command' => 'logout',
            'broker' => 'broker1',
            'token' => $token
        ]);
        $logoutResponse->assertStatus(200);

        // Step 5: Verify user is logged out
        $userInfoAfterLogout = $this->get("/api/server?command=userInfo&broker=broker1&token={$token}");
        $userInfoAfterLogout->assertStatus(204); // No content after logout
    }

    public function test_multiple_broker_sessions()
    {
        $token1 = 'broker1_token_' . uniqid();
        $token2 = 'broker2_token_' . uniqid();
        
        // Attach both brokers
        $attach1 = $this->get("/api/server?command=attach&broker=broker1&token={$token1}");
        $attach1->assertStatus(200);
        
        $attach2 = $this->get("/api/server?command=attach&broker=broker2&token={$token2}");
        $attach2->assertStatus(200);

        // Login with broker1
        $login1 = $this->post('/api/server', [
            'command' => 'login',
            'username' => 'test@example.com',
            'password' => 'password',
            'broker' => 'broker1',
            'token' => $token1
        ]);
        $login1->assertStatus(200);

        // Check if broker2 can access user info (SSO should work)
        $userInfo2 = $this->get("/api/server?command=userInfo&broker=broker2&token={$token2}");
        $userInfo2->assertStatus(200);
        
        $userData = json_decode($userInfo2->getContent(), true);
        $this->assertEquals($this->testUser->email, $userData['email']);
    }

    public function test_centralized_logout()
    {
        $token1 = 'broker1_token_' . uniqid();
        $token2 = 'broker2_token_' . uniqid();
        
        // Setup both brokers and login
        $this->get("/api/server?command=attach&broker=broker1&token={$token1}");
        $this->get("/api/server?command=attach&broker=broker2&token={$token2}");
        
        $this->post('/api/server', [
            'command' => 'login',
            'username' => 'test@example.com',
            'password' => 'password',
            'broker' => 'broker1',
            'token' => $token1
        ]);

        // Verify both brokers have access
        $userInfo1 = $this->get("/api/server?command=userInfo&broker=broker1&token={$token1}");
        $userInfo1->assertStatus(200);
        
        $userInfo2 = $this->get("/api/server?command=userInfo&broker=broker2&token={$token2}");
        $userInfo2->assertStatus(200);

        // Logout from broker1
        $logout = $this->post('/api/server', [
            'command' => 'logout',
            'broker' => 'broker1',
            'token' => $token1
        ]);
        $logout->assertStatus(200);

        // Verify both brokers are logged out
        $userInfoAfter1 = $this->get("/api/server?command=userInfo&broker=broker1&token={$token1}");
        $userInfoAfter1->assertStatus(204);
        
        $userInfoAfter2 = $this->get("/api/server?command=userInfo&broker=broker2&token={$token2}");
        $userInfoAfter2->assertStatus(204);
    }
}