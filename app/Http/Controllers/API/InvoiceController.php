<?php

namespace App\Http\Controllers\API;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\User;
use App\Models\PaymentType;
use App\Models\PaymentStage;
use App\Models\PaymentMethod;
use App\Models\InvoiceStage;
use App\Models\InvoiceType;
use App\Models\Item;
use App\Models\Order;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//Actions
use App\Actions\Item\ItemHasStock;
use App\Actions\Item\ItemIsStocked;
use App\Actions\Invoice\InvoiceHasEnoughSubtotal;
use App\Actions\Invoice\InvoiceAnull;

//Utils
use App\Utils\PaginationUtils;
use App\Utils\CustomCarbon;
use App\Utils\MoreUtils;

//Services
use App\Services\LanguageService;

class InvoiceController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();

        //permission middleware
        $this->middleware('permission_in_role:invoices_list/read');
        $this->middleware('permission_in_role:invoices_list/pagination', ['only' => ['pagination']]);
        $this->middleware('permission_in_role:invoices_list/delete', ['only' => ['destroy', 'anull']]);
     
        $this->middleware('permission_in_role:sell_invoice/create|permission_in_role:purchase_invoice/create', ['only' => ['store','generate' ]]);
        $this->middleware('permission_in_role:sell_invoice/update|permission_in_role:purchase_invoice/update', ['only' => ['update']]);
   
    }

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
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = new Invoice();
        $invoice->type_id  = $request->type_id;
        $invoice->user_id = Auth::user()->id;
        $invoice->stage_id = InvoiceStage::getForDraft();
        //Initial values
        $invoice->subtotal = 0;
        // $invoice->serie = '0000'; //set a fake value to call a custom function in the model 
        $invoice->serie = MoreUtils::generateInvoiceCorrelativeSerie(Invoice::class, $invoice->type_id );
        $invoice->save();

        $invoice->order()->create(['stage_id' => 1, 'delivery_date' => Carbon::now(), 'serie' => $invoice->serie]);

        $invoice->load('order');

        return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         // "/^-?[0-9]+(?:\.[0-9]{1,2})?$/"
    //         'subtotal' => 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/',
    //         'taxes' => 'numeric|gte:0|lt:subtotal',
    //         'discount' => 'numeric|gte:0|lt:subtotal',
    //         'type_id' => 'required|exists:invoice_types,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError($validator->errors()->first());
    //     }

    //     $invoice = new Invoice();
        
    //     $invoice->serie    = $request->serie;
    //     $invoice->subtotal = $request->subtotal;
    //     $invoice->discount = $request->discount;
    //     $invoice->total    = $invoice->calculateTotal();
    //     $invoice->taxes    = $invoice->total * ( Auth::user()->getCountryTax() / 100 );

    //     //Validate that total is not negative
    //     if($invoice->total < 0){
    //         return $this->sendError($invoice->toArray(),  $this->languageService->getSystemMessage('invoice.total-negative'));       
    //     }

    //     //Set FKs
    //     $invoice->type_id  = $request->type_id;
    //     $invoice->user_id = Auth::user()->id;
    //     $invoice->stage_id = InvoiceStage::getForDraft();
        
    //     $invoice->save();

    //     $invoice->order()->create(['stage_id' => 1]);

    //     return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::with(['type','stage','order'])->findOrFail($id);
                
        return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('crud.read'));
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
        $validator = Validator::make($request->all(), [
            // 'serie' => 'nullable|max:10',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = Invoice::with('payments')->findOrFail($id);
        $invoice->external_user_id = $request->external_user_id;
        

        //check that update payment_type_id is possible
        if($invoice->payment_type_id > 0 && $invoice->payment_type_id != $request->payment_type_id){
            $payments = $invoice->payments;

            // xdebug_break();
            foreach ($payments as $payment) {
                if($payment->stage_id == PaymentStage::getForPaid()){
                    return $this->sendError('At least one payment is already paid.');
                }
            }

            foreach ($payments as $payment) {
                $payment->method_id = PaymentMethod::getForPaymentType($request->payment_type_id);
                $payment->save();

            }
        }
        $invoice->payment_type_id  = $request->payment_type_id;
        
        //change invoice stage
        if($invoice->payment_type_id == PaymentType::getForInternalCredit()){
            $invoice->setStageByInstallment();
        }else{
            $invoice->setStageDraft();
        }

        $invoice->save();
        $invoice->load('payments');

        return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('crud.update'));  
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
            'pageSize' => 'numeric|gt:0',
        ]);

        $validator->sometimes('type_id', 'required|exists:invoice_types,id', function ($input) {
            return $input->type_id > 0;
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'units');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;

        $fromDate      = $request->fromDate;
        $toDate        = $request->toDate;
        $type_id       = $request->type_id;
        
        $query = Invoice::query();

        $query->select('invoices.*');
        $query->whereHas('details');
        $query->with(['stage','type']);
        $query->whereNotIn('stage_id', [InvoiceStage::getForDraft()]);

        $query->when((!empty($type_id)), function ($q) use($type_id) {
            return $q->where('type_id', '=', $type_id);
        });

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('serie', 'like', '%'. $searchValue .'%');
        });

        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome =Auth::user()->getTimezone();
            //change date format
            $fromDate = CustomCarbon::countryTZtoUTC($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::countryTZtoUTC($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[ $fromDate, $toDate]);
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);       
 
        return $this->sendResponse($results->items(), $this->languageService->getSystemMessage('crud.pagination'), $results->total() );

    }
     
    /**
     * 
     */

    public function anull(int $id)
    {
        $invoice = Invoice::findOrFail($id);

        $this->businessActions([ new InvoiceAnull($invoice)]);

        return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('invoice.anull'));
    }


    /**
     * update stock
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:invoices,id',
            'discount' => 'numeric|gte:0'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = Invoice::with(['details'])->findOrFail($request->id);
        $invoiceType = $invoice->getType();

        if($invoice->stage_id != InvoiceStage::getForDraft()){
            return $this->sendError('There stock was already updated for this invoice.');
        }

        if(count($invoice->details) == 0){
            return $this->sendError('The invoice has no items.');
        }

        $details = $invoice->details;
        $invoice->subtotal = 0;

        //beginTransaction
        DB::beginTransaction();

        //Update state if date is delayed
        foreach ($details as $detail) {

            $item = Item::findOrFail($detail->item_id);

            if($invoiceType == InvoiceType::getForSell()){
                $this->businessValidations([
                    new ItemHasStock($item , $detail->quantity),
                ], [ new InvoiceAnull($invoice)]);
            }
            else if($invoiceType == InvoiceType::getForPurchase()){
                $this->businessValidations([
                    new ItemIsStocked($item),
                ], [ new InvoiceAnull($invoice)]);
            }

            //Update stock
            //Set price according to invoice type
            if($invoiceType == InvoiceType::getForSell()){
                $item->decreaseStock($detail->quantity);
            }else if($invoiceType == InvoiceType::getForPurchase()){
                $item->increaseStock($detail->quantity);
            }
            $item->save();

            //sum subtotal
            $invoice->subtotal += ($detail->price * $detail->quantity);
            
        }

        $invoice->discount = $request->discount;
        $invoice->total    = $invoice->calculateTotal();
        $invoice->taxes    = $invoice->total * ( Auth::user()->getCountryTax() / 100 );

        //Validate that total is not negative
        if($invoice->total < 0){
            return $this->sendError([],  $this->languageService->getSystemMessage('invoice.total-negative'));       
        }

        //Validate that total is not negative
        if($invoice->discount > $invoice->total){
            return $this->sendError([],  $this->languageService->getSystemMessage('invoice.discount-incorrect'));       
        }

        $invoice->setStageStockUpdated();
        $invoice->save();

        //end transaction
        DB::commit();

        return $this->sendResponse([], $this->languageService->getSystemMessage('crud.update'));
    }
}
