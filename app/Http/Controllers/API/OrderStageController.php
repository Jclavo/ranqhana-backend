<?php

namespace App\Http\Controllers\API;

use App\Models\OrderStage;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

//Services
use App\Services\LanguageService;

class OrderStageController extends ResponseController
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
        $orderStage = OrderStage::all();
      
        return $this->sendResponse($orderStage, $this->languageService->getSystemMessage('crud.read'));
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
     * @param  \App\Models\OrderStage  $orderStage
     * @return \Illuminate\Http\Response
     */
    public function show(OrderStage $orderStage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderStage  $orderStage
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderStage $orderStage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderStage  $orderStage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderStage $orderStage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderStage  $orderStage
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderStage $orderStage)
    {
        //
    }
}
