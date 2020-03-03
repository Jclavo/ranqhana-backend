<?php

namespace App\Http\Controllers\API;

use App\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;

class ItemController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        $item = Item::all();
            
        return $this->sendResponse($item->toArray(), 'Items retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required',
            'store_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = new Item();
        
        $item->name = $request->name;
        $item->description = $request->description;
        $item->store_id = $request->store_id;
        
        $item->save();
        
        return $this->sendResponse($item->toArray(), 'Items created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find($id);
        
        if (is_null($item)) {
            return $this->sendError('Item not found.');
        }
        
        return $this->sendResponse($item->toArray(), 'Item retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::find($id);
        
        $item->name = $request->name;
        $item->description = $request->description;
                
        $item->save();
        
        return $this->sendResponse($item->toArray(), 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $item = Item::find($id);

        if (is_null($item)) {
            return $this->sendError('Item not found.');
        }
        
        $item->delete();

        return $this->sendResponse($item->toArray(), 'Item deleted successfully.');
    }
}
