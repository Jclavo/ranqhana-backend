<?php

namespace App\Http\Controllers\API;

use App\Models\Unit;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\App;

//Utils
use App\Utils\PaginationUtils;

class UnitController extends ResponseController
{
    function __construct()
    {
        $this->middleware('permission_in_role:units/read'); 
        $this->middleware('permission_in_role:units/create', ['only' => ['store']]);
        $this->middleware('permission_in_role:units/update', ['only' => ['update']]);
        $this->middleware('permission_in_role:units/delete', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $units = Unit::all();
            
        // return $this->sendResponse($units->toArray(), 'Units retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
            'code' => ['required','max:3','unique:units'] 
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = new Unit();
        
        $unit->code = strtoupper($request->code);
        $unit->description = $request->description;
        $request->fractioned ? $unit->fractioned = true : $unit->fractioned = false;
        $unit->save();

        return $this->sendResponse($unit->toArray(), __('messages.create'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = Unit::findOrFail($id);
        
        return $this->sendResponse($unit->toArray(), __('messages.read'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        //
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
            'code' => ['required','max:3',
                        Rule::unique('units')->ignore($id)],
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = Unit::findOrFail($id);
        
        $unit->code = strtoupper($request->code);
        $unit->description = $request->description;
        $request->fractioned ? $unit->fractioned = true : $unit->fractioned = false;

        $unit->save();

        return $this->sendResponse($unit->toArray(), __('messages.update'));  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $unit = Unit::findOrFail($id);

        $unit->delete();

        return $this->sendResponse($unit->toArray(), __('messages.delete'));
    }

    /**
     * 
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
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'units');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;


        $query = Unit::query();
        $query->select('units.*');

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('code', 'like', '%'. $searchValue .'%')
                     ->orWhere('description', 'like', '%'. $searchValue .'%');
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                   ->paginate($pageSize);
    
        return $this->sendResponse($results->items(), __('messages.pagination'), $results->total() );
        
    }

}
