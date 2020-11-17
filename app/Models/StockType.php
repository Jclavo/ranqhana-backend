<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

//Models
use App\Models\Item;
use App\Models\Translation;

class StockType extends BaseModel
{
    /**
     * Relationships
     */
    
    /**
     * Get all of the items that are assigned this stock type.
     */
    public function items()
    {
        return $this->belongsToMany('App\Models\Item')
                    ->using('App\Models\ItemStockType');
    }

    /**
     * Get all of the Stock Types' translations.
     */
    public function translations()
    {
         return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                     ->where('locale',App::getLocale());
    }
}
