<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    public function sendResponse($result, $message, $records = 0)
    {
        $response = ['status' => true, 'result' => $result, 'message' => $message, 'records' => $records];
        
        return response()->json($response, 200);
    }
    
    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = ['status' => false, 'result' => $errorMessages, 'message' => $error, 'records' => 0];
        
        return response()->json($response, $code);
    }

    /**
     * Enforces business objectives.
     *
     * @param  array $objectives
     * @param  array $errorObjectives
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function businessValidations($objectives, $errorObjectives = [])
    {
        foreach ($objectives as $objective) {
            if (!$objective->passes()){
                
                foreach ($errorObjectives as $errorObjective) {
                    $errorObjective->execute();
                }
                abort(200, $objective->message());  
                break; 
            } 
        }
    }

    public function businessActions($actions)
    {
        foreach ($actions as $action) {
            $action->execute();
        }
    }
  
}
