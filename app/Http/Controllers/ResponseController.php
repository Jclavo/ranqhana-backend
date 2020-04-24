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
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function business($objectives)
    {
        $messages = tap(collect(), function($messages) use($objectives) {
            collect($objectives)->each(function($objective) use($messages) {
                if (! $objective->passes()) {
                     $messages->put('business', $objective->message());
                    //array_push($messages,$objective->message());
                    //throw ValidationException::withMessages(['message' => $objective->message()]);
                    abort(200, $objective->message());
                   
                }
            });
        });        
        
        // if (! $messages->isEmpty()) {
        //     throw new ValidationException(
        //         app('validator'), response()->json('abc')
        //     );
        //     // return $this->sendError($messages->toArray());
        //     // return $this->sendError('error');
        //     // return $this->sendError($messages[0]->business);

        //     // https://medium.com/@matthew.erskine/laravel-5-5-an-easy-way-to-clean-up-your-business-logic-eb5536150734
        //     // https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d
        //     // https://stackoverflow.com/questions/37692482/laravel-how-to-add-a-custom-function-in-an-eloquent-model
        // }https://es.slideshare.net/BobbyBouwmann/laravel-design-patterns-86729254
    }

    
    
    
}
