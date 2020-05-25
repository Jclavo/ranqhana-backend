<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends BaseModel
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'country_id'
    ];
}
