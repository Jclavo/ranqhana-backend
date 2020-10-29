<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class OrderStage extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'code','name',
    ];

    /**
     * Relationships
     */

    /**
     * Get all of the OrderStage translations.
     */
    public function translations()
    {
         return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                     ->where('locale',App::getLocale());
    }

    /**
     * Getter (statics)
     */

    static function getForNew()
    {
        return 1;
    }

    static function getForRequested()
    {
        return 2;
    }

    static function getForAccepted()
    {
        return 3;
    }

    static function getForPreparing()
    {
        return 4;
    }

    static function getForWrapped()
    {
        return 5;
    }

    static function getForReady()
    {
        return 6;
    }

    static function getForShipped()
    {
        return 7;
    }

    static function getForDelivered()
    {
        return 8;
    }

    static function getForCancel()
    {
        return 9;
    }

    static function getForAutomatic()
    {
        return 10;
    }
}
