<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;

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


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('mine', function (Builder $builder) {
            $builder->whereIn('user_id', function($query){
                        $query->select('ranqhana_users.id')
                            ->from('ranqhana_users')
                            ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);
                    });

        });
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
        return $this->belongsTo('App\Models\Unit');
    }

     /**
     * The items that belong to the stock type.
     */
    public function stock_types()
    {
        return $this->morphToMany('App\Models\StockType', 'stock_typeable');
    }

    /**
     * Get the prices for the Item.
     */
    public function prices()
    {
        return $this->morphMany('App\Models\Price', 'priceable');
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
 