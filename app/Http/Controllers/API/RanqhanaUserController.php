<?php

namespace App\Http\Controllers\API;

use App\Models\RanqhanaUser;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RanqhanaUserController extends ResponseController{


    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'external_user_id' => 'required|exists:mysql_roles.users,id',
        //     'login' => 'required|exists:mysql_roles.users,login',
        //     'company_project_id' => 'required|exists:mysql_roles.users,company_project_id',
        // ]);
        
        // if ($validator->fails()) {
        //     return $this->sendError($validator->errors()->first());
        // }
        
        // $ranqhanaUser = RanqhanaUser::updateOrCreate([
        //     'external_user_id' => $request->external_user_id,
        //     'login' => $request->login,
        //     'company_project_id' => $request->company_project_id,
        // ]);

        // return $this->sendResponse($ranqhanaUser->toArray(), 'Record syncronized!');  
    }

}