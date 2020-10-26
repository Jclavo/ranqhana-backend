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

    static function getForProduct()
    {
        return 1;
    }

    static function getForService()
    {
        return 2;
    }
}
