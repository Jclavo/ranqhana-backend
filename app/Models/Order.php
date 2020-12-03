<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\OrderStage;
use App\Models\Invoice;

use App\Scopes\Belongs2CompanyByInvoice;
// use App\Casts\CorrelativeCode;
// use App\Utils\MoreUtils;

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
        'invoice_id', 'stage_id', 'delivery_date', 'serie'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new Belongs2CompanyByInvoice);
    }


    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'code' => CorrelativeCode::class,
    // ];

    //Relationships

    /**
     * Get the "Stage" associated with the "Order".
     */
    public function stage()
    {
        return $this->belongsTo(OrderStage::class,'stage_id','code');
    }

    /**
     * Get the invoice that owns the order.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    //Setters
    public function setStageCancel()  
    {
        $this->stage_id = OrderStage::getForCancel();
    }
}
