<?php

namespace App\Http\Controllers\API;

use App\Models\Item;
use App\Models\ItemType;

use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

//Utils
use App\Utils\PaginationUtils;

//Services
use App\Services\LanguageService;

class ItemController extends ResponseController
{
    private $languageService = null;
    
    function __construct()
    {
        //initialize language service
        $this->languageService = new LanguageService();
        
        //General
        $this->middleware('permission_in_role:services_list/pagination|products_list/pagination' , ['only' => ['pagination']]);
        $this->middleware('permission_in_role:services_list/delete|products_list/delete', ['only' => ['destroy']]);
        
        //Products
        $this->middleware('permission_in_role:service/create', ['only' => ['storeService']]);
        $this->middleware('permission_in_role:service/update', ['only' => ['updateService']]);
        
        //Services
        $this->middleware('permission_in_role:product/create', ['only' => ['storeProduct']]);
        $this->middleware('permission_in_role:product/update', ['only' => ['updateProduct']]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::with('stock_types')->findOrFail($id); //get stock_types
                
        return $this->sendResponse($item->toArray(), $this->languageService->getSystemMessage('crud.read'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $item = Item::findOrFail($id);
        
        $item->delete();

        return $this->sendResponse($item->toArray(), $this->languageService->getSystemMessage('crud.delete'));
    }


    /**
     * Pagination displaying list of resources
     */
    public function pagination(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'pageSize' => 'numeric|gt:0'
        ]);

        $validator->sometimes('stock_type_id', 'required|exists:stock_types,id', function ($input) {
            return $input->stock_type_id > 0;
        });

        $validator->sometimes('type_id', 'required|exists:item_types,id', function ($input) {
            return $input->type_id > 0;
        });

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        // SearchOptions values
        $pageSize      = PaginationUtils::getPageSize($request->pageSize);
        $sortColumn    = PaginationUtils::getSortColumn($request->sortColumn,'items');
        $sortDirection = PaginationUtils::getSortDirection($request->sortDirection);
        $searchValue   = $request->searchValue;
        $stock_type_id = $request->stock_type_id;
        $type_id       = $request->type_id;
            
        $query = Item::query();
        $query->select('items.*', 'units.abbreviation as unit', 'units.fractioned')
              ->leftJoin('units', function ($join){
                    $join->on('units.code', '=', 'items.unit_id');
                });

        $query->where(function($q) use ($searchValue){
            $q->where('items.name', 'like', '%'. $searchValue .'%')
              ->orWhere('items.description', 'like', '%'. $searchValue .'%')
              ->orWhere('items.price', 'like', $searchValue .'%')
              ->orWhere('items.stock', 'like', $searchValue .'%');
        });

        //Filter by types
        $query->when(( $type_id > 0 ), function ($q) use($type_id) {
            $q->where('items.type_id', $type_id );
        });

        //Filter by stock types
        $query->when(( $stock_type_id > 0 ), function ($q) use($stock_type_id) {
            return $q->join('stock_typeables', function ($join) use($stock_type_id){
                        $join->on('stock_typeables.stock_typeable_id', '=', 'items.id')
                                ->where('stock_typeables.stock_type_id', $stock_type_id)
                                ->where('stock_typeables.stock_typeable_type', Item::class);
                    });
        });
                
        //get stock_types
        $query->with(['stock_types','type']);
        
        $results = $query->orderBy($sortColumn, $sortDirection)
                         ->paginate($pageSize);

        /**
         * custom logic to get translation when the relationship has a Pivot table
         */

        $items = $results->items();

        foreach ($items as $item) {

            foreach ($item['stock_types'] as $stock_type) {

                foreach ($stock_type->translations as $translation) {
                        $stock_type[$translation->key] = $translation->value;
                }
                unset($stock_type->translations);              
            }
        }


            
        return $this->sendResponse($items, $this->languageService->getSystemMessage('crud.pagination'), $results->total() );

    }

    /**
     * PRODUCTS Methods
     */

    /**
     * Store a newly created PRODUCT resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric|between:0.00,99999.99',
            'unit_id' => 'required|exists:units,id',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $item = new Item();
        
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $request->stocked ? $item->stocked = $request->stocked : $item->stocked = false; 
        $item->unit_id = $request->unit_id;
        $item->user_id = Auth::user()->id;
        $item->type_id = ItemType::getForProduct();
        $item->save();

        //Add price
        $item->prices()->create(['price' => $request->price]);

        //add stock types
        $item->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($item->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    /**
     * Update The specified PRODUCT resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function updateProduct(int $id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric|between:0.00,99999.99',
            'unit_id' => 'required|exists:units,id',
            'stock_types' => 'nullable|array',
            'stock_types.*' => 'nullable|exists:stock_types,id'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
       
        $item = Item::where('type_id', ItemType::getForProduct())->findOrFail($id); 
        
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->unit_id = $request->unit_id;

        $request->stocked ? $item->stocked = $request->stocked : $item->stocked = false; 
        

        if(!$item->stocked && $item->stock > 0){
            return $this->sendError($this->languageService->getSystemMessage('item.has-stock'));
        }
     
        $item->save();

        //Add price
        $item->prices()->updateOrCreate(['price' => $request->price]);

        //add stock types
        $item->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($item->toArray(), $this->languageService->getSystemMessage('crud.update'));
    }


    /**
    * SERVICES Methods
    */

    /**
     * Store a newly created SERVICE resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeService(Request $request)
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

        $service = new Item();
        
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->stocked = true;
        $service->user_id = Auth::user()->id;
        $service->type_id = ItemType::getForService();
        $service->save();

        //Add price
        $service->prices()->create(['price' => $request->price]);

        //add stock types
        $service->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($service->toArray(), $this->languageService->getSystemMessage('crud.create'));  
    }

    /**
     * Update the specified SERVICE resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function updateService(int $id, Request $request)
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

        $service = Item::where('type_id', ItemType::getForService())->findOrFail($id); 
        
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->user_id = Auth::user()->id;
        $service->save();

        //Add price
        $service->prices()->create(['price' => $request->price]);

        //add stock types
        $service->stock_types()->sync($request->stock_types ?? []);

        return $this->sendResponse($service->toArray(), $this->languageService->getSystemMessage('crud.update'));  
        
    }



}
