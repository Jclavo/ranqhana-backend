<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'stock', 'store_id', 'unit_id'
    ];


    //Custom functions
    public function getStockAttribute(){
        return $this->attributes['stock'];
    }

    public function hasStock()
    {
        if ($this->stock > 1) return true;
        return false;
    }

    public function increaseStock($quantity){
        $this->stock = $this->stock + $quantity;
    }

    public function decreaseStock($quantity){
        $this->stock = $this->stock - $quantity;
    }
}
 