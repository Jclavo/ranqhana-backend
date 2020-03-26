<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $fillable = [
        'item_id', 'quantity', 'price', 'total', 'invoice_id'
    ];

}
