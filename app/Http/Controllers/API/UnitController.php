<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

class UnitController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::all();
            
        return $this->sendResponse($units->toArray(), 'Units retrieved successfully.');
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
            'code' => ['required',
                        'max:3',
                        Rule::unique('units')->where(function($query) {
                            $query->where('store_id', '=', Auth::user()->store_id);
                        })
                    ], 
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = new Unit();
        
        $unit->code = strtoupper($request->code);
        $unit->description = $request->description;
        
        $request->allow_decimal ? $unit->allow_decimal = true : $unit->allow_decimal = false;

        $unit->store_id = Auth::user()->store_id;
        $unit->save();

        return $this->sendResponse($unit->toArray(), 'Units created successfully.');  
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

        $this->authorize('isMyUnit', $unit);
        
        return $this->sendResponse($unit->toArray(), 'Unit retrieved successfully.');
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
            'code' => ['required',
                        'max:3',
                        Rule::unique('units')->where(function($query) use($id){
                            $query->where('id', '<>', $id)
                                  ->where('store_id', '=', Auth::user()->store_id);
                        })  
                    ],
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = Unit::findOrFail($id);

        $this->authorize('isMyUnit', $unit);
        
        $unit->code = strtoupper($request->code);
        // $unit->description = $request->description;
        // $unit->allow_decimal = $request->allow_decimal;

        $unit->save();

        return $this->sendResponse($unit->toArray(), 'Units updated successfully.');  
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

        $this->authorize('isMyUnit', $unit);

        $unit->delete();

        return $this->sendResponse($unit->toArray(), 'Unit deleted successfully.');
    }

    /**
     * 
     */
    public function pagination(Request $request)
    {
        $sortArray = array("asc", "ASC", "desc", "DESC");

        // SearchOptions values
        $per_page = $request->per_page;
        $sortColumn = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        $searchValue = $request->searchValue;

        // Initialize values if they are empty.
        if (empty($per_page)) {
            $per_page = 20;
        }

        if (!in_array($sortDirection, $sortArray)) {
            $sortDirection = "DESC";
            $sortColumn = "updated_at";
        }
        
        if (empty($sortColumn)) {
               $sortColumn = "updated_at";
        }

        $query = Unit::query();

        $query->select('units.*');

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('code', 'like', '%'. $searchValue .'%')
                     ->orWhere('description', 'like', '%'. $searchValue .'%');
        });

        $query->where('store_id', '=', 0)
              ->orWhere('store_id', '=', Auth::user()->store_id);


        $results = $query->orderBy($sortColumn, $sortDirection)
        ->paginate($per_page);

            
        return $this->sendResponse($results->items(), 'Units retrieved successfully.', $results->total() );

    }

}
