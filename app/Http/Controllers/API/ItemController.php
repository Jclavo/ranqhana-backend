<?php

namespace App\Http\Controllers\API;

use App\Item;
use App\Price;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

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
            'price' => 'required:decimal',
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

        //Add price

        $this->savePrice($request->price,$item->id);

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
        //$item = Item::find($id);
        
        $item = Item::
                select('items.*','prices.price')
                ->leftJoin('prices', 'items.id', '=', 'prices.item_id')
                ->where('items.id', '=', $id)
                ->get();

        // if (is_empty($item)) {
        //     return $this->sendError('Item not found.');
        // }
        
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
            'name' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::find($id);
        
        $item->name = $request->name;
        $item->description = $request->description;
                
        $item->save();

        //Add price
        $price = 0;
        if ($request->price) {
           $price = $request->price;
        }
        $this->savePrice($price,$item->id);
        
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


    /**
     * 
     */
    public function pagination(Request $request)
    {
      // return $this->sendResponse($request->store_id, 'Items retrieved successfully.' );

        $sortArray = array("asc", "ASC", "desc", "DESC");


        $store_id = $request->store_id;
        // SearchOptions values
        $per_page = $request->searchOption['per_page'];
        $sortColumn = $request->searchOption['sortColumn'];
        $sortDirection = $request->searchOption['sortDirection'];
        $searchValue = $request->searchOption['searchValue'];

        // Initialize values if they are empty.
        if (empty($per_page)) {
            $per_page = 20;
        }

        if (!in_array($sortDirection, $sortArray)) {
            $sortDirection = "DESC";
            $sortColumn = "updated_at";
        }
        

        //if(Schema::hasColumn('items', $sortColumn ) === false) ;
        if (empty($sortColumn)) {
               $sortColumn = "updated_at";
        }

        $results = Item::
                select('items.*')
                ->join('stores', function ($join) use($store_id){
                    $join->on('stores.id', '=', 'items.store_id')
                         ->where('items.store_id', '=', $store_id);
                })

                ->where('items.name', 'like', '%'. $searchValue .'%')
                ->orWhere('items.name', 'like', '%'. $searchValue .'%')
                ->orderBy('items.'.$sortColumn, $sortDirection)
                ->paginate($per_page);
            
        return $this->sendResponse($results->items(), 'Items retrieved successfully.', $results->total() );

    }

    public function savePrice($price_item, $item_id){

        $addFlag = false;

        $lastPrice = Price::where('item_id', $item_id)
                            ->orderBy('created_at', 'DESC')
                            ->first();


        
        if(empty($lastPrice)){
            $addFlag = true;
        }
        else if($lastPrice->price != $price_item ){
            $addFlag = true;
        }
        
        if($price_item <= 0){
            $addFlag = false;
        }

        $price = null;
        if($addFlag){

            $price = new Price();
            $price->price = $price_item;
            $price->item_id = $item_id;
            
            $price->save();
        }
        return $price;
    }
}
