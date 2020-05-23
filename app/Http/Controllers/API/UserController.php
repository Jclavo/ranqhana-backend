<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Store;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 

//Utils
use App\Utils\PaginationUtils;
use App\Utils\UserUtils;

//Actions
use App\Actions\User\UserIdentificationValidByCountry;
use App\Actions\User\UserIsFree;

//Rules
use App\Rules\Identification;
use App\Rules\PhoneCountry;

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
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|max:45',
            'lastname' => 'required|max:45',
            'address' => 'nullable|max:100',
            'identification' => ['required', 'numeric' ,
                                Rule::unique('users')->where(function($query) use($request) {
                                    $query->where('store_id', '=', $request->store_id);
                                }),
                                new Identification($request->store_id)],
            'email' => ['nullable','email',
                        Rule::unique('users')->where(function($query) use($request) {
                            $query->where('store_id', '=', $request->store_id);
                        })],
            'phone' => ['nullable', new PhoneCountry($request->store_id) ],
        ]);
      
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $user = new User();
        
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->identification = $request->identification;
        $user->email = $request->email;
        $user->password = bcrypt($request->identification);
        $user->store_id = $request->store_id;
        $user->login = UserUtils::generateLogin($user->identification,$user->store_id);
        
        $user->save();
        
        return $this->sendResponse($user->toArray(), 'User created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id, ['id','name','identification','email','store_id']);
        
        return $this->sendResponse($user->toArray(), 'User retrieved successfully.');
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|max:45',
            'lastname' => 'required|max:45',
            'address' => 'nullable|max:100',
            'identification' => ['required', 'numeric' ,
                                Rule::unique('users')->where(function($query) use($request) {
                                    $query->where('store_id', '=', $request->store_id);
                                })->ignore($id),
                                new Identification($request->store_id)],
            'email' => ['nullable','email',
                        Rule::unique('users')->ignore($id)],
            
            'password' => 'nullable|min:8|max:45',
            'repassword' => 'nullable|min:8|max:45|same:password',
            'phone' => ['nullable', new PhoneCountry($request->store_id)]

        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // $store =  
        $this->businessValidations([
            new UserIdentificationValidByCountry(Store::findOrFail($request->store_id), $request->identification),
        ]);

        $user = User::findOrFail($id);

        //Validate if store can be update
        if($user->store_id != $request->store_id){
            $this->businessValidations([
                new UserIsFree(User::findOrFail($id)),
            ]);
        }
        
        $user->name = $request->name;
        $user->identification = $request->identification;
        $user->email = $request->email;
        $user->store_id = $request->store_id;
        //Update password if it has a value
        if(!empty($request->password)){
            $user->password = bcrypt($request->password);
        }
        
        $user->save();

        return $this->sendResponse($user->toArray(), 'User updated successfully.');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if($user->id === Auth::user()->id){
            return $this->sendError('The logged user can not be deleted.');
        }
        
        $user->delete();

        return $this->sendResponse($user->toArray(), 'User deleted successfully.');
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
        $query->select('users.id','users.login','users.identification','users.name','users.lastname','users.email',
                        'users.phone','users.address', 'users.store_id','stores.name as store')
              ->join('stores', 'users.store_id', '=', 'stores.id');  
       
        $query->where(function($q) use ($searchValue){
            $q->where('users.login', 'like', '%'. $searchValue .'%')
              ->orwhere('users.identification', 'like', '%'. $searchValue .'%')
              ->orwhere('users.name', 'like', '%'. $searchValue .'%')
              ->orwhere('users.lastname', 'like', '%'. $searchValue .'%')
              ->orWhere('users.email', 'like', $searchValue .'%')
              ->orWhere('users.phone', 'like', $searchValue .'%')
              ->orWhere('users.address', 'like', $searchValue .'%');
        });

        $results = $query->orderBy('users.'.$sortColumn, $sortDirection)
                         ->paginate($pageSize);
 
        return $this->sendResponse($results->items(), 'Users retrieved successfully.', $results->total() );

    }
}