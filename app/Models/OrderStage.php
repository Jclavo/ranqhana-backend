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
        'name'
    ];

    /**
     * Relationships
     */

    /**
     * Get all of the OrderStage translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }

    /**
     * Getter (statics)
     */

    static function getStageNew()
    {
        return 1;
    }
}
