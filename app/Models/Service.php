<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

//Models
use App\Models\Price;
use App\Models\StockType;

class Service extends BaseModel
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'price', 'user_id'
    ];


    /**
    * RELATIONSHIPS
    */

    /**
    * Get the prices for the Item.
    */
    public function prices()
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    /**
    * The services that belong to the stock type.
    */
    public function stock_types()
    {
        return $this->morphToMany(StockType::class, 'stock_typeable');
    }
}
