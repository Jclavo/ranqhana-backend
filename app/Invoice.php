<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\BaseModel;

class Invoice extends BaseModel 
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serie', 'subtotal', 'taxes', 'discount', 'total', 'type_id', 'user_id'
    ];

    /**
     * The attributes that are cast.
     */
    protected $casts = [
        // 'subtotal' => 'decimal:2',
        //  'discount' => 'decimal:2',
    ];
 
}
