<?php

namespace App\Http\Controllers;

class ResponseController extends Controller
{
    public function sendResponse($result, $message)
    {
        $response = [ 'status' => true, 'result' => $result, 'message' => $message ];
        
        return response()->json($response, 200);
    }
    
    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = ['status' => false, 'result' => $errorMessages, 'message' => $error];
        
        return response()->json($response, $code);
    }
    
    
    
}
