<?php

namespace App\Http\Controllers\API;

use App\Models\InvoiceDetail;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\InvoiceType;
use App\Models\OrderStage;

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

//Services
use App\Services\LanguageService;

class InvoiceDetailController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();

        //permission middleware
        $this->middleware('permission_in_role:invoices_list/read');
        $this->middleware('permission_in_role:invoices_list/delete', ['only' => ['destroy', 'anull']]);
        
        $this->middleware('permission_in_role:sell_invoice/create|permission_in_role:purchase_invoice/create', ['only' => ['store','generate' ]]);
        // $this->middleware('permission_in_role:sell_invoice/update|permission_in_role:purchase_invoice/update', ['only' => ['update']]);
           
    }

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
            'quantity'   => 'required|numeric|gt:0|regex:/^[0-9]{1,5}+(?:\.[0-9]{1,2})?$/',
            'item_id'    => 'required|exists:items,id',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $validator->sometimes('price', 'required|numeric|regex:/^[0-9]{1,5}+(?:\.[0-9]{1,2})?$/', function ($input) {
            return $input->price > 0;
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::findOrFail($request->item_id);
        $invoice = Invoice::findOrFail($request->invoice_id);
        $invoiceType = $invoice->getType();
        $order = $invoice->order;

        if($order->stage_id != OrderStage::getForNew()){
            return $this->sendError('There is not possible to add more items to this order.');
        }

        if($invoiceType == InvoiceType::getForSell()){
            $this->businessValidations([new ItemHasStock($item , $request->quantity)]);
        }
        else if($invoiceType == InvoiceType::getForPurchase()){
            $this->businessValidations([new ItemIsStocked($item)]);
        }

        $this->businessValidations([new BelongsToStore(Auth::user(), [$item , $invoice ])]);
       
        //Set price according to invoice type
        $price = 0;
        if($invoiceType == InvoiceType::getForSell()){
            $price    = $item->price;
        }else if($invoiceType == InvoiceType::getForPurchase()){
            $price   = $request->price;
        }


        $invoiceDetail = InvoiceDetail::select('invoice_details.*')
                                        ->where('item_id',$request->item_id)
                                        ->where('invoice_id',$request->invoice_id)
                                        ->first();

        if(is_null($invoiceDetail)){
            $invoiceDetail = new InvoiceDetail();
            $invoiceDetail->item_id    = $request->item_id;
            $invoiceDetail->quantity   = $request->quantity;
            $invoiceDetail->price      = $price;
            $invoiceDetail->invoice_id = $request->invoice_id;
        }else{
            $invoiceDetail->quantity   = $invoiceDetail->quantity + $request->quantity;
            $invoiceDetail->price      = $price;
        }
        
        $invoiceDetail->calculateTotal();
        $invoiceDetail->save();

        return $this->sendResponse($invoiceDetail->toArray(),$this->languageService->getSystemMessage('crud.create'));
    
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'quantity'   => 'required|numeric|gt:0|regex:/^[0-9]{1,5}+(?:\.[0-9]{1,2})?$/',
    //         'price'    =>   'numeric|gt:0|regex:/^[0-9]{1,5}+(?:\.[0-9]{1,2})?$/',
    //         'item_id'    => 'required|exists:items,id',
    //         'invoice_id' => 'required|exists:invoices,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors()->first());
    //     }

    //     $item = Item::findOrFail($request->item_id);

    //     $invoice = Invoice::findOrFail($request->invoice_id);
    //     $invoiceType = $invoice->getType();
    //     $typeSell = $invoice->getForSell();
    //     $typePurchase = $invoice->getForPurchase();

    //     //Validation only for a Sell Invoice
    //     if($invoiceType == $typeSell){
    //         $this->businessValidations([
    //             new ItemHasStock($item , $request->quantity),
    //         ], [ new InvoiceAnull($invoice)]);
    //     }
    //     else if($invoiceType == $typePurchase){
    //         $this->businessValidations([
    //             new ItemIsStocked($item),
    //         ], [ new InvoiceAnull($invoice)]);
    //     }


    //     $this->businessValidations([
    //         new InvoiceIsOpened($invoice),
    //         new BelongsToStore(Auth::user(), [$item , $invoice ]),
    //     ], [ new InvoiceAnull($invoice)]);
       

    //     //Set price according to invoice type
    //     $price = 0;
    //     if($invoiceType == $typeSell){
    //         $price    = $item->price;
    //     }else if($invoiceType == $typePurchase){
    //         $price   = $request->price;
    //     }

    //     $invoiceDetail = new InvoiceDetail();
    //     $invoiceDetail->item_id    = $request->item_id;
    //     $invoiceDetail->quantity   = $request->quantity;
    //     $invoiceDetail->price      = $price;
    //     $invoiceDetail->invoice_id = $request->invoice_id;
    //     $invoiceDetail->calculateTotal();

    //     $this->businessValidations([ new InvoiceHasEnoughSubtotal($invoice, $invoiceDetail)], 
    //                                [ new InvoiceAnull($invoice)]
    //     );

    //     $invoiceDetail->save();

    //     //Update stock
    //     //Set price according to invoice type
    //     if($invoiceType == $typeSell){
    //         $item->decreaseStock($invoiceDetail->quantity);
    //     }else if($invoiceType == $typePurchase){
    //         $item->increaseStock($invoiceDetail->quantity);
    //     }
    //     $item->save();

    //     return $this->sendResponse($invoiceDetail->toArray(),$this->languageService->getSystemMessage('crud.create'));
    
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvoiceDetail  $invoiceDetail
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_id)
    {

        $invoiceDetails = InvoiceDetail::whereHas('invoice', function ($query) use($invoice_id) {
            $query->where('invoices.id', '=', $invoice_id);
        })
        ->select(['invoice_details.*'])
        ->with(['item.unit'])
        ->get();


        // $invoiceDetails = InvoiceDetail::select('invoice_details.*', 'items.name as item', 'units.code as unit')
        //                 ->join('invoices', function ($join) use($invoice_id){
        //                     $join->on('invoice_details.invoice_id', '=', 'invoices.id')
        //                         ->where('invoices.id', '=', $invoice_id);
        //                 })
        //                 ->join('items', 'invoice_details.item_id', '=', 'items.id')
        //                 ->join('units', 'items.unit_id', '=', 'units.id')
        //                 ->get();

        if($invoiceDetails->isEmpty()){
            return $this->sendError($this->languageService->getSystemMessage('invoice.detail-not-found'));
        }else{
            return $this->sendResponse($invoiceDetails, $this->languageService->getSystemMessage('crud.read'));
        }
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
    public function destroy(int $id)
    {
        $InvoiceDetail = InvoiceDetail::select('invoice_details.*')->findOrFail($id);
        
        $InvoiceDetail->delete();

        return $this->sendResponse($InvoiceDetail->toArray(), $this->languageService->getSystemMessage('crud.delete'));
    
    }

}
