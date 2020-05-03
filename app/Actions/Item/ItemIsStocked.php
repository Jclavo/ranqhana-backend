<?php

namespace App\Actions\Item;

class ItemIsStocked
{
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function passes()
    {
        if(!$this->item->isStocked()) return false;

        return true;
    }

    public function message()
    {
        return 'Item ' . $this->item->id . ' does not have stock.';
    }
}
