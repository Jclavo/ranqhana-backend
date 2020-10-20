<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

use Illuminate\Support\Facades\Validator;

//Utils
use App\Utils\PaginationUtils;
use App\Utils\CustomCarbon;

//Services
use App\Services\LanguageService;

class OrderController extends ResponseController
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
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
                
        return $this->sendResponse($order->toArray(), $this->languageService->getSystemMessage('crud.read'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    /**
     * Pagination
     */

    public function pagination(Request $request){

        $validator = Validator::make($request->all(), [
            'stage_id' => 'exists:order_stages,id',
            'pageSize' => 'numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'orders');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;

        $fromDate      = $request->fromDate;
        $toDate        = $request->toDate;
        $stage_id       = $request->stage_id;
        

        $query = Order::query();

        $query->select('orders.*');
        $query->whereHas('invoice');
        $query->with('invoice.stage');
        // $query->where('type_id', '=', $type_id);

        // $query->when((!empty($searchValue)), function ($q) use($searchValue) {
        //     return $q->where('serie', 'like', '%'. $searchValue .'%')
        //              ->orWhere('total', 'like', '%'. $searchValue .'%');
        // });
        // $query = $query::whereHas('invoice', function ($query) use($invoice_id) {
        //     $query->where('invoices.id', '=', $invoice_id);
        // });

        $query->when((!empty($stage_id)) , function ($q) use($stage_id) {
            return $q->where('orders.stage_id', '=', $stage_id);
        });


        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $fromDate = CustomCarbon::countryTZtoUTC($fromDate,'00:00:00');
            $toDate = CustomCarbon::countryTZtoUTC($toDate,'23:59:59');

            return $q->whereBetween('orders.created_at',[ $fromDate, $toDate]);
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);       
 
        return $this->sendResponse($results->items(), $this->languageService->getSystemMessage('crud.pagination'), $results->total() );

    }

    /**
     * Change Status
     */
    public function changeStatus(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:orders,id',
            'stage_id' => 'required|exists:order_stages,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $order = Order::findOrFail($request->id);

        $order->stage_id = $request->stage_id;
        $order->save();
                
        return $this->sendResponse($order->toArray(), $this->languageService->getSystemMessage('statusChange'));
    }
}
