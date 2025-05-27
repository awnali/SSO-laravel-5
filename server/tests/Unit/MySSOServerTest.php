<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\MySSOServer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MySSOServerTest extends TestCase
{
    use RefreshDatabase;

    protected $ssoServer;
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ssoServer = new MySSOServer();
        
        // Create a test user
        $this->testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_broker_validation_with_valid_broker()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('getBrokerInfo');
        $method->setAccessible(true);

        $brokerInfo = $method->invoke($this->ssoServer, 'broker1');
        
        $this->assertIsArray($brokerInfo);
        $this->assertEquals('broker1_secret', $brokerInfo['secret']);
    }

    public function test_broker_validation_with_invalid_broker()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('getBrokerInfo');
        $method->setAccessible(true);

        $brokerInfo = $method->invoke($this->ssoServer, 'invalid_broker');
        
        $this->assertNull($brokerInfo);
    }

    public function test_user_authentication_with_valid_credentials()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);

        $result = $method->invoke($this->ssoServer, 'test@example.com', 'password');
        
        $this->assertInstanceOf(\Jasny\ValidationResult::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEmpty($result->getErrors());
    }

    public function test_user_authentication_with_invalid_credentials()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);

        $result = $method->invoke($this->ssoServer, 'test@example.com', 'wrong_password');
        
        $this->assertInstanceOf(\Jasny\ValidationResult::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertNotEmpty($result->getErrors());
    }

    public function test_user_authentication_with_nonexistent_user()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);

        $result = $method->invoke($this->ssoServer, 'nonexistent@example.com', 'password');
        
        $this->assertInstanceOf(\Jasny\ValidationResult::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertNotEmpty($result->getErrors());
    }

    public function test_get_user_info()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('getUserInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->ssoServer, 'test@example.com');
        
        $this->assertIsArray($result);
        $this->assertEquals($this->testUser->id, $result['id']);
        $this->assertEquals($this->testUser->name, $result['name']);
        $this->assertEquals($this->testUser->email, $result['email']);
    }

    public function test_get_user_info_nonexistent()
    {
        $reflection = new \ReflectionClass($this->ssoServer);
        $method = $reflection->getMethod('getUserInfo');
        $method->setAccessible(true);

        $result = $method->invoke($this->ssoServer, 'nonexistent@example.com');
        
        $this->assertNull($result);
    }

    public function test_get_user_by_id()
    {
        $result = $this->ssoServer->getUserById($this->testUser->id);
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->testUser->id, $result->id);
        $this->assertEquals($this->testUser->email, $result->email);
    }

    public function test_get_user_by_invalid_id()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        $this->ssoServer->getUserById(99999);
    }
}