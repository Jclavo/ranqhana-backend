<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class PaymentStage extends BaseModel
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
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }


    /**
     * Getter (statics)
     */

    static function getForWaiting()
    {
        return 1;
    }

    static function getForDelayed()
    {
        return 2;
    }

    static function getForPaid()
    {
        return 3;
    }

    static function getForAnnulled()
    {
        return 4;
    }

}
