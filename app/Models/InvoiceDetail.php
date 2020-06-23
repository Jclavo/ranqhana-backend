<?php

namespace App\Models;

use App\Models\BaseModel;

class InvoiceDetail extends BaseModel
{
    protected $fillable = [
        'quantity', 'price', 'total', 'item_id', 'invoice_id'
    ];

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
