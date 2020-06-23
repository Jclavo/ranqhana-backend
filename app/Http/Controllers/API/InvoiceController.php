<?php

namespace App\Http\Controllers\API;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\User;

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

//Utils
use App\Utils\PaginationUtils;
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
            // "/^-?[0-9]+(?:\.[0-9]{1,2})?$/"
            'subtotal' => 'required|gt:0|regex:/^[0-9]{1,10}+(?:\.[0-9]{1,2})?$/',
            'taxes' => 'numeric|gte:0|lt:subtotal',
            'discount' => 'numeric|gte:0|lt:subtotal',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $invoice = new Invoice();
        
        $invoice->serie    = $request->serie;
        $invoice->subtotal = $request->subtotal;
        $invoice->taxes    = $request->taxes;
        $invoice->discount = $request->discount;
        $invoice->total    = $invoice->calculateTotal();

        //Validate that total is not negative
        if($invoice->total < 0){
            return $this->sendError($invoice->toArray(), 'Invoice total is negative.');       
        }

        //Set FKs
        $invoice->type_id  = $request->type_id;
        $invoice->user_id = Auth::user()->getLocalUserID();
        $invoice->setStagePaid();
        
        $invoice->save();

        return $this->sendResponse($invoice->toArray(), 'Sell invoice created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::with(['type','stage'])->findOrFail($id);
                
        return $this->sendResponse($invoice->toArray(), 'Invoice retrieved successfully.');
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

        $invoice->serie    = $request->serie;

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
            'type_id' => 'required|exists:invoice_types,id',
            'pageSize' => 'numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

       // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'units');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;

        $fromDate      = $request->fromDate;
        $toDate        = $request->toDate;
        $type_id       = $request->type_id;
        
        // if (empty($type)) {
        //     $type = "S";
        // }

        $query = Invoice::query();

        $query->select('invoices.*');

        $query->where('type_id', '=', $type_id);

        $query->when((!empty($searchValue)), function ($q) use($searchValue) {
            return $q->where('serie', 'like', '%'. $searchValue .'%')
                     ->orWhere('total', 'like', '%'. $searchValue .'%');
        });

        // $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

        //     $timezome = UserUtils::getTimezone(Auth::user());
        //     //change date format
        //     $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
        //     $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

        //     return $q->whereBetween('created_at',[ $fromDate." 00:00:00", $toDate." 23:59:59"]);
        // });

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);       
 
        return $this->sendResponse($results->items(), 'Invoices retrieved successfully.', $results->total() );

    }
     
    /**
     * 
     */

    public function anull(int $id)
    {
        $invoice = Invoice::findOrFail($id);

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


        // $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

        //     $timezome = UserUtils::getTimezone(Auth::user());
        //     //change date format
        //     $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
        //     $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

        //     return $q->whereBetween('created_at',[$fromDate, $toDate]);
        // });

       
        $rawQuery = $query->get()
                    ->groupBy(function($date) use($searchBy){
                        //Only in hours case 
                        $timezome = UserUtils::getTimezone(Auth::user());
                        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date->created_at, 'UTC');
                        $date->setTimezone($timezome); 

                        switch ($searchBy) {
                            case 'Y':
                                return Carbon::parse($date)->format('Y'); // grouping by year
                                break;
                            case 'M':
                                return Carbon::parse($date)->format('M/Y'); // grouping by month 
                                break;
                            case 'D':
                                return Carbon::parse($date)->format('M/D-d'); // grouping by day
                                break;
                            case 'H':
                                return Carbon::parse($date)->format('D g A', $timezome); // grouping by day
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
