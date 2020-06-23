<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Invoice extends BaseModel 
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serie', 'subtotal', 'taxes', 'discount', 'total', 'type_id', 'stage_id', 'user_id'
    ];

    /**
     * The attributes that are cast.
     */
    protected $casts = [
        // 'subtotal' => 'decimal:2',
        //  'discount' => 'decimal:2',
    ];


    //Relationships
    public function type()
    {
        return $this->belongsTo('App\Models\InvoiceTypes');
    }

    public function stage()
    {
        return $this->belongsTo('App\Models\InvoiceStages');
    }



    //Custom functions
    public function calculateTotal(){
        return (($this->subtotal + $this->taxes) - $this->discount);
    }

    //Setter and Getters
    public function setStagePaid() // stage = 'P';
    {
        $this->stage_id = 1;
    }

    public function setStageAnulled() // stage = 'A';
    {
        $this->stage_id = 2;
    }

    public function getStagePaid()
    {
        return 1;
    }

    public function getStageAnulled()
    {
        return 2;
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
