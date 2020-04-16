<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
// use App\Unit;
use App\BaseModel;

class Unit extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'description', 'allow_decimal'
    ];
}
