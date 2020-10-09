<?php

namespace App\Http\Controllers\API;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

//Services
use App\Services\LanguageService;

class PaymentTypeController extends ResponseController
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
        $paymentType = PaymentType::all();
      
        return $this->sendResponse($paymentType, $this->languageService->getSystemMessage('crud.read'));
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
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentType $paymentType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentType $paymentType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentType $paymentType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentType $paymentType)
    {
        //
    }
}
