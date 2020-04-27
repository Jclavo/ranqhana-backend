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
    public function businessValidations($validations, $actions = [])
    {
        foreach ($validations as $validation) {
            if (!$validation->passes()){
                
                // foreach ($errorObjectives as $errorObjective) {
                //     $errorObjective->execute();
                // }
                $this->businessActions($actions);
                abort(200, $validation->message());  
                break; 
            } 
        }
    }

    public function businessActions($actions)
    {
        foreach ($actions as $action) {
            if (!$action->execute()){
                abort(200, $action->message());  
                break; 
            } 
        }
    }
  
}
