<?php

namespace App\Http\Controllers\API;

use App\Invoice;
use App\InvoiceDetail;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
            'subtotal' => 'required|numeric|gt:0',
            'taxes' => 'numeric|gte:0|lt:subtotal',
            'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $sellInvoice = new Invoice();
        
        $sellInvoice->serie    = $request->serie;
        $sellInvoice->subtotal = $request->subtotal;
        $sellInvoice->taxes    = $request->taxes;
        $sellInvoice->discount = $request->discount;
        $sellInvoice->total    = $sellInvoice->subtotal + $sellInvoice->taxes - $sellInvoice->discount;
        $sellInvoice->type_id  = $request->type_id;
        $sellInvoice->user_id  = Auth::user()->id;
        $sellInvoice->store_id  = Auth::user()->store_id;

        //Update stage
        $sellInvoice->setStagePaid();
        
        $sellInvoice->save();

        return $this->sendResponse($sellInvoice->toArray(), 'Sell invoice created successfully.');  
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
    public function destroy(int $id)
    {
        $invoice = Invoice::find($id);

        if (is_null($invoice)) {
            return $this->sendError('Invoice not found.');
        }

        $belongs = User::where('id','=',Auth::user()->id)
                        ->where('store_id','=',Auth::user()->store_id)
                        ->get();
        
        
        if(count($belongs) == 0) {
            return $this->sendError('Invoice does not belongs to the logged user.');
        }

        $invoice->delete();

        return $this->sendResponse($invoice->toArray(), 'Invoice Anulled/Canceled successfully.');
    }

    /**
     * 
     */

    public function pagination(Request $request)
    {
        $sortArray = array("asc", "ASC", "desc", "DESC");

        $store_id = $request->store_id;
        // SearchOptions values
        $per_page      = $request->searchOption['per_page'];
        $sortColumn    = $request->searchOption['sortColumn'];
        $sortDirection = $request->searchOption['sortDirection'];
        $searchValue   = $request->searchOption['searchValue'];
        $fromDate      = $request->searchOption['fromDate'];
        $toDate        = $request->searchOption['toDate'];
        $type_id       = $request->searchOption['type_id'];

        // Initialize values if they are empty.
        if (empty($per_page)) {
            $per_page = 20;
        }

        if (!in_array($sortDirection, $sortArray)) {
            $sortDirection = "DESC";
            $sortColumn = "created_at";
        }

        if (empty($sortColumn)) {
            $sortColumn = "created_at";
        }
        
        if (empty($type)) {
            $type = "S";
        }

        $query = Invoice::query();

        $query->select('invoices.*');

        $query->where('type_id', '=', $type_id);

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('serie', 'like', '%'. $searchValue .'%')
                     ->orWhere('total', 'like', '%'. $searchValue .'%');
        });

        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {
            // return $q->whereBetween('created_at', [$fromDate,$toDate]);
            return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($per_page);
 
        return $this->sendResponse($results->items(), 'Invoices retrieved successfully.', $results->total() );

    }
     
    /**
     * 
     */

    // public function createSellInvoice(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'subtotal' => 'required|numeric|between:0.01,99999.99',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors()->first());
    //     }

    //     $sellInvoice = new Invoice();
        
    //     // $sellInvoice->serie    = $request->serie;
    //     $sellInvoice->subtotal = $request->subtotal;
    //     $sellInvoice->taxes    = $request->taxes;
    //     $sellInvoice->discount = $request->discount;
    //     $sellInvoice->total    = $sellInvoice->subtotal + $sellInvoice->taxes - $sellInvoice->discount;

    //     $sellInvoice->type     = 'S';
    //     $sellInvoice->user_id  = Auth::user()->id;
        
    //     $sellInvoice->save();

    //     return $this->sendResponse($sellInvoice->toArray(), 'Sell invoice created successfully.');  
    // }

    // public function createInvoiceDetail(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'item_id'    => 'required|integer',
    //         'quantity'   => 'required|integer|min:1',
    //         'price'      => 'required|numeric|between:0.01,99999.99',
    //         // 'total'      => 'required|numeric|between:0.01,99999.99',
    //         'invoice_id' => 'required|integer',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors()->first());
    //     }

    //     // update stock
    //     $itemController = new ItemController();
    //     $stockResponse = $itemController->updateStock($request->item_id, $request->quantity);

    //     if(json_decode($stockResponse->content(),true)['status']){
            
    //         $invoiceDetail = new InvoiceDetail();
        
    //         $invoiceDetail->item_id    = $request->item_id;
    //         $invoiceDetail->quantity   = $request->quantity;
    //         $invoiceDetail->price      = $request->price;
    //         $invoiceDetail->total      = $invoiceDetail->quantity * $invoiceDetail->price;
    //         $invoiceDetail->invoice_id = $request->invoice_id;
            
    //         $invoiceDetail->save();

    //         return $this->sendResponse($invoiceDetail->toArray(), 'Invoice detail created successfully.');
    //     }
    //     else{
    //         return $this->sendError(json_decode($stockResponse->content(),true)['message']);
    //     }
    // }

    
}
