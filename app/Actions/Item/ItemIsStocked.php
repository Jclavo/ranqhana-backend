<?php

namespace App\Actions\Item;

//Services
use App\Services\LanguageService;

class ItemIsStocked
{
    protected $item;
    protected $languageService;

    public function __construct($item)
    {
        //initialize language service
        $this->languageService = new LanguageService();

        $this->item = $item;
    }

    public function passes()
    {
        if(!$this->item->isStocked()) return false;

        return true;
    }

    public function message()
    {
        return $this->languageService->getSystemMessage('item.has-no-stock') . ' : ' . $this->item->id;
    }
}
