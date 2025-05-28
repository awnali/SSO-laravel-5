<?php

namespace App\Http\Controllers;

use App\MySSOServer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MyServerController extends Controller
{
    /**
     * Allowed SSO commands to prevent RCE attacks
     */
    private const ALLOWED_COMMANDS = [
        'attach',
        'login',
        'logout',
        'userInfo'
    ];

    public function index(Request $request, MySSOServer $ssoServer)
    {
        // Validate and sanitize the command parameter
        $command = $request->input('command');
        
        if (!$command || !in_array($command, self::ALLOWED_COMMANDS, true)) {
            return response()->json(['error' => 'Invalid or unknown command'], 404);
        }
        
        // Additional security: verify the command method exists and is public
        if (!method_exists($ssoServer, $command)) {
            return response()->json(['error' => 'Command not available'], 404);
        }
        
        try {
            // Execute the validated command
            $ssoServer->$command();
        } catch (\Exception $e) {
            \Log::error('SSO Server Error', [
                'command' => $command,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
            
            return response()->json(['error' => 'Server error occurred'], 500);
        }
        
        // The SSO server methods handle their own output
        exit();
    }
}
