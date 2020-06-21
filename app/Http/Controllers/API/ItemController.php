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
        // $this->savePrice($item->price,$item->id);

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
        $item = Item::findOrFail($id);
                
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
        // $this->savePrice($item->price,$item->id);
        
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
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'items');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;
            
        $invoice = new Invoice();
        $invoice_type_id  = $request->invoice_type_id;

        $query = Item::query();
        $query->select('items.*', 'units.code as unit', 'units.fractioned')
              ->leftJoin('units', function ($join){
                    $join->on('units.id', '=', 'items.unit_id')
                         ->latest();
                });
        
        $query->when((!empty($invoice_type_id) && $invoice_type_id == $invoice->getTypeForPurchase()), function ($q) {
            return $q->where('items.stocked', '=', true );
        });

        $query->where(function($q) use ($searchValue){
            $q->where('items.name', 'like', '%'. $searchValue .'%')
              ->orWhere('items.description', 'like', '%'. $searchValue .'%')
              ->orWhere('items.price', 'like', $searchValue .'%')
              ->orWhere('items.stock', 'like', $searchValue .'%');
        });

        
        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);

            
        return $this->sendResponse($results->items(), 'Items retrieved successfully.', $results->total() );

    }

    // public function savePrice($price_item, $item_id){

    //     $addFlag = false;

    //     $lastPrice = Price::where('item_id', $item_id)
    //                         ->orderBy('created_at', 'DESC')
    //                         ->first();


        
    //     if(empty($lastPrice)){
    //         $addFlag = true;
    //     }
    //     else if($lastPrice->price != $price_item ){
    //         $addFlag = true;
    //     }
        
    //     if($price_item <= 0){
    //         $addFlag = false;
    //     }

    //     $price = null;
    //     if($addFlag){

    //         $price = new Price();
    //         $price->price = $price_item;
    //         $price->item_id = $item_id;
            
    //         $price->save();
    //     }
    //     return $price;
    // }

    // public function updateStock($id, $quantity){

    //     $item = Item::find($id);

    //     if (is_null($item)) {
    //         return $this->sendError('Item not found.');
    //         // return false;
    //     }

    //     if($quantity > $item->stock){
    //         return $this->sendError('There is not stock for product ' . $id);
    //         // return false;
    //     }

    //     $item->stock = $item->stock - $quantity;
    //     $item->save();
        
    //     // return true;
    //     return $this->sendResponse($item->toArray(), 'Stock updated.');

    // }

    /**
     * To get "Items" according invoice type
     */

    // public function getForInvoiceType(int $invoiceType)
    // {
    //     $validator = Validator::make(['invoiceType' =>$invoiceType], [
    //         'invoiceType' => 'required|exists:invoice_types,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors()->first());
    //     }

    //     $invoice = new Invoice();

    //     $query = Item::query();

    //     $query->select('items.*', 'units.code as unit', 'units.fractioned')
    //             ->join('stores', function ($join){
    //                 $join->on('stores.id', '=', 'items.store_id')
    //                     ->where('items.store_id', '=', Auth::user()->store_id);
    //             })
    //             ->leftJoin('units', function ($join){
    //                 $join->on('units.id', '=', 'items.unit_id')
    //                     ->latest();
    //             })
    //             ->where('store_id', '=', Auth::user()->store_id);

    //     $query->when(($invoiceType == $invoice->getTypeForPurchase()), function ($q)  {
    //         return $q->where('stocked', true );
    //     });

    //     $item = $query->take(10);

    //     return $this->sendResponse($item, 'Items retrieved successfully.');

    //     // $invoice = Invoice::findOrFail($id);

    //     // $this->businessValidations([
    //     //     new BelongsToStore(Auth::user(), [$invoice]),
    //     // ]);

    //     // $this->businessActions([ new InvoiceAnull($invoice)]);

    //     // return $this->sendResponse($invoice->toArray(), 'Invoice Anulled successfully.');
    // }

}
