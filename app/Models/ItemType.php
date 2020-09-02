<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name'
    ];


    /**
     * Getter (statics)
     */

    static function getTypeProduct()
    {
        return 1;
    }

    static function getTypeService()
    {
        return 2;
    }
}
