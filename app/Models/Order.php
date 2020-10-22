<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrderStage;
use App\Models\Invoice;

class Order extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 
    protected $fillable = [
        'invoice_id', 'stage_id', 'delivery_date'
    ];

    /**
     * The relations to be loaded
     */

    protected $with = ['stage'];


    //Relationships

    /**
     * Get the "Stage" associated with the "Order".
     */
    public function stage()
    {
        return $this->belongsTo(OrderStage::class);
    }

    /**
     * Get the invoice that owns the order.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    //Setter and 
    public function setStageCancel()  
    {
        $this->stage_id = OrderStage::getForCancel();
    }
}
