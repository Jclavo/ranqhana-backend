<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class PaymentMethod extends BaseModel
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
     * Relationships
     */

    /**
     * Get all of the PaymentMethod translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }


    /**
     * Getter (statics)
     */

    static function getForMoney()
    {
        return 1;
    }

    static function getForCard()
    {
        return 2;
    }
}
