<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'price','item_id'
    ];


    /**
     * Get the owning priceable model.
     */
    public function priceable()
    {
        return $this->morphTo();
    }
}
