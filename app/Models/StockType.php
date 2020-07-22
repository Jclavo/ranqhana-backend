<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockType extends Model
{
    
     /**
     * The items that belong to the stock type.
     */
    public function items()
    {
        return $this->belongsToMany('App\Models\Item');
                    // ->using('App\Models\CompanyProject')
                    // ->withTimestamps()
                    // ->withPivot('id')
                    // ->orderBy('name');
    }
}
