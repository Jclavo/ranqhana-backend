<?php

namespace App\Http\Controllers\API;

use App\Models\PaymentStage;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

//Services
use App\Services\LanguageService;

class PaymentStageController extends ResponseController
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
        $paymentStage = PaymentStage::all();
      
        return $this->sendResponse($paymentStage, $this->languageService->getSystemMessage('crud.read'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentStages  $paymentStages
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentStages $paymentStages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentStages  $paymentStages
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentStages $paymentStages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentStages  $paymentStages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentStages $paymentStages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentStages  $paymentStages
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentStages $paymentStages)
    {
        //
    }
}
