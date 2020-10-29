<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Translation;

use Illuminate\Support\Facades\App;

class Unit extends BaseModel
{
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'abbreviation', 'name', 'fractioned'
    ];

    
    /**
     * Relationships
     */

    /**
     * Get all of the unit's translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable', null,null,'code')
        ->where('locale',App::getLocale());
    }

}
