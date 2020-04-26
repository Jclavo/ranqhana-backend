<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceStage extends Model
{
    protected $fillable = [
        'code', 'description'
    ];
}
