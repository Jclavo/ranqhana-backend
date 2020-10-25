<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class InvoiceType extends BaseModel
{
    protected $fillable = [
        'code', 'description'
    ];

    /**
     * Relationships
     */

    /**
     * Get all of the Invoice Types' translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }

        //Getter (statics)

    //TYPES

    static function getForSell(){
        return 1;
    }

    static function getForPurchase(){
        return 2;
    }
}