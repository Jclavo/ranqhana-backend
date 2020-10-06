<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 

//Services
use App\Services\LanguageService;

class ImageController extends ResponseController
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
        $this->modelsAllowed = array('ITEM', 'USER');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'model_id' => 'required|integer',
            'model' => [
                'required',
                Rule::in($this->modelsAllowed)
            ]
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $newImage = array('name' => $request->name);
        switch ($request->model) {
            case 'ITEM':
                # code...
                $item = Item::findOrFail($request->model_id); 
                $item->images()->updateOrCreate($newImage);
                break;
            
            default:
                # code...
                break;
        }

        return $this->sendResponse([],$this->languageService->getSystemMessage('crud.create'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $image = Image::findOrFail($id);
        
        $image->delete();

        return $this->sendResponse($image->toArray(), $this->languageService->getSystemMessage('crud.delete'));
    }
}
