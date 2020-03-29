<?php

namespace App\Http\Controllers\API;

use App\Invoice;
use App\InvoiceDetail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB;

use App\Http\Controllers\API\ItemController;

class InvoiceController extends ResponseController
{
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'invoice_id' => 'required|integer',
        // ]);

        // if ($validator->fails()) {
        //     return $this->sendError($validator->errors()->first());
        // }

        $sellInvoice = Invoice::find($id);

        if (is_null($sellInvoice)) {
            return $this->sendError('Invoice not found.');
        }

        $sellInvoice->serie    = $request->serie;
        // $sellInvoice->client   = $request->client;

        $sellInvoice->save();

        return $this->sendResponse($sellInvoice->toArray(), 'Sell invoice updated successfully.');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {

    }

    /**
     * 
     */

    public function pagination(Request $request)
    {
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
        
        if (empty($sortColumn)) {
               $sortColumn = "updated_at";
        }

        $results = Invoice::
                select('invoices.*')
                // ->join('stores', function ($join) use($store_id){
                //     $join->on('stores.id', '=', 'items.store_id')
                //          ->where('items.store_id', '=', $store_id);
                // })
                // ->leftJoin('prices', function ($join){
                //     $join->on('items.id', '=', 'prices.item_id')
                //          ->latest();
                //         //  ->orderBy('prices.created_at', 'DESC')
                //         //  ->take(1);
                //         //  ->first();
                // })

                ->where('invoices.serie', 'like', '%'. $searchValue .'%')
                // ->orWhere('invoices.description', 'like', '%'. $searchValue .'%')
                // ->orWhere('invoices.price', 'like', $searchValue .'%')
                // ->orWhere('invoices.stock', 'like', $searchValue .'%')
                ->orderBy('invoices.'.$sortColumn, $sortDirection)
                // ->distinct()
                ->paginate($per_page);

                // $item = Item::
                // select('items.*','prices.price')
                // ->leftJoin('prices', 'items.id', '=', 'prices.item_id')
                // ->where('items.id', '=', $id)
                // ->orderBy('prices.created_at', 'DESC')
                // ->first();
            
        return $this->sendResponse($results->items(), 'Invoices retrieved successfully.', $results->total() );

    }
     




    /**
     * 
     */

    public function createSellInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|numeric|between:0.01,99999.99',
            // 'total' => 'required|numeric|between:0.01,99999.99',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $sellInvoice = new Invoice();
        
        // $sellInvoice->serie    = $request->serie;
        $sellInvoice->subtotal = $request->subtotal;
        $sellInvoice->taxes    = $request->taxes;
        $sellInvoice->discount = $request->discount;
        $sellInvoice->total    = $sellInvoice->subtotal + $sellInvoice->taxes - $sellInvoice->discount;

        $sellInvoice->type     = 'S';
        $sellInvoice->user_id  = Auth::user()->id;
        
        $sellInvoice->save();

        return $this->sendResponse($sellInvoice->toArray(), 'Sell invoice created successfully.');  
    }

    public function createInvoiceDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required|integer',
            'quantity'   => 'required|integer|min:1',
            'price'      => 'required|numeric|between:0.01,99999.99',
            // 'total'      => 'required|numeric|between:0.01,99999.99',
            'invoice_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // update stock
        $itemController = new ItemController();
        $stockResponse = $itemController->updateStock($request->item_id, $request->quantity);

        if(json_decode($stockResponse->content(),true)['status']){
            
            $invoiceDetail = new InvoiceDetail();
        
            $invoiceDetail->item_id    = $request->item_id;
            $invoiceDetail->quantity   = $request->quantity;
            $invoiceDetail->price      = $request->price;
            $invoiceDetail->total      = $invoiceDetail->quantity * $invoiceDetail->price;
            $invoiceDetail->invoice_id = $request->invoice_id;
            
            $invoiceDetail->save();

            return $this->sendResponse($invoiceDetail->toArray(), 'Invoice detail created successfully.');
        }
        else{
            return $this->sendError(json_decode($stockResponse->content(),true)['message']);
        }
    }

    
}
