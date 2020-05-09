<?php

namespace App\Http\Controllers\API;

use App\Invoice;
use App\InvoiceDetail;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Actions\Invoice\InvoiceAnull;
use App\Actions\BelongsToStore;
use Illuminate\Validation\Rule; 

use App\Http\Controllers\API\ItemController;
use Carbon\Carbon;
use App\Utils\CustomCarbon;
use App\Utils\UserUtils;

class InvoiceController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $validator = Validator::make($request->all(), [
            // 'required|between:0,99.99'
            // 'number' => 'required|numeric|digits_between:1,10' // it does not need numeric
            'subtotal' => 'required|numeric|gt:0',
            'taxes' => 'numeric|gte:0|lt:subtotal',
            'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $sellInvoice = new Invoice();
        
        $sellInvoice->serie    = $request->serie;
        $sellInvoice->subtotal = $request->subtotal;
        $sellInvoice->taxes    = $request->taxes;
        $sellInvoice->discount = $request->discount;
        $sellInvoice->total    = $sellInvoice->subtotal + $sellInvoice->taxes - $sellInvoice->discount;
        $sellInvoice->type_id  = $request->type_id;
        $sellInvoice->user_id  = Auth::user()->id;
        $sellInvoice->store_id  = Auth::user()->store_id;

        //Update stage
        $sellInvoice->setStagePaid();
        
        $sellInvoice->save();

        return $this->sendResponse($sellInvoice->toArray(), 'Sell invoice created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::select('invoices.*','stores.name as store', 'invoice_types.description as type')
                   ->join('stores', 'invoices.store_id', '=', 'stores.id')
                   ->join('invoice_types', 'invoices.type_id', '=', 'invoice_types.id')
                   ->where('invoices.id','=', $id)
                   ->where('invoices.store_id','=', Auth::user()->store_id)
                   ->get();

        if($invoice->isEmpty()){
            return $this->sendError('Invoice not found.');
        }else{
            return $this->sendResponse($invoice, 'Invoice retrieved successfully.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        $this->businessValidations([
            new BelongsToStore(Auth::user(), [$invoice]),
        ]);

        $invoice->serie    = $request->serie;
        // $sellInvoice->client   = $request->client;

        $invoice->save();

        return $this->sendResponse($invoice->toArray(), 'Invoice updated successfully.');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
    }

    /**
     * 
     */

    public function pagination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'subtotal' => 'required|numeric|gt:0',
            // 'taxes' => 'numeric|gte:0|lt:subtotal',
            // 'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $sortArray = array("asc", "ASC", "desc", "DESC");

        // SearchOptions values
        $per_page      = $request->per_page;
        $sortColumn    = $request->sortColumn;
        $sortDirection = $request->sortDirection;
        $searchValue   = $request->searchValue;
        $fromDate      = $request->fromDate;
        $toDate        = $request->toDate;
        $type_id       = $request->type_id;

        // Initialize values if they are empty.
        if (empty($per_page)) {
            $per_page = 20;
        }

        if (!in_array($sortDirection, $sortArray)) {
            $sortDirection = "DESC";
            $sortColumn = "created_at";
        }

        if (empty($sortColumn)) {
            $sortColumn = "created_at";
        }
        
        if (empty($type)) {
            $type = "S";
        }

        $query = Invoice::query();

        $query->select('invoices.*');

        $query->where('type_id', '=', $type_id)
              ->where('store_id', '=', Auth::user()->store_id);

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('serie', 'like', '%'. $searchValue .'%')
                     ->orWhere('total', 'like', '%'. $searchValue .'%');
        });

        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome = UserUtils::getTimezone(Auth::user());
            //change date format
            $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
        });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($per_page);
 
        return $this->sendResponse($results->items(), 'Invoices retrieved successfully.', $results->total() );

    }
     
    /**
     * 
     */

    public function anull(int $id)
    {
        $invoice = Invoice::findOrFail($id);

        $this->businessValidations([
            new BelongsToStore(Auth::user(), [$invoice]),
        ]);

        $this->businessActions([ new InvoiceAnull($invoice)]);

        return $this->sendResponse($invoice->toArray(), 'Invoice Anulled successfully.');
    }


    /**
     * 
     */
    public function graphicReport(Request $request){

        $validator = Validator::make($request->all(), [
            'fromDate' => 'date',
            'toDate' => 'date|after_or_equal:fromDate',
            'type_id' => 'required|exists:invoice_types,id',
            'searchBy' => [Rule::in(['Y', 'M', 'D', 'H'])]
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $type_id = $request->type_id;
        $searchBy = $request->searchBy;

        $query = Invoice::query();

        $query->select('invoices.total','invoices.created_at')
              ->where('type_id', '=', $type_id)
              ->where('store_id', '=', Auth::user()->store_id);


        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome = UserUtils::getTimezone(Auth::user());
            //change date format
            $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
        });

        $rawQuery = $query->get()
                    ->groupBy(function($date) use($searchBy){

                        switch ($searchBy) {
                            case 'Y':
                                return Carbon::parse($date->created_at)->format('Y'); // grouping by year
                                break;
                            case 'M':
                                return Carbon::parse($date->created_at)->format('M/Y'); // grouping by month 
                                break;
                            case 'D':
                                return Carbon::parse($date->created_at)->format('M/D-d'); // grouping by day
                                break;
                            case 'H':
                                return Carbon::parse($date->created_at)->format('D g A'); // grouping by day
                                // format('g:i A');
                                break;
                            default:
                                # code...
                                break;
                        }
                    })
                    ->map(function ($row) {
                        return $row->sum('total');
                    });
        
        // Logic to convert the group by in an array with X/Y
        $query_keys = array_keys($rawQuery->toArray());
        $results = array();
        foreach($query_keys as $key) {
            array_push($results,['X' => $key, 'Y' => $rawQuery[$key]]);
        }
                    
        return $this->sendResponse($results, 'Invoices retrieved successfully.' );

    }


    
}
