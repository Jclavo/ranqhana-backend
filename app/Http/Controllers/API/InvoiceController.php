<?php

namespace App\Http\Controllers\API;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\User;
use App\Models\PaymentType;
use App\Models\InvoiceStages;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

//Actions
use App\Actions\Invoice\InvoiceAnull;
use App\Actions\BelongsToStore;

//Utils
use App\Utils\PaginationUtils;
use App\Utils\CustomCarbon;

//Services
use App\Services\LanguageService;

class InvoiceController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();
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
            // "/^-?[0-9]+(?:\.[0-9]{1,2})?$/"
            'subtotal' => 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/',
            'taxes' => 'numeric|gte:0|lt:subtotal',
            'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = new Invoice();
        
        $invoice->serie    = $request->serie;
        $invoice->subtotal = $request->subtotal;
        $invoice->discount = $request->discount;
        $invoice->total    = $invoice->calculateTotal();
        $invoice->taxes    = $invoice->total * ( Auth::user()->getCountryTax() / 100 );

        //Validate that total is not negative
        if($invoice->total < 0){
            return $this->sendError($invoice->toArray(),  $this->languageService->getSystemMessage('invoice.total-negative'));       
        }

        //Set FKs
        $invoice->type_id  = $request->type_id;
        $invoice->user_id = Auth::user()->getLocalUserID();
        $invoice->stage_id = InvoiceStages::getStageDraft();
        
        $invoice->save();

        return $this->sendResponse($invoice->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::with(['type','stage'])->findOrFail($id);
                
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
            'serie' => 'nullable|max:10',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        $validator->sometimes('external_user_id', 'required', function ($input) {
            return $input->payment_type_id == PaymentType::getTypeCredit();
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = Invoice::findOrFail($id);

        $invoice->serie            = $request->serie;
        $invoice->external_user_id = $request->external_user_id;
        $invoice->payment_type_id  = $request->payment_type_id;

        //change invoice stage
        if($invoice->payment_type_id == Invoice::getPaymentTypeCredit()){
            $invoice->setStageByInstallment();
        }else{
            $invoice->setStageDraft();
        }

        $invoice->save();

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
            'type_id' => 'required|exists:invoice_types,id',
            'pageSize' => 'numeric|gt:0',
        ]);

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
        
        // if (empty($type)) {
        //     $type = "S";
        // }

        $query = Invoice::query();

        $query->select('invoices.*');
        $query->whereHas('details');
        $query->with('stage');

        $query->where('type_id', '=', $type_id);

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('serie', 'like', '%'. $searchValue .'%')
                     ->orWhere('total', 'like', '%'. $searchValue .'%');
        });

        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome =Auth::user()->getTimezone();
            //change date format
            $fromDate = CustomCarbon::countryTZtoUTC($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::countryTZtoUTC($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
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
}
