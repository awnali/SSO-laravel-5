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
        'Alice' => ['secret'=>'8iwzik1bwd'],
        'Greg' => ['secret'=>'7pypoox2pc'],
        'Julias' => ['secret'=>'ceda63kmhp']
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
        if (!isset($username)) {
            return ValidationResult::error("username isn't set");
        }

        if (!isset($password)) {
            return ValidationResult::error("password isn't set");
        }

        if(Auth::attempt(['email' => $username, 'password' => $password])){
            return ValidationResult::success();
        }
        return ValidationResult::error("can't find user");

    }


    /**
     * Get the user information
     *
     * @return array
     */
    protected function getUserInfo($username)
    {
        $user = User::where('email',$username)->first();

        return $user ? $user : null;
    }
    public function getUserById($id){
        return User::findOrFail($id);
    }
}