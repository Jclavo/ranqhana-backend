<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use App\Scopes\Belongs2CompanyByInvoice;

class InvoiceDetail extends BaseModel
{
    protected $fillable = [
        'quantity', 'price', 'total', 'item_id', 'invoice_id'
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

    //Relationships
    
    /**
     * Get the "Invoice" associated with the "Invoice Detail".
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    /**
     * Get the "Item" associated with the "Invoice Detail".
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }


    //Custom functions
    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->price;
    }


}
