<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

use App\Models\ItemType;
use App\Models\Image;
use App\Models\StockType;
use App\Models\Unit;

use App\Scopes\Belongs2CompanyScope;

class Item extends BaseModel
{
    use SoftDeletes;

    protected $with = ['images'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'stock', 'store_id', 'unit_id'
    ];


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new Belongs2CompanyScope);
    }

    /**
     * Get the user that owns the user.
    */
    public function user()
    {
        return $this->belongsTo('App\Models\RanqhanaUser');
    }

    /**
     * Get the unit associated with the item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id','code');
    }

     /**
     * The items that belong to the stock type.
     */
    public function stock_types()
    {
        return $this->morphToMany(StockType::class, 'stock_typeable');
    }

    /**
     * Get the prices for the Item.
     */
    public function prices()
    {
        return $this->morphMany('App\Models\Price', 'priceable');
    }

    /**
     * Get the "Type" associated with the "Item".
     */
    public function type()
    {
        return $this->belongsTo(ItemType::class,'type_id','code');
    }

    /**
     * Get all of the item's images.
     */
    public function images()
    {
        return $this->morphMany('App\Models\Image', 'imageable');
    }
    

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
 