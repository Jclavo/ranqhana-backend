<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\PaymentStage;

use App\Scopes\Belongs2CompanyByInvoice;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{ 
    use SoftDeletes;

    protected $with = ['method','stage'];

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
     * Relationships
     */

    /**
     * Get the Invoice that owns the Payment.
    */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the Payment Method associated with the Payment.
     */
    public function method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the Payment Stage associated with the Payment.
     */
    public function stage()
    {
        return $this->belongsTo(PaymentStage::class);
    }

    /**
     * Setter
     */

    public function setStageAnulled() 
    {
        $this->stage_id = PaymentStage::getForAnnulled();
    }


}

