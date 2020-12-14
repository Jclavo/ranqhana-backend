<?php

namespace App\Http\Controllers\API;

use App\Models\InvoiceStage;
use App\Http\Controllers\ResponseController;

//Services
use App\Services\LanguageService;

class InvoiceStageController extends ResponseController
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
        $invoiceStage = InvoiceStage::all();
      
        return $this->sendResponse($invoiceStage, $this->languageService->getSystemMessage('crud.create'));
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
     * @param  \App\InvoiceTypes  $InvoiceTypes
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceTypes $InvoiceTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvoiceTypes  $InvoiceTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceTypes $InvoiceTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InvoiceTypes  $InvoiceTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceTypes $InvoiceTypes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvoiceTypes  $InvoiceTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceTypes $InvoiceTypes)
    {
        //
    }
}
