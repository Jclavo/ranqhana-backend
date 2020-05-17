<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Store;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

//Utils
use App\Utils\PaginationUtils;

//Actions
use App\Actions\User\UserIdentificationValidByCountry;

class UserController extends ResponseController{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:45',
            'identification' => 'required|numeric|unique:users',
            'email' => 'email|unique:users',
            'store_id' => 'required|exists:stores,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // $store =  
        $this->businessValidations([
            new UserIdentificationValidByCountry(Store::findOrFail($request->store_id), $request->identification),
        ]);

        $user = new User();
        
        $user->name = $request->name;
        $user->identification = $request->identification;
        $user->email = $request->email;
        $user->password = bcrypt($request->identification);
        $user->store_id = $request->store_id;
        
        $user->save();
        
        return $this->sendResponse($user->toArray(), 'User created successfully.');  
    }

    /**
     * Pagination of table users
     */
    public function pagination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pageSize' => 'numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'users');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;
        $store_id      = Auth::user()->store_id;


        $query = User::query();
        $query->select('users.id','users.name','users.identification','users.email','users.store_id','stores.name as store')
              ->join('stores', 'users.store_id', '=', 'stores.id');  
       
        $query->where(function($q) use ($searchValue){
            $q->where('users.name', 'like', '%'. $searchValue .'%')
              ->orWhere('users.identification', 'like', '%'. $searchValue .'%')
              ->orWhere('users.email', 'like', $searchValue .'%');
            //   ->orWhere('users.stock', 'like', $searchValue .'%');
        });

        $results = $query->orderBy('users.'.$sortColumn, $sortDirection)
                         ->paginate($pageSize);
 
        return $this->sendResponse($results->items(), 'Users retrieved successfully.', $results->total() );

    }
}