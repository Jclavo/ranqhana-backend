<?php

namespace App;

use App\BaseModel;

class InvoiceDetail extends BaseModel
{
    protected $fillable = [
        'item_id', 'quantity', 'price', 'total', 'invoice_id'
    ];

    //Custom functions

    // public function getQuantityAttribute(){
    //     return $this->attributes['quantity'];
    // }

    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->price;
    }


}
