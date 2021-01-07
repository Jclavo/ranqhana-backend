<?php

namespace App\Http\Controllers\API;

use App\Models\InvoiceDetail;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\InvoiceType;
use App\Models\OrderStage;
use App\Models\InvoiceStage;

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
use Carbon\Carbon;

//Services
use App\Services\LanguageService;

//Utils
use App\Utils\MoreUtils;

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
            'quantity'   => 'required|numeric|gt:0',
            'item_id'    => 'required|exists:items,id'
        ]);

        $validator->sometimes('price', 'required|numeric|regex:/^[0-9]{1,6}+(?:\.[0-9]{1,2})?$/', function ($request) {
            return $request->price > 0;
        });

        $validator->sometimes('invoice_id', 'required|exists:invoices,id', function ($request) {
            return $request->invoice_id > 0;
        });

        $validator->sometimes('type_id', 'required|exists:invoice_types,id', function ($request) {
            return !isset($request->invoice_id);
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = Item::findOrFail($request->item_id);

        //check if INVOICE ID exist
        if(isset($request->invoice_id) && $request->invoice_id > 0){
            $invoice = Invoice::findOrFail($request->invoice_id);
        }else{
            //create the new invoice
            $invoice = new Invoice();
            //set initial values
            $invoice->type_id  = $request->type_id;
            $invoice->user_id = Auth::user()->id;
            $invoice->stage_id = InvoiceStage::getForDraft();
            $invoice->subtotal = 0;
            $invoice->serie = MoreUtils::generateInvoiceCorrelativeSerie(Invoice::class, $invoice->type_id );
            $invoice->save();
    
            $invoice->order()->create(['stage_id' => 1, 'delivery_date' => Carbon::now(), 'serie' => $invoice->serie]); 
            
            $invoice = Invoice::findOrFail($invoice->id);
        }

        $invoiceType = $invoice->getType();
        $order = $invoice->order;

        if($item->price <= 0 && $invoiceType == InvoiceType::getForSell()){
            return $this->sendError('Can not be added an item with price $0.');    
        }

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
                                        ->where('invoice_id',$invoice->id)
                                        ->first();

        if(is_null($invoiceDetail)){
            $invoiceDetail = new InvoiceDetail();
            $invoiceDetail->item_id    = $request->item_id;
            $invoiceDetail->quantity   = $request->quantity;
            $invoiceDetail->price      = $price;
            $invoiceDetail->invoice_id = $invoice->id;
        }else{
            $invoiceDetail->quantity   = $invoiceDetail->quantity + $request->quantity;
            $invoiceDetail->price      = $price;
        }
        
        $invoiceDetail->calculateTotal();
        $invoiceDetail->save();

        $invoice->load(['order','details.item.unit']);

        return $this->sendResponse($invoice->toArray(),$this->languageService->getSystemMessage('crud.create'));
    
    }

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
