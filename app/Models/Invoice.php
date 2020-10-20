<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Models\Payment;
use App\Models\InvoiceStages;
use App\Models\Order;

use App\Scopes\Belongs2CompanyScope;

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
        return $this->belongsTo('App\Models\InvoiceTypes');
    }

    /**
     * Get the "Stage" associated with the "Invoice".
     */
    public function stage()
    {
        return $this->belongsTo('App\Models\InvoiceStages');
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

    //Getter (statics)

    //PAYMENT TYPE

    static function getPaymentTypeDebit(){
        return 1;
    }

    static function getPaymentTypeCredit(){
        return 2;
    }

    
    //Getter 
    public function getType(){
        return $this->type_id;
    }


    //Setter and Getters
    public function setStagePaid() // stage = 'P';
    {
        $this->stage_id = InvoiceStages::getForPaid();
    }

    public function setStageAnulled() // stage = 'A';
    {
        $this->stage_id = InvoiceStages::getForAnulled();
    }

    public function setStageDraft() // stage = 'D';
    {
        $this->stage_id = InvoiceStages::getForDraft();
    }

    public function setStageByInstallment() // stage = 'I';
    {
        $this->stage_id = InvoiceStages::getForByInstallment();
    }

    public function setStageStockUpdated() // stage = 'U';
    {
        $this->stage_id = InvoiceStages::getForStockUpdated();
    }


 
}
