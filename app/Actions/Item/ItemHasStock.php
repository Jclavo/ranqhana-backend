<?php

namespace App\Actions\Item;

use App\Models\ItemType;

//Services
use App\Services\LanguageService;

class ItemHasStock
{
    protected $item;
    protected $quantity;
    protected $languageService;

    public function __construct($item, $quantity)
    {
        //initialize language service
        $this->languageService = new LanguageService();
    
        $this->item = $item;
        $this->quantity = $quantity;
    }    
    
    public function passes()
    {
        if($this->item->type_id == ItemType::getForService()) return true;

        if(!$this->item->stocked) return true;

        if(!$this->item->hasStock()) return false;

        if($this->quantity > $this->item->stock) return false;
        
        return true;
    }    
    
    public function message()
    {
        // return 'There is not stock for item ' . $this->item->id;
        return $this->languageService->getSystemMessage('item.has-no-stock') . ' : ' . $this->item->name;
    }
}