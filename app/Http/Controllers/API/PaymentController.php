<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStage;
use App\Models\Invoice;
use App\Models\InvoiceStage;

use App\Http\Controllers\ResponseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; 

use Carbon\Carbon;

//Services
use App\Services\LanguageService;

//Utils
use App\Utils\CustomCarbon;

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
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $validator->sometimes('method_id', 'required|exists:payment_methods,id', function ($input) {
            return $input->method_id > 0;
        });

        // $validator->sometimes('payment_date', 'date|after_or_equal:today', function ($input) {
        //     return !empty($input->payment_date);
        // });

        if(!empty($request->payment_date)){
            
            $now = CustomCarbon::UTCtoCountryTZ(Carbon::now())->startOfDay();
            $dateTimeObject = Carbon::parse($request->payment_date)->startOfDay();
            $date_diff = $now->diffInDays($dateTimeObject);

            if($date_diff > 0){
                return $this->sendError('The payment date can not be in the past');
            }
        }

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        //Get values 
        $amount = $request->amount;
        $payment_date = $request->payment_date ?  $request->payment_date : Carbon::now();
        $invoice_id = $request->invoice_id;
        $method_id = $request->method_id ? $request->method_id : PaymentMethod::getMethodMoney();
        
        /**
         * Validation section
         */

        $invoice = Invoice::findOrFail($invoice_id);

        // It is missing to validate if the invoice is already payed

        if($invoice->stage_id == InvoiceStage::getForPaid()){
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
        $payment->method_id = $method_id;
        $payment->stage_id  = PaymentStage::getForWaiting();
        $payment->save();

        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::findOrFail($id); 
                
        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.read'));
    
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
            'method_id' => 'required|exists:payment_methods,id',
        ]);

        $validator->sometimes('money', 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/', function ($input) {
            return $input->method_id == PaymentMethod::getMethodMoney();
        });

        $validator->sometimes('transaction_code', 'required|min:8', function ($input) {
            return $input->method_id == PaymentMethod::getMethodCard() && !empty($input->transaction_code);
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        //Get values 
        $method_id = $request->method_id;
        $money = 0.0;
        $transaction_code = '';
        switch ($method_id) {
            case PaymentMethod::getMethodMoney():
                $money = $request->money;
                break;
            case PaymentMethod::getMethodCard():
                $transaction_code = $request->transaction_code ?? "";
                break;
            default:
                # code...
                break;
        }
        
        /**
         * Validation section
         */

        $payment = Payment::findOrFail($id);

        // validate if payment is already payed
        if($payment->stage_id == PaymentStage::getForPaid()){
            return $this->sendError('This payment is already paid.');
        }
        
        //validate money only if the payment is in cash
        if($method_id == PaymentMethod::getMethodMoney()){
            if($money < $payment->amount){
                return $this->sendError('The money is less than the amount to pay.');
            }
        }

        //update values
        $payment->money = $money;
        $payment->method_id = $method_id;
        $payment->real_payment_date =  Carbon::now();
        $payment->stage_id  = PaymentStage::getForPaid();
        $payment->transaction_code  = $transaction_code;
        $payment->save();


        //Check if all the payments are ok to close Invoice
        $invoice = Invoice::findOrFail($payment->invoice_id);
        //calculate last payments
        $lastPaymentsAmount = $this->getLastPaymentsAmountPayed($invoice->id);
        if($lastPaymentsAmount == $invoice->total){
            $invoice->stage_id = InvoiceStage::getForPaid();
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
        if($payment->stage_id == PaymentStage::getForPaid()){
            return $this->sendError($this->languageService->getSystemMessage('stage.already-paid'));
        }

        $payment->setStageAnulled();
        $payment->save();
        $payment->delete();

        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.annulled'));
    }

    /**
     * Get Payments by invoice
     */
    public function byInvoice(int  $invoice_id){

        $payments = Payment::whereHas('invoice', function ($query) use($invoice_id) {
            $query->where('invoices.id', '=', $invoice_id);
        })
        ->orderBy('payment_date')
        ->get();

        //Update state if date is delayed
        foreach ($payments as $payment) {
            if($payment->stage_id == PaymentStage::getForWaiting()){
                if($payment->payment_date < Carbon::now()->toDateString()){
                    $payment->stage_id = PaymentStage::getForDelayed();   
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
                                // ->where('stage_id',PaymentStage::getForPaid())
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
                                ->where('stage_id',PaymentStage::getForPaid())
                                ->get();

        foreach ($lastPayments as $lastPayment) {
            $lastPaymentsAmount += $lastPayment->amount;
        }

        return $lastPaymentsAmount;
    }


    /**
     * Update Payment date
     */
    public function updatePaymentDate(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:payments,id',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $payment = Payment::findOrFail($request->id);

        //validate depends on Stage
        switch ($payment->stage_id) {
            case PaymentStage::getForCanceled():
                return $this->sendError($this->languageService->getSystemMessage('stage.already-canceled'));
                break;
            case PaymentStage::getForPaid():
                return $this->sendError($this->languageService->getSystemMessage('stage.already-paid'));
                break;
            default:
                # code...
                break;
        }

        $payment->payment_date = Carbon::parse($request->payment_date);
        $payment->save();
                
        return $this->sendResponse($payment->toArray(), $this->languageService->getSystemMessage('crud.update-date'));
    }

}
