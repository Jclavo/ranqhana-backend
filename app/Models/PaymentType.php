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
        'name'
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

    static function getTypeDebit()
    {
        return 1;
    }
    
    static function getTypeCredit()
    {
        return 2;
    }
}