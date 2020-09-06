<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Translation;

use App\Traits\LanguageTrait;
use Illuminate\Support\Facades\App;

class Unit extends BaseModel
{
    use LanguageTrait;
    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'description', 'fractioned'
    ];

    
    /**
     * Relationships
     */

    /**
     * Get all of the unit's translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }

}
