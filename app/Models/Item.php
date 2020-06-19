<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends BaseModel
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
        $this->stocked ? $this->stock = $this->stock + $quantity : null;
        
    }

    public function decreaseStock($quantity){
        $this->stocked ? $this->stock = $this->stock - $quantity: null;
    }

    public function isStocked(){
        return $this->stocked;
    }
}
 