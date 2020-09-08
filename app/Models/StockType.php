<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

//Models
use App\Models\Service;
use App\Models\Item;

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
        return $this->morphedByMany(Item::class, 'stock_typeable');
    }

    /**
     * Get all of the items that are assigned this stock type.
     */
    public function services()
    {
        return $this->morphedByMany(Service::class, 'stock_typeable');
    }

    /**
     * Get all of the Stock Types' translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }
}
