<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\InvoiceStage;
use App\Models\InvoiceType;
use App\Models\Order;

use App\Scopes\Belongs2CompanyScope;
use App\Utils\MoreUtils;

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

     public function setSerieAttribute($value)
    {
        $this->attributes['serie'] = MoreUtils::generateCorrelativeSerie($this);
    }


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


    //Relationships

    /**
     * Get the "Type" associated with the "Invoice".
     */
    public function type()
    {
        return $this->belongsTo(InvoiceType::class,'type_id','code');
    }

    /**
     * Get the "Stage" associated with the "Invoice".
     */
    public function stage()
    {
        return $this->belongsTo(InvoiceStage::class,'stage_id','code');
    }

    /**
     * Get the "payment" associated with the "Invoice".
     */
    public function payment()
    {
        return $this->belongsTo(PaymentType::class,'payment_type_id','code');
    }

    /**
     * Get the "Invoice details" for the "Invoice"
    */
    public function details()
    {
        return $this->hasMany('App\Models\InvoiceDetail');
    }

    /**
     * Get the "Payments" for the "Invoice"
    */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }


    //Custom functions
    public function calculateTotal(){
        // return (($this->subtotal + $this->taxes) - $this->discount);
        return ($this->subtotal - $this->discount);
    }
    
    //Getter 
    public function getType(){
        return $this->type_id;
    }

    //Setter and Getters
    public function setStagePaid()
    {
        $this->stage_id = InvoiceStage::getForPaid();
    }

    public function setStageAnulled() 
    {
        $this->stage_id = InvoiceStage::getForAnnulled();
    }

    public function setStageDraft()
    {
        $this->stage_id = InvoiceStage::getForDraft();
    }

    public function setStageByInstallment() 
    {
        $this->stage_id = InvoiceStage::getForByInstallment();
    }

    public function setStageStockUpdated()
    {
        $this->stage_id = InvoiceStage::getForStockUpdated();
    }
}
