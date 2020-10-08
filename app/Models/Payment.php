<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\PaymentStage;

use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{ 
    use SoftDeletes;
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
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the Payment Stage associated with the Payment.
     */
    public function paymentStage()
    {
        return $this->belongsTo(PaymentStage::class);
    }


}

