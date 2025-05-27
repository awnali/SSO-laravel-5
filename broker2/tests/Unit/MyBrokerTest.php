<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\MyBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class MyBrokerTest extends TestCase
{
    use RefreshDatabase;

    protected $broker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set required server variables for SSO broker
        $_SERVER['HTTP_HOST'] = 'localhost:8002';
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        // Mock HTTP responses for SSO server
        Http::fake([
            'localhost:8000/api/server*' => Http::response(['success' => true], 200),
        ]);
    }

    public function test_broker_initialization()
    {
        $broker = new MyBroker();
        
        $this->assertInstanceOf(MyBroker::class, $broker);
    }

    public function test_broker_configuration()
    {
        $broker = new MyBroker();
        
        $reflection = new \ReflectionClass($broker);
        $serverProperty = $reflection->getProperty('server');
        $serverProperty->setAccessible(true);
        $brokerProperty = $reflection->getProperty('broker');
        $brokerProperty->setAccessible(true);
        $secretProperty = $reflection->getProperty('secret');
        $secretProperty->setAccessible(true);

        $this->assertEquals('http://localhost:8000/api/server', $serverProperty->getValue($broker));
        $this->assertEquals('test_broker', $brokerProperty->getValue($broker));
        $this->assertEquals('test_secret', $secretProperty->getValue($broker));
    }

    public function test_get_user_info_wrapper()
    {
        // Mock successful user info response
        Http::fake([
            'localhost:8000/api/server*' => Http::response([
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com'
            ], 200),
        ]);

        $broker = new MyBroker();
        $userInfo = $broker->getUserInfo();

        $this->assertIsArray($userInfo);
        $this->assertEquals(1, $userInfo['id']);
        $this->assertEquals('Test User', $userInfo['name']);
        $this->assertEquals('test@example.com', $userInfo['email']);
    }

    public function test_get_user_info_when_not_logged_in()
    {
        // Mock no content response (not logged in)
        Http::fake([
            'localhost:8000/api/server*' => Http::response('', 204),
        ]);

        $broker = new MyBroker();
        $userInfo = $broker->getUserInfo();

        $this->assertNull($userInfo);
    }

    public function test_login_user_success()
    {
        // Mock successful login response
        Http::fake([
            'localhost:8000/api/server*' => Http::response([
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com'
            ], 200),
        ]);

        $broker = new MyBroker();
        $result = $broker->loginUser('test@example.com', 'password');

        $this->assertTrue($result);
    }

    public function test_login_user_failure()
    {
        // Mock failed login response
        Http::fake([
            'localhost:8000/api/server*' => Http::response(['error' => 'Invalid credentials'], 401),
        ]);

        $broker = new MyBroker();
        $result = $broker->loginUser('test@example.com', 'wrong_password');

        $this->assertFalse($result);
    }

    public function test_session_token_generation()
    {
        $broker = new MyBroker();
        
        $reflection = new \ReflectionClass($broker);
        $method = $reflection->getMethod('getSessionID');
        $method->setAccessible(true);

        $sessionId1 = $method->invoke($broker);
        $sessionId2 = $method->invoke($broker);

        $this->assertIsString($sessionId1);
        $this->assertIsString($sessionId2);
        // Session ID should be consistent for the same instance
        $this->assertEquals($sessionId1, $sessionId2);
    }

    public function test_request_url_generation()
    {
        $broker = new MyBroker();
        
        $reflection = new \ReflectionClass($broker);
        $method = $reflection->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $url = $method->invoke($broker, 'userInfo', []);

        $this->assertStringContains('http://localhost:8000/api/server', $url);
        $this->assertStringContains('command=userInfo', $url);
        $this->assertStringContains('broker=test_broker', $url);
    }

    public function test_attachment_status()
    {
        $broker = new MyBroker();
        
        $reflection = new \ReflectionClass($broker);
        $method = $reflection->getMethod('isAttached');
        $method->setAccessible(true);

        $isAttached = $method->invoke($broker);

        $this->assertIsBool($isAttached);
    }
}