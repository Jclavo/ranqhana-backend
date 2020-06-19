<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTypes extends Model
{
    protected $fillable = [
        'code', 'description'
    ];
}
