<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class PaymentType extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'code', 'name'
    ];

     /**
     * Relationships
     */

    /**
     * Get all of the PaymentStages translations.
     */
    public function translations()
    {
         return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                     ->where('locale',App::getLocale());
    }


    /**
     * Getter (statics)
     */

    static function getForDebit()
    {
        return 1;
    }
    
    static function getForCredit()
    {
        return 2;
    }

    static function getForCash()
    {
        return 3;
    }

    static function getForInternalCredit()
    {
        return 4;
    }
}
