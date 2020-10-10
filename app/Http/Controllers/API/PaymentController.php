<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStage;
use App\Models\Invoice;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

use Carbon\Carbon;

//Services
use App\Services\LanguageService;

class PaymentController extends ResponseController
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'amount' => 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/',
            'payment_date' => 'sometimes|date|after_or_equal:today',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $validator->sometimes('payment_method_id', 'required|exists:payment_methods,id', function ($input) {
            return $input->payment_method_id > 0;
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        //Get values 
        $amount = $request->amount;
        $payment_date = $request->payment_date ?  $request->payment_date : Carbon::now();;
        $invoice_id = $request->invoice_id;
        $payment_method_id = $request->payment_method_id ? $request->payment_method_id : PaymentMethod::getMethodMoney();
        
        /**
         * Validation section
         */

        $invoice = Invoice::findOrFail($invoice_id);

        // It is missing to validate if the invoice is already payed

        if($invoice->stage_id == Invoice::getStagePaid()){
            return $this->sendError('The payment can not be created because the invoice was already paid.');
        }

        /**
         * SECTION validate amount should be equal or less than Invoice Total
         */

        //calculate last payments
        $lastPaymentsAmount = $this->getLastPaymentsAmount($invoice_id);

        if ($amount > $invoice->total) {
            return $this->sendError('The payment should not be greater than the total invoice.');
        }

        $remainAmount = $invoice->total - $lastPaymentsAmount;
        if ($remainAmount == 0 ) {
            return $this->sendError('All the payments for this invoice were already created.');  
        }else if($amount > $remainAmount){
            return $this->sendError('The payment should not be greater than the remaining payment.');
        }

        $payment = new Payment();
        $payment->amount    = $amount;
        $payment->payment_date =  $payment_date;
        $payment->invoice_id    = $invoice_id;
        $payment->payment_method_id = $payment_method_id;
        $payment->payment_stage_id  = PaymentStage::getStageWaiting();
        $payment->save();

        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $validator->sometimes('money', 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', function ($input) {
            return $input->payment_method_id == PaymentMethod::getMethodMoney();
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        //Get values 
        $money = $request->money;
        $payment_method_id = $request->payment_method_id;
        
        /**
         * Validation section
         */

        $payment = Payment::findOrFail($id);

        // validate if payment is already payed
        if($payment->payment_stage_id == PaymentStage::getStagePaid()){
            return $this->sendError('This payment is already paid.');
        }
        
        //validate money only if the payment is in cash
        if($payment_method_id == PaymentMethod::getMethodMoney()){
            if($money < $payment->amount){
                return $this->sendError('The money is less than the amount to pay.');
            }
        }

        //update values
        $payment_method_id == PaymentMethod::getMethodMoney() ? $payment->money = $money : null;
        $payment->payment_method_id = $payment_method_id;
        $payment->real_payment_date =  Carbon::now();
        $payment->payment_stage_id  = PaymentStage::getStagePaid();
        $payment->save();


        //Check if all the payments are ok to close Invoice
        $invoice = Invoice::findOrFail($payment->invoice_id);
        //calculate last payments
        $lastPaymentsAmount = $this->getLastPaymentsAmountPayed($invoice->id);
        if($lastPaymentsAmount == $invoice->total){
            $invoice->stage_id = Invoice::getStagePaid();
            $invoice->save();
        }

        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.update'));  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $payment = Payment::findOrFail($id);

        // validate if payment is already payed
        if($payment->payment_stage_id == PaymentStage::getStagePaid()){
            return $this->sendError('This payment can not be Anull because it is already paid.');
        }

        $payment->delete();

        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('payment.anull'));
    }

    /**
     * Get Payments by invoice
     */
    public function byInvoice(int  $invoice_id){

        $payments = Payment::whereHas('invoice', function ($query) use($invoice_id) {
            $query->where('invoices.id', '=', $invoice_id);
        })
        ->get();

        //Update state if date is delayed
        foreach ($payments as $payment) {
            if($payment->payment_stage_id == PaymentStage::getStageWaiting()){
                if($payment->payment_date < Carbon::now()){
                    $payment->payment_stage_id = PaymentStage::getStageDelayed();   
                    $payment->save();
                }
            }
        }

        // load relationships
        $payments->load(['paymentMethod','paymentStage']);


        return $this->sendResponse($payments->toArray(), $this->languageService->getSystemMessage('crud.read'));
    }


    /**
     * Get last payments amount
     */
    private function getLastPaymentsAmount(int $invoice_id){
        $lastPaymentsAmount = 0;

        $lastPayments = Payment::where('invoice_id',$invoice_id)
                                // ->where('payment_stage_id',PaymentStage::getStagePaid())
                                ->get();

        foreach ($lastPayments as $lastPayment) {
            $lastPaymentsAmount += $lastPayment->amount;
        }

        return $lastPaymentsAmount;
    }

    /**
     * Get last payments amount payed
     */
    private function getLastPaymentsAmountPayed(int $invoice_id){
        $lastPaymentsAmount = 0;

        $lastPayments = Payment::where('invoice_id',$invoice_id)
                                ->where('payment_stage_id',PaymentStage::getStagePaid())
                                ->get();

        foreach ($lastPayments as $lastPayment) {
            $lastPaymentsAmount += $lastPayment->amount;
        }

        return $lastPaymentsAmount;
    }
}