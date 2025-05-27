# SSO-laravel-5

This tutorial work for laravel 5.x but i'm following 5.4 here. 

lets first setup SSO server

## Server

Create new laravel project

You can install from composer or use laravel installer
```sh
$ laravel new server
```
Make sure laravel project is working. 

You might need to create new app key for your laravel project,

```sh
$ php artisan key:generate
```

Install [sso package](https://github.com/jasny/sso) from composer:

```sh
$ composer require jasny/sso
```
Copy the MySSOServer class from repo. This is simple a service class, which you can paste anywhere in your laravel project.

*make sure your server is connected to DB and you run migration successfully.*

Create new Server Controller (you can do this stuff even in route file):

```sh
$ php artisan make:controller MyServerController
```

Replace newly creted HomeController, with following code:
```

<?php

namespace App\Http\Controllers;

use App\MySSOServer;

class MyServerController extends Controller
{
    public function index(MySSOServer $ssoServer){
        $command = isset($_REQUEST['command']) ? $_REQUEST['command'] : null;
        if (!$command || !method_exists($ssoServer, $command)) {
            header("HTTP/1.1 404 Not Found");
            header('Content-type: application/json; charset=UTF-8');

            echo json_encode(['error' => 'Unknown command']);
            exit();
        }
        $user = $ssoServer->$command();
        if($user)
            return response()->json($user);
    }
}

```

Add route to your route file:

    Route::get('/', 'MyServerController@index');
    Route::post('/', 'MyServerController@index');

*yes you need both,GET & POST*

try to hit above url from browser, you should get following response,

>     {
>         error: "Unknown command"
>     }
If you didn't see above response, it means you did some mistake above.

Your server is ready :) 

lets jump to client now.

## Client

Create new laravel project

```sh
$ laravel new client1
```

*Make sure your client laravel project is working*.

Install [sso package](https://github.com/jasny/sso) from composer:
```sh
$ composer require jasny/sso
```
its time to create your client authentication, you can choose any type of authentication by i will stay with laravel authentication scaffolding, which you can generate by following artisan command:
```ssh
$ php artisan make:auth 
$ php artisan migrate
```
*now you should have login/registration scaffolding along with HomeController.*

Create MyBroker.php file and paste following code:

    <?php

	namespace App;


	use Illuminate\Support\Facades\Auth;
	use Jasny\SSO\Broker;
	use Jasny\SSO\Exception;
	use Jasny\SSO\NotAttachedException;

	class MyBroker extends Broker
	{
    public function __construct()
    {
		        parent::__construct(env('SSO_SERVER_URL'),env('SSO_CLIENT_ID'),env("SSO_CLIENT_SECRET"));
        $this->attach(true);
    }

    protected function request($method, $command, $data = null)
    {
        if (!$this->isAttached()) {
            throw new NotAttachedException('No token');
        }
        $url = $this->getRequestUrl($command, !$data || $method === 'POST' ? [] : $data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: Bearer '. $this->getSessionID()]);

        if ($method === 'POST' && !empty($data)) {
            $post = is_string($data) ? $data : http_build_query($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        $response = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            $message = 'Server request failed: ' . curl_error($ch);
            throw new Exception($message);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        list($contentType) = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
        

        $data = json_decode($response, true);
        if ($httpCode == 403) {
            $this->clearToken();
            throw new NotAttachedException($data['error'] ?: $response, $httpCode);
        }
        if ($httpCode >= 400) throw new Exception($data['error'] ?: $response, $httpCode);

        return $data;
    }
    protected function loginUser($username, $password){
        try{
            $this->login($username, $password);
        }
        catch(NotAttachedException $e){
            return false;
        }
        catch(Exception $e){
            return false;
        }
        return true;
    }
    public function loginCurrentUser($returnUrl = '/home'){
        if($user = $this->getUserInfo()){
            Auth::loginUsingId($user['id']);
            return redirect($returnUrl);
        }
     }
    }
    
    
Don't forget to add your server and broker credentials in .ev file:

> SSO_SERVER_URL=http://localhost/server/public/ 

> SSO_CLIENT_ID=12345

> SSO_CLIENT_SECRET=abc123

At this point either you can remove the laravel authentication and use authentication based on response of our server or you can continue to use laravel auth and use SSO both. i will use the second method where i won't touch laravel authentication rather will add new methods to it.

So let customize laravel login first, copy paste following method into your LoginController:

    public function login(Request $request, MyBroker $myBroker)
    {
        $this->validateLogin($request);

        //Login on SSO SERVER
        if($myBroker->loginUser($request->get('email'),$request->get('password'))){
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
        }
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

*You login/register should still work fine even after this code.*

by using following line, you have successfully created shared session using SSO. Now we'll create another Client to test everything.

## Client2

Just repeat the steps what we did before for client1.
don't forget to replace CLIENT_ID and CLIENT_SECRET in .env file

##SSO between client1 & client2

 Do every step for all your clients, i.e. client1 & client2

 **1. Redirect after login**


Now we will implement functionality that if you're loggedIn in one of client it should ask you to signIn when you go to login page of other client. 

*Your client and Server should be connected to same DB for authentication*

paste following code in your laravel Exception handler method:

    $broker = new MyBroker();
    $broker->loginCurrentUser();
    
So now if you are loggedIn in client one and come to home of client2, it will auto login, whoever is loggedIn in client1

Do the same step for client1, so that if you are loggedIn in client2 and comes to client1 home page, it should auto login.

**2. Redirect from Middleware**
now we'll redirect to home page if you're loggedIn user visits login and registration pages.
Paste following code in RedirectIfAuthenticated middleware:

    $broker  =new MyBroker();
    $broker->loginCurrentUser();

**3. Logout**

if you are logout from one of client, it means you're logged out from all clients. 

copy the following function and paste in LoginController: 

    public function logout(Request $request)
    {
        $broker  =new MyBroker();
        $broker->logout();
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }

Now test everything and your SSO Server along with 2 clients are ready :)
