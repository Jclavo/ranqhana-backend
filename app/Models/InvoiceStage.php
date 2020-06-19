<?php

namespace App\Models;;

use Illuminate\Database\Eloquent\Model;

class InvoiceStage extends Model
{
    protected $fillable = [
        'code', 'description'
    ];
}
