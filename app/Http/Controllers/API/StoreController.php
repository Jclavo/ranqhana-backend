<?php

namespace App\Http\Controllers\API;

use App\Models\Store;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

//Utils
use App\Utils\PaginationUtils;

//Services
use App\Services\LanguageService;

class StoreController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

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
            'country_id' => 'required|exists:countries,id',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $store = new Store();
        $store->name = $request->name;
        $store->country_id = $request->country_id;

        $store->save();

        return $this->sendResponse($store->toArray(), $this->languageService->getSystemMessage('crud.create'));
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $store = Store::select('stores.*','countries.code as country_code')
                 ->join('countries', 'stores.country_id', '=', 'countries.id')->findOrFail($id);
        
        return $this->sendResponse($store->toArray(), $this->languageService->getSystemMessage('crud.read'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:45',
            'country_id' => 'required|exists:countries,id',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $store = Store::findOrFail($id);;
        $store->name = $request->name;
        $store->country_id = $request->country_id;

        $store->save();

        return $this->sendResponse($store->toArray(), $this->languageService->getSystemMessage('crud.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $store = Store::findOrFail($id);
        
        $store->delete();

        return $this->sendResponse($store->toArray(), $this->languageService->getSystemMessage('crud.delete'));
    }


    /**
     * Pagination 
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
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'stores');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;

        $query = Store::query();
        $query->select('stores.*', 'countries.code' , 'countries.name as country')
              ->join('countries', 'stores.country_id', '=', 'countries.id');  
       
        $query->where(function($q) use ($searchValue){
            $q->where('stores.name', 'like', '%'. $searchValue .'%');
        });

        $results = $query->orderBy('stores.'.$sortColumn, $sortDirection)
                         ->paginate($pageSize);
 
        return $this->sendResponse($results->items(), $this->languageService->getSystemMessage('crud.pagination'), $results->total() );

    }
}
