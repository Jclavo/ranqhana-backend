<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    
    /**
     * Get all of the items that are assigned this stock type.
     */
    public function items()
    {
        return $this->morphedByMany('App\Models\Item', 'stock_typeable');
    }
}
