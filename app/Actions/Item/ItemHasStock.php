<?php

namespace App\Actions\Item;

use App\Models\ItemType;

class ItemHasStock
{
    protected $item;
    protected $quantity;

    public function __construct($item, $quantity)
    {
        $this->item = $item;
        $this->quantity = $quantity;
    }    
    
    public function passes()
    {
        if($this->item->type_id == ItemType::getTypeService()) return true;

        if(!$this->item->stocked) return true;

        if(!$this->item->hasStock()) return false;

        if($this->quantity > $this->item->stock) return false;
        
        return true;
    }    
    
    public function message()
    {
        return 'There is not stock for item ' . $this->item->id;
    }
}