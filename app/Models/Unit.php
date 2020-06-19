<?php

namespace App\Models;

use App\Models\BaseModel;

class Unit extends BaseModel
{
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'description', 'fractioned'
    ];
}
