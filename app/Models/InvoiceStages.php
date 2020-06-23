<?php

namespace App\Models;;

use Illuminate\Database\Eloquent\Model;

class InvoiceStages extends Model
{
    protected $fillable = [
        'code', 'description'
    ];
}
