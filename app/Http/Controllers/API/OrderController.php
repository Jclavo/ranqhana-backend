<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\OrderStage;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;

use Illuminate\Support\Facades\Validator;

//Utils
use App\Utils\PaginationUtils;
use App\Utils\CustomCarbon;

//Services
use App\Services\LanguageService;

use Carbon\Carbon;

class OrderController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();

        //permission middleware
        $this->middleware('permission_in_role:orders_list/read');
        $this->middleware('permission_in_role:orders_list/pagination', ['only' => ['pagination']]);
        // $this->middleware('permission_in_role:orders_list/delete', ['only' => ['destroy', 'anull']]);

        // $this->middleware('permission_in_role:order/create', ['only' => ['store' ]]);
        $this->middleware('permission_in_role:sell_order/update|permission_in_role:purchase_order/update', ['only' => ['update', 'updateStage','updateDeliveryDate']]);
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
            'serie' => 'max:12',
            'pageSize' => 'numeric|gt:0',
        ]);

        $validator->sometimes('stage_id', 'required|exists:order_stages,id', function ($input) {
            return $input->stage_id > 0;
        });

        $validator->sometimes('type_id', 'required|exists:invoice_types,id', function ($input) {
            return $input->type_id > 0;
        });

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
        $stage_id      = $request->stage_id;
        $type_id       = $request->type_id;
        $serie         = $request->serie;
        

        $query = Order::query();

        $query->select('orders.*');

        $query->whereHas('invoice', function ($query) use($type_id) {
            $query->when((!empty($type_id)) , function ($subquery) use($type_id) {
                return $subquery->where('invoices.type_id', '=', $type_id);
            });
        });

        $query->with(['invoice.stage','invoice.type']);

        $query->when((!empty($stage_id)) , function ($q) use($stage_id) {
            return $q->where('orders.stage_id', '=', $stage_id);
        });

        $query->when((!empty($serie)) , function ($q) use($serie) {
            return $q->where('orders.serie', 'like', '%'. $serie .'%');
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
    public function updateStage(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:orders,id',
            'stage_id' => 'required|exists:order_stages,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        if($request->stage_id == OrderStage::getForCancel()){
            return $this->sendError($this->languageService->getSystemMessage('order.change-stage-canceled'));
        }

        $order = Order::findOrFail($request->id);

        if($order->stage_id == OrderStage::getForCancel()){
            return $this->sendError($this->languageService->getSystemMessage('order.already-canceled'));
        }

        $order->stage_id = $request->stage_id;
        $order->save();
                
        return $this->sendResponse($order->toArray(), $this->languageService->getSystemMessage('stageChange'));
    }

    /**
     * Change date
     */
    public function updateDeliveryDate(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:orders,id',
            'delivery_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $order = Order::findOrFail($request->id);

        //validate depends on Stage
        switch ($order->stage_id) {
            case OrderStage::getForCancel():
                return $this->sendError($this->languageService->getSystemMessage('order.already-canceled'));
                break;
            case OrderStage::getForDelivered():
                return $this->sendError($this->languageService->getSystemMessage('order.already-delivered'));
                break;
            default:
                # code...
                break;
        }


        $order->delivery_date = Carbon::parse($request->delivery_date);
        $order->save();
                
        return $this->sendResponse($order->toArray(), $this->languageService->getSystemMessage('crud.update-date'));
    }

}
