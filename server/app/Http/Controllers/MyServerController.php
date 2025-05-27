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
        $ssoServer->$command();
        
        // The SSO server methods handle their own output
        exit();
    }
}
