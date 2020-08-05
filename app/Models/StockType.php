<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//Models
use App\Models\Service;
use App\Models\Item;

class StockType extends Model
{
    
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
}
