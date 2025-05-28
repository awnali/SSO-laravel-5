<?php
/**
 * Created by PhpStorm.
 * User: awnali
 * Date: 6/14/17
 * Time: 3:57 PM
 */

namespace App;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jasny\SSO\Server;
use Jasny\ValidationResult;

class MySSOServer extends Server
{

    /**
     * Get registered brokers from environment configuration
     * @return array
     */
    private function getBrokers()
    {
        return [
            'broker1' => ['secret' => env('SSO_BROKER1_SECRET')],
            'broker2' => ['secret' => env('SSO_BROKER2_SECRET')]
        ];
    }

    /**
     * Get the API secret of a broker and other info
     *
     * @param string $brokerId
     * @return array|null
     */
    protected function getBrokerInfo($brokerId)
    {
        // Validate broker ID to prevent injection attacks
        if (!is_string($brokerId) || !preg_match('/^[a-zA-Z0-9_-]+$/', $brokerId)) {
            \Log::warning('Invalid broker ID attempted', ['broker_id' => $brokerId]);
            return null;
        }
        
        $brokers = $this->getBrokers();
        return isset($brokers[$brokerId]) ? $brokers[$brokerId] : null;
    }

    /**
     * Authenticate using user credentials
     *
     * @param string $username
     * @param string $password
     * @return ValidationResult
     */
    protected function authenticate($username, $password)
    {
        // Input validation
        if (empty($username) || empty($password)) {
            \Log::warning('Authentication attempt with missing credentials', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return ValidationResult::error("Invalid credentials");
        }

        // Validate email format
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            \Log::warning('Authentication attempt with invalid email format', [
                'ip' => request()->ip()
            ]);
            return ValidationResult::error("Invalid credentials");
        }

        // Rate limiting check (basic implementation)
        $cacheKey = 'auth_attempts_' . request()->ip();
        $attempts = \Cache::get($cacheKey, 0);
        
        if ($attempts >= 5) {
            \Log::warning('Authentication rate limit exceeded', [
                'ip' => request()->ip(),
                'attempts' => $attempts
            ]);
            return ValidationResult::error("Too many attempts. Please try again later.");
        }

        if (Auth::attempt(['email' => $username, 'password' => $password])) {
            // Clear failed attempts on successful login
            \Cache::forget($cacheKey);
            
            \Log::info('Authentication successful', [
                'ip' => request()->ip(),
                'user_id' => Auth::id()
            ]);
            return ValidationResult::success();
        }
        
        // Increment failed attempts
        \Cache::put($cacheKey, $attempts + 1, now()->addMinutes(15));
        
        \Log::warning('Authentication failed', [
            'ip' => request()->ip(),
            'attempts' => $attempts + 1
        ]);
        
        return ValidationResult::error("Invalid credentials");
    }


    /**
     * Get the user information
     *
     * @param string $username
     * @return array|null
     */
    protected function getUserInfo($username)
    {
        if (empty($username) || !filter_var($username, FILTER_VALIDATE_EMAIL)) {
            \Log::warning('Invalid username in getUserInfo', ['ip' => request()->ip()]);
            return null;
        }

        $user = User::where('email', $username)->first();
        
        if ($user) {
            \Log::info('User info retrieved', [
                'user_id' => $user->id,
                'ip' => request()->ip()
            ]);
            
            // Return only safe user information
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
        }

        return null;
    }

    /**
     * Get user by ID - maintains original behavior for backward compatibility
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            \Log::warning('Invalid user ID in getUserById', [
                'id' => $id,
                'ip' => request()->ip()
            ]);
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('User not found');
        }

        return User::findOrFail($id);
    }

    /**
     * Get user by ID with secure error handling (returns null instead of throwing)
     *
     * @param int $id
     * @return User|null
     */
    public function getUserByIdSecure($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            \Log::warning('Invalid user ID in getUserByIdSecure', [
                'id' => $id,
                'ip' => request()->ip()
            ]);
            return null;
        }

        try {
            return User::findOrFail($id);
        } catch (\Exception $e) {
            \Log::warning('User not found in getUserByIdSecure', [
                'id' => $id,
                'ip' => request()->ip()
            ]);
            return null;
        }
    }
}