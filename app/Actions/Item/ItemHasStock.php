<?php

namespace App\Actions\Item;

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
        if(!$this->item->hasStock()) return false;

        if($this->$quantity > $this->item->stock) return false;
        
        return true;
    }    
    
    public function message()
    {
        return 'There is not stock for product ' . $this->item->id;
    }
}