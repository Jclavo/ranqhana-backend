<?php

namespace App\Http\Controllers\API;

use App\Models\Price;
use App\Models\Item;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\InvoiceStage;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;

//Services
use App\Services\LanguageService;

class PriceController extends ResponseController
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
     * @param  \App\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function edit(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Price $price)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Price  $price
     * @return \Illuminate\Http\Response
     */
    public function destroy(Price $price)
    {
        //
    }

    public function sellPriceByItem(int $item_id){

        //check item exists
        Item::findOrFail($item_id); 

        $prices = Price::select('prices.price', 'prices.created_at' )
            ->join('items', function ($join){
                $join->on('prices.priceable_id', '=', 'items.id')
                ->where('prices.priceable_type', Item::class);
            })
            ->where('items.id', '=', $item_id)
            ->distinct()
            ->orderBy('prices.created_at', 'DESC')
            ->orderBy('prices.price')
            ->limit(10)
            ->get();

        return $this->sendResponse($prices,  $this->languageService->getSystemMessage('crud.pagination'));

    }

    public function purchasePriceByItem(int $item_id){

        //check item exists
        Item::findOrFail($item_id); 

        $prices = InvoiceDetail::select('invoice_details.price', 'invoice_details.created_at' )
            ->join('invoices', function ($join){
                $join->on('invoice_details.invoice_id', '=', 'invoices.id')
                ->where('invoices.type_id', InvoiceType::getForPurchase())
                ->whereIn('invoices.stage_id', [InvoiceStage::getForPaid(), InvoiceStage::getForByInstallment()]);
            })
            ->join('items', function ($join){
                $join->on('invoice_details.item_id', '=', 'items.id');
            })
            ->where('items.id', '=', $item_id)
            ->distinct()
            ->orderBy('invoice_details.created_at', 'DESC')
            ->orderBy('invoice_details.price')
            ->limit(10)
            ->get();

//         select distinct id.price, id.created_at
// from invoice_details id
// inner join invoices i2 
// on id.invoice_id = i2.id

// and i2.type_id = 2
// and i2.stage_id in (1,4)

// inner join items i 
// on id.item_id = i. id
// where i.id = 1
// order by id.created_at desc, id.price;

        return $this->sendResponse($prices,  $this->languageService->getSystemMessage('crud.pagination'));

    }
}
