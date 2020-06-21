<?php

namespace App\Models;

// use App\Models\BaseModel;
// use Illuminate\Database\Eloquent\Model;
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
            $builder->select('items.*')
                     ->join('ranqhana_users','user_id', '=', 'ranqhana_users.id')
                    ->where('ranqhana_users.company_project_id', '=', Auth::user()->company_project_id);

        });
    }

    /**
     * 
     */
    


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
 