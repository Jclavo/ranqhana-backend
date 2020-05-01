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
use App\Actions\Invoice\InvoiceAnull;
use App\Actions\BelongsToStore;

use App\Http\Controllers\API\ItemController;
use Carbon\Carbon;
use App\Utils\CustomCarbon;
use App\Utils\UserUtils;

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
        $invoice = Invoice::findOrFail($id);

        $this->businessValidations([
            new BelongsToStore(Auth::user(), [$invoice]),
        ]);

        $invoice->serie    = $request->serie;
        // $sellInvoice->client   = $request->client;

        $invoice->save();

        return $this->sendResponse($invoice->toArray(), 'Invoice updated successfully.');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
    }

    /**
     * 
     */

    public function pagination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'subtotal' => 'required|numeric|gt:0',
            // 'taxes' => 'numeric|gte:0|lt:subtotal',
            // 'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $sortArray = array("asc", "ASC", "desc", "DESC");

        // SearchOptions values
        $per_page      = $request->per_page;
        $sortColumn    = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        $searchValue   = $request->searchValue;
        $fromDate      = $request->fromDate;
        $toDate        = $request->toDate;
        $type_id       = $request->type_id;

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

            $timezome = UserUtils::getTimezone(Auth::user());
            //change date format
            $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($per_page);
 
        return $this->sendResponse($results->items(), 'Invoices retrieved successfully.', $results->total() );

    }
     
    /**
     * 
     */

    public function anull(int $id)
    {
        $invoice = Invoice::findOrFail($id);

        $this->businessValidations([
            new BelongsToStore(Auth::user(), [$invoice]),
        ]);

        $this->businessActions([ new InvoiceAnull($invoice)]);

        return $this->sendResponse($invoice->toArray(), 'Invoice Anulled successfully.');
    }


    
}
