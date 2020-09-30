<?php

namespace App\Http\Controllers\API;

use App\Models\StockType;
use App\Http\Controllers\ResponseController;

//Services
use App\Services\LanguageService;

class StockTypeController extends ResponseController
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
        $stockTypes = StockType::all();
      
        return $this->sendResponse($stockTypes, $this->languageService->getSystemMessage('crud.create'));
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
     * @param  \App\StockTypes  $stockTypes
     * @return \Illuminate\Http\Response
     */
    public function show(StockTypes $stockTypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StockTypes  $stockTypes
     * @return \Illuminate\Http\Response
     */
    public function edit(StockTypes $stockTypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StockTypes  $stockTypes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockTypes $stockTypes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StockTypes  $stockTypes
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockTypes $stockTypes)
    {
        //
    }
}
