<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

//Models
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\User;

//Utils
use App\Utils\CustomCarbon;

//Services
use App\Services\LanguageService;

class ReportController extends ResponseController
{
    private $languageService = null;

    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();
    }

    /**
     * Invoice (Sales/Purchase) quantity per day/week/month/year
     */

    public function invoiceMoney(Request $request){

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

        $query->select('invoices.total','invoices.created_at');

        $query->whereHas('details', function ($query) use($type_id) {
            $query->where('type_id', '=', $type_id)
                  ->where('stage_id', '=', Invoice::getStagePaid());
        });
              
        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome = Auth::user()->getTimezone();
            //change date format
            $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[$fromDate, $toDate]);
        });

       
        $rawQuery = $query->get()
                    ->groupBy(function($date) use($searchBy){
                        //Only in hours case 
                        $timezome = Auth::user()->getTimezone();
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
                    
        return $this->sendResponse($results, $this->languageService->getSystemMessage('crud.pagination') );

    }


    /**
     * Popular products (Sales/Purchase)
    */
    public function popularProducts(Request $request){

        $validator = Validator::make($request->all(), [
            'fromDate' => 'date',
            'toDate' => 'date|after_or_equal:fromDate',
            'type_id' => 'required|exists:invoice_types,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $fromDate = $request->fromDate;
        $toDate = $request->toDate;
        $type_id = $request->type_id;

        $query = InvoiceDetail::query();

        $query->select('item_id', DB::raw("sum(invoice_details.quantity) as quantity"));

        $query->whereHas('invoice', function ($query) use($type_id){
            $query->where('invoices.type_id', '=', $type_id)
                  ->where('invoices.stage_id', '=', Invoice::getStagePaid());
        })
        ->groupBy('item_id')
        ->orderByDesc('quantity');

        
        $query->when((!empty($fromDate)) && (!empty($toDate)) , function ($q) use($fromDate,$toDate) {

            $timezome = Auth::user()->getTimezone();
            //change date format
            $fromDate = CustomCarbon::UTCtoCountryTZ($fromDate,'00:00:00', $timezome);
            $toDate = CustomCarbon::UTCtoCountryTZ($toDate,'23:59:59', $timezome);

            return $q->whereBetween('created_at',[$fromDate, $toDate]);
        });

        $query->with('item');
        $query = $query->get();


        return $this->sendResponse($query, $this->languageService->getSystemMessage('crud.pagination'));

    }
}
