<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Integration tests for the complete SSO system
 * 
 * These tests require all three applications to be running:
 * - Server: http://localhost:8000
 * - Broker1: http://localhost:8001  
 * - Broker2: http://localhost:8002
 * 
 * Run with: php vendor/bin/phpunit tests/Integration/SSOIntegrationTest.php
 */
class SSOIntegrationTest extends TestCase
{
    protected $client;
    protected $serverUrl = 'http://localhost:8000';
    protected $broker1Url = 'http://localhost:8001';
    protected $broker2Url = 'http://localhost:8002';
    protected $testUser = [
        'email' => 'test@example.com',
        'password' => 'password'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client([
            'timeout' => 10,
            'allow_redirects' => false,
            'cookies' => true,
        ]);
    }

    public function test_all_applications_are_running()
    {
        // Test server is running
        try {
            $response = $this->client->get($this->serverUrl);
            $this->assertContains($response->getStatusCode(), [200, 302]);
        } catch (RequestException $e) {
            $this->fail("SSO Server is not running at {$this->serverUrl}");
        }

        // Test broker1 is running
        try {
            $response = $this->client->get($this->broker1Url);
            $this->assertContains($response->getStatusCode(), [200, 302]);
        } catch (RequestException $e) {
            $this->fail("Broker1 is not running at {$this->broker1Url}");
        }

        // Test broker2 is running
        try {
            $response = $this->client->get($this->broker2Url);
            $this->assertContains($response->getStatusCode(), [200, 302]);
        } catch (RequestException $e) {
            $this->fail("Broker2 is not running at {$this->broker2Url}");
        }
    }

    public function test_sso_server_api_endpoints()
    {
        // Test attach endpoint
        $response = $this->client->get($this->serverUrl . '/api/server?command=attach&broker=broker1&token=test_token');
        $this->assertEquals(200, $response->getStatusCode());

        // Test userInfo endpoint (should return 204 when not logged in)
        $response = $this->client->get($this->serverUrl . '/api/server?command=userInfo&broker=broker1&token=test_token');
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_broker_login_pages_accessible()
    {
        // Test broker1 login page
        $response = $this->client->get($this->broker1Url . '/login');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContains('login', strtolower($response->getBody()->getContents()));

        // Test broker2 login page
        $response = $this->client->get($this->broker2Url . '/login');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContains('login', strtolower($response->getBody()->getContents()));
    }

    public function test_complete_sso_flow()
    {
        // Step 1: Login to broker1
        $this->loginToBroker($this->broker1Url);

        // Step 2: Verify we can access broker1 dashboard
        $response = $this->client->get($this->broker1Url . '/home');
        $this->assertEquals(200, $response->getStatusCode());
        $dashboardContent = $response->getBody()->getContents();
        $this->assertStringContains('Test User', $dashboardContent);

        // Step 3: Access broker2 (should auto-login via SSO)
        $response = $this->client->get($this->broker2Url . '/home');
        $this->assertEquals(200, $response->getStatusCode());
        $broker2Content = $response->getBody()->getContents();
        $this->assertStringContains('Test User', $broker2Content);

        // Step 4: Logout from broker1
        $this->logoutFromBroker($this->broker1Url);

        // Step 5: Verify we're logged out from both brokers
        $response = $this->client->get($this->broker1Url . '/home');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/login', $response->getHeader('Location')[0]);

        $response = $this->client->get($this->broker2Url . '/home');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/login', $response->getHeader('Location')[0]);
    }

    public function test_reverse_sso_flow()
    {
        // Step 1: Login to broker2 first
        $this->loginToBroker($this->broker2Url);

        // Step 2: Verify we can access broker2 dashboard
        $response = $this->client->get($this->broker2Url . '/home');
        $this->assertEquals(200, $response->getStatusCode());

        // Step 3: Access broker1 (should auto-login via SSO)
        $response = $this->client->get($this->broker1Url . '/home');
        $this->assertEquals(200, $response->getStatusCode());
        $broker1Content = $response->getBody()->getContents();
        $this->assertStringContains('Test User', $broker1Content);

        // Step 4: Logout from broker2
        $this->logoutFromBroker($this->broker2Url);

        // Step 5: Verify centralized logout worked
        $response = $this->client->get($this->broker1Url . '/home');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/login', $response->getHeader('Location')[0]);
    }

    public function test_invalid_credentials()
    {
        // Try to login with wrong credentials
        $response = $this->client->get($this->broker1Url . '/login');
        $this->assertEquals(200, $response->getStatusCode());
        
        $loginPage = $response->getBody()->getContents();
        $csrfToken = $this->extractCsrfToken($loginPage);

        $response = $this->client->post($this->broker1Url . '/login', [
            'form_params' => [
                '_token' => $csrfToken,
                'email' => $this->testUser['email'],
                'password' => 'wrong_password',
            ]
        ]);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/login', $response->getHeader('Location')[0]);
    }

    public function test_session_isolation_between_different_browsers()
    {
        // Create a second client to simulate different browser
        $client2 = new Client([
            'timeout' => 10,
            'allow_redirects' => false,
            'cookies' => true,
        ]);

        // Login with first client
        $this->loginToBroker($this->broker1Url);

        // Verify first client can access dashboard
        $response = $this->client->get($this->broker1Url . '/home');
        $this->assertEquals(200, $response->getStatusCode());

        // Verify second client cannot access dashboard (different session)
        $response = $client2->get($this->broker1Url . '/home');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/login', $response->getHeader('Location')[0]);
    }

    protected function loginToBroker($brokerUrl)
    {
        // Get login page and extract CSRF token
        $response = $this->client->get($brokerUrl . '/login');
        $this->assertEquals(200, $response->getStatusCode());
        
        $loginPage = $response->getBody()->getContents();
        $csrfToken = $this->extractCsrfToken($loginPage);

        // Submit login form
        $response = $this->client->post($brokerUrl . '/login', [
            'form_params' => [
                '_token' => $csrfToken,
                'email' => $this->testUser['email'],
                'password' => $this->testUser['password'],
            ]
        ]);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/home', $response->getHeader('Location')[0]);
    }

    protected function logoutFromBroker($brokerUrl)
    {
        // Get a page to extract CSRF token
        $response = $this->client->get($brokerUrl . '/home');
        $this->assertEquals(200, $response->getStatusCode());
        
        $page = $response->getBody()->getContents();
        $csrfToken = $this->extractCsrfToken($page);

        // Submit logout form
        $response = $this->client->post($brokerUrl . '/logout', [
            'form_params' => [
                '_token' => $csrfToken,
            ]
        ]);

        $this->assertEquals(302, $response->getStatusCode());
    }

    protected function extractCsrfToken($html)
    {
        if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/<input[^>]*name="_token"[^>]*value="([^"]+)"/', $html, $matches)) {
            return $matches[1];
        }
        
        $this->fail('Could not extract CSRF token from page');
    }
}