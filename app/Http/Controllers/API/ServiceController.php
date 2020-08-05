<?php

namespace App\Http\Controllers\API;

use App\Models\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

//Utils
use App\Utils\PaginationUtils;


class ServiceController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service = Service::all();
            
        return $this->sendResponse($service->toArray(), 'Services retrieved successfully.');
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
            'name' => 'required',
            'price' => 'required|numeric|between:0.00,99999.99',
            'description' => 'nullable|max:200',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $service = new Service();
        
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = Auth::user()->getLocalUserID();
        $service->save();

        //Add price
        $service->prices()->create(['price' => $request->price]);

        //add stock types
        $service->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($service->toArray(), 'Service created successfully.');  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::with('stock_types')->findOrFail($id); //get stock_types
                
        return $this->sendResponse($service->toArray(), 'Service retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric|between:0.00,99999.99',
            'description' => 'nullable|max:200',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $service = Service::findOrFail($id);
        
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = Auth::user()->getLocalUserID();
        $service->save();

        //Add price
        $service->prices()->create(['price' => $request->price]);

        //add stock types
        $service->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($service->toArray(), 'Service updated successfully.');  
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $service = Service::findOrFail($id);
        
        $service->delete();

        return $this->sendResponse($service->toArray(), 'Service deleted successfully.');
    }


    /**
     * Pagination
     */
    public function pagination(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pageSize' => 'numeric|gt:0',
            'stock_type' => 'nullable|exists:stock_types,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'items');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;
        $stock_type_id    = $request->stock_type_id;
            

        $query = Service::query();
        $query->select('services.*');

        $query->where(function($q) use ($searchValue){
            $q->where('services.name', 'like', '%'. $searchValue .'%')
              ->orWhere('services.description', 'like', '%'. $searchValue .'%')
              ->orWhere('services.price', 'like', $searchValue .'%');
        });

        //Filter by stock types
        $query->when((!empty($stock_type_id) ), function ($q) use($stock_type_id) {
            return $q->join('stock_typeables', function ($join) use($stock_type_id){
                        $join->on('stock_typeables.stock_typeable_id', '=', 'services.id')
                             ->where('stock_typeables.stock_type_id', $stock_type_id)
                             ->where('stock_typeables.stock_typeable_type', Service::class);
                    });
        });
        
        
        //get stock_types
        $query->with('stock_types');

        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);

            
        return $this->sendResponse($results->items(), 'Services retrieved successfully.', $results->total() );

    }
}
