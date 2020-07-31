<?php

namespace App\Http\Controllers\API;

use App\Models\Item;
use App\Models\Price;
use App\Models\Unit;
use App\Models\Invoice;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

//Utils
use App\Utils\PaginationUtils;

class ItemController extends ResponseController
{

    function __construct()
    {
        $this->middleware('permission_in_role:items/read'); 
        $this->middleware('permission_in_role:items/create', ['only' => ['store']]);
        $this->middleware('permission_in_role:items/update', ['only' => ['update']]);
        $this->middleware('permission_in_role:items/delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        // $item = Item::all();
            
        // return $this->sendResponse($item->toArray(), 'Items retrieved successfully.');
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
            'price' => 'required|numeric|between:0.00,99999.99',
            'unit_id' => 'required|exists:units,id',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = new Item();
        
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $request->stocked ? $item->stocked = $request->stocked : $item->stocked = false; 
        $item->unit_id = $request->unit_id;
        $item->user_id = Auth::user()->getLocalUserID();
        $item->save();

        //Add price
        $item->prices()->create(['price' => $request->price]);

        //add stock types
        $item->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($item->toArray(), 'Item created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::with('stock_types')->findOrFail($id); //get stock_types
                
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
            'name' => 'required',
            'price' => 'required|numeric|between:0.00,99999.99',
            'unit_id' => 'required|exists:units,id',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
       
        $item = Item::findOrFail($id);
        
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->unit_id = $request->unit_id;

        $request->stocked ? $item->stocked = $request->stocked : $item->stocked = false; 
        

        if(!$item->stocked && $item->stock > 0){
            return $this->sendError('The item has stock. It can not be modified.');
        }
     
        $item->save();

        //Add price
        $item->prices()->updateOrCreate(['price' => $request->price]);

        //add stock types
        $item->stock_types()->sync($request->stock_types ?? []);

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
        $item = Item::findOrFail($id);
        
        $item->delete();

        return $this->sendResponse($item->toArray(), 'Item deleted successfully.');
    }


    /**
     * Pagination
     */
    public function pagination(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'pageSize' => 'numeric|gt:0',
            'stock_type' => 'nullable|exists:stock_types,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'items');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;
        $stock_type_id    = $request->stock_type_id;
            
        $invoice = new Invoice();
        $stock_type  = $request->stock_type;

        $query = Item::query();
        $query->select('items.*', 'units.code as unit', 'units.fractioned')
              ->leftJoin('units', function ($join){
                    $join->on('units.id', '=', 'items.unit_id')
                         ->latest();
                });

        //Filter by stock types
        $query->when((!empty($stock_type_id) ), function ($q) use($stock_type_id) {
            return $q->join('item_stock_type', function ($join) use($stock_type_id){
                        $join->on('item_stock_type.item_id', '=', 'items.id')
                             ->where('item_stock_type.stock_type_id', '=' ,$stock_type_id);
                    });
        });
        
        $query->where(function($q) use ($searchValue){
            $q->where('items.name', 'like', '%'. $searchValue .'%')
              ->orWhere('items.description', 'like', '%'. $searchValue .'%')
              ->orWhere('items.price', 'like', $searchValue .'%')
              ->orWhere('items.stock', 'like', $searchValue .'%');
        });

        //get stock_types
        $query->with('stock_types');

        
        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);

            
        return $this->sendResponse($results->items(), 'Items retrieved successfully.', $results->total() );

    }

}
