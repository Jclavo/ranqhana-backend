<?php

namespace App\Http\Controllers\API;

use App\InvoiceDetail;
use App\Invoice;
use App\Item;

use App\Actions\Item\ItemHasStock;
use App\Actions\Invoice\InvoiceIsOpened;
use App\Actions\Invoice\InvoiceHasEnoughSubtotal;
use App\Actions\Invoice\InvoiceAnull;
use App\Actions\BelongsToStore;

use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 


class InvoiceDetailController extends ResponseController
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
        $validator = Validator::make($request->all(), [
            'item_id'    => 'required|exists:items,id',
            'quantity'   => 'required|numeric|gt:0',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::findOrFail($request->item_id);
        $invoice = Invoice::findOrFail($request->invoice_id);

        $this->businessValidations([
            new ItemHasStock($item , $request->quantity),
            new InvoiceIsOpened($invoice),
            new BelongsToStore(Auth::user(), [$item , $invoice ]),
        ], [ new InvoiceAnull($invoice)]);

           
        $invoiceDetail = new InvoiceDetail();
    
        $invoiceDetail->item_id    = $request->item_id;
        $invoiceDetail->quantity   = $request->quantity;
        $invoiceDetail->price      = $item->price;
        $invoiceDetail->invoice_id = $request->invoice_id;
        $invoiceDetail->calculateTotal();

        $this->businessValidations([ new InvoiceHasEnoughSubtotal($invoice, $invoiceDetail)], 
                                   [ new InvoiceAnull($invoice)]
        );

        $invoiceDetail->save();

        //Update stock
        $item->decreaseStock($invoiceDetail->quantity);
        $item->save();

        return $this->sendResponse($invoiceDetail->toArray(), 'Invoice detail created successfully.');
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvoiceDetail  $invoiceDetail
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceDetail $invoiceDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvoiceDetail  $invoiceDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceDetail $invoiceDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InvoiceDetail  $invoiceDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceDetail $invoiceDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvoiceDetail  $invoiceDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceDetail $invoiceDetail)
    {
        //
    }
}
