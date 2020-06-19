<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class Invoice extends BaseModel 
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serie', 'subtotal', 'taxes', 'discount', 'total', 'type_id', 'stage', 'user_id'
    ];

    /**
     * The attributes that are cast.
     */
    protected $casts = [
        // 'subtotal' => 'decimal:2',
        //  'discount' => 'decimal:2',
    ];

    //Custom functions
    // public function getStockAttribute(){
    //     return $this->attributes['stock'];
    // }

    public function setStageAnulled()
    {
        $this->stage = 'A';
    }

    public function setStagePaid()
    {
        $this->stage = 'P';
    }

    public function getType(){
        return $this->type_id;
    }

    public function getTypeForSell(){
        return 1;
    }

    public function getTypeForPurchase(){
        return 2;
    }
 
}
