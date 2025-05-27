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
     * Registered brokers
     * @var array
     */
    private static $brokers = [
        'broker1' => ['secret'=>'broker1_secret'],
        'broker2' => ['secret'=>'broker2_secret']
    ];

    /**
     * Get the API secret of a broker and other info
     *
     * @param string $brokerId
     * @return array
     */
    protected function getBrokerInfo($brokerId)
    {
        return isset(self::$brokers[$brokerId]) ? self::$brokers[$brokerId] : null;
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
        \Log::info('authenticate called', ['username' => $username, 'password' => '***']);
        
        if (!isset($username)) {
            return ValidationResult::error("username isn't set");
        }

        if (!isset($password)) {
            return ValidationResult::error("password isn't set");
        }

        if(Auth::attempt(['email' => $username, 'password' => $password])){
            \Log::info('authenticate success', ['username' => $username]);
            return ValidationResult::success();
        }
        \Log::info('authenticate failed', ['username' => $username]);
        return ValidationResult::error("can't find user");

    }


    /**
     * Get the user information
     *
     * @return array
     */
    protected function getUserInfo($username)
    {
        \Log::info('getUserInfo called', ['username' => $username]);
        $user = User::where('email',$username)->first();
        \Log::info('getUserInfo user found', ['user' => $user ? $user->toArray() : null]);

        return $user ? $user->toArray() : null;
    }
    public function getUserById($id){
        return User::findOrFail($id);
    }
}