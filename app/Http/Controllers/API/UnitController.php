<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = new Unit();
        
        $unit->code = $request->code;
        $unit->description = $request->description;
        $unit->allow_decimal = $request->allow_decimal;
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
        $unit = unit::find($id);
        
        if (is_null($unit)) {
            return $this->sendError('Unit not found.');
        }
        
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
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $unit = Unit::find($id);

        if (is_null($unit)) {
            return $this->sendError('Unit not found.');
        }
        
        $unit->code = $request->code;
        $unit->description = $request->description;
        $unit->allow_decimal = $request->allow_decimal;
        $unit->save();

        return $this->sendResponse($unit->toArray(), 'Units created successfully.');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        
        $unit = Unit::find($id);

        if (is_null($unit)) {
            return $this->sendError('Unit not found.');
        }

        $unit->delete();

        return $this->sendResponse($unit->toArray(), 'Unit deleted successfully.');
    }
}
