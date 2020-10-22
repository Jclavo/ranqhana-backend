<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrderStage;
use App\Models\Invoice;

// use App\Casts\CorrelativeCode;

use App\Utils\MoreUtils;


class Order extends BaseModel
{
    /**
     * The relations to be loaded
     */

    protected $with = ['stage'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 
    protected $fillable = [
        'invoice_id', 'stage_id', 'delivery_date', 'code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'code' => CorrelativeCode::class,
    // ];

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = MoreUtils::generateCorrelativeCode($this);
    }

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
