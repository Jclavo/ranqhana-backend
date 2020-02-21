<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends ResponseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|numeric',
            'email' => 'email|unique:users',
            'name' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'store_id' => 'required'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       switch ($request->country_code) {
            case "55":
                $validator = Validator::make($request->all(), [
                    'identification' => 'required|digits:11|unique:users',
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->errors()->first());
                }
                break;
            case "51":
                //echo "Your favorite color is blue!";
                break;
            default:
                return $this->sendError('Unknow country');
        }


        $user = new User();
        
        $user->name = $request->name;
        $user->identification = $request->identification;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->country_code = $request->country_code;
        $user->store_id = $request->store_id;
        
        $user->save();
        
        return $this->sendResponse($user->toArray(), 'User created successfully.');  
        
        
    }
    
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|numeric',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       switch ($request->country_code) {
            case "55":
                $validator = Validator::make($request->all(), [
                    'identification' => 'required|digits:11',
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->errors()->first());
                }
                break;
            case "51":
                //echo "Your favorite color is blue!";
                break;
            default:
                return $this->sendError('Unknow country');
        }
        
        if (!Auth::attempt($request->only('country_code', 'identification',  'password'))) {
            return $this->sendError('Invalid credentials');
        }
        
        Auth::user()->api_token = Str::random(80);
        Auth::user()->save();
        
        //return response()->json(['user' => Auth::user(),'token' => Auth::user()->api_token]);
        return $this->sendResponse(Auth::user()->toArray(), 'User logged successfully.');  
    }
    
    public function getUserInformation() {
        return $this->sendResponse(Auth::user(), 'User information gotten successfully.');  
    }
}
