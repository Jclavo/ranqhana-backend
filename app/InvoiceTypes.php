<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceTypes extends Model
{
    protected $fillable = [
        'code', 'description'
    ];
}
