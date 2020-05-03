<?php

namespace App\Http\Controllers\API;

use App\InvoiceDetail;
use App\Invoice;
use App\Item;

use App\Actions\Item\ItemHasStock;
use App\Actions\Item\ItemIsStocked;
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
            'price'    =>  'numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::findOrFail($request->item_id);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $invoiceType = $invoice->getType();
        $typeSell = $invoice->getTypeForSell();
        $typePurchase = $invoice->getTypeForPurchase();

        //Validation only for a Sell Invoice
        if($invoiceType == $typeSell){
            $this->businessValidations([
                new ItemHasStock($item , $request->quantity),
            ], [ new InvoiceAnull($invoice)]);
        }else if($invoiceType == $typePurchase){
            $this->businessValidations([
                new ItemIsStocked($item),
            ], [ new InvoiceAnull($invoice)]);
        }


        $this->businessValidations([
            new InvoiceIsOpened($invoice),
            new BelongsToStore(Auth::user(), [$item , $invoice ]),
        ], [ new InvoiceAnull($invoice)]);
       

        //Set price according to invoice type
        $price = 0;
        if($invoiceType == $typeSell){
            $price    = $item->price;
        }else if($invoiceType == $typePurchase){
            $price   = $request->price;
        }

        $invoiceDetail = new InvoiceDetail();
        $invoiceDetail->item_id    = $request->item_id;
        $invoiceDetail->quantity   = $request->quantity;
        $invoiceDetail->price      = $price;
        $invoiceDetail->invoice_id = $request->invoice_id;
        $invoiceDetail->calculateTotal();

        $this->businessValidations([ new InvoiceHasEnoughSubtotal($invoice, $invoiceDetail)], 
                                   [ new InvoiceAnull($invoice)]
        );

        $invoiceDetail->save();

        //Update stock
        //Set price according to invoice type
        if($invoiceType == $typeSell){
            $item->decreaseStock($invoiceDetail->quantity);
        }else if($invoiceType == $typePurchase){
            $item->increaseStock($invoiceDetail->quantity);
        }
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
