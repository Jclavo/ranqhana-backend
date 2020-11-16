<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\App;

class ItemType extends BaseModel
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
     * Get all of the Invoice Stages' translations.
     */
    public function translations()
    {
         return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                     ->where('locale',App::getLocale());
    }

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
