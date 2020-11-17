<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\LanguageTrait;
use App\Override\QueryBuilder;

use App\Models\Translation;

class ItemStockType extends Pivot
{
    use LanguageTrait;

    /**
    * Relationships
    */

    /**
     * Get all of the Invoice Types' translations.
     */
    public function translations()
    {
        // return $this->morphMany(Translation::class, 'translationable', null,null,'code')
        //              ->where('locale',App::getLocale());
        return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                    ->where('locale',App::getLocale());
    }



    /**
    * Overwrite method from Builder
    */

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */

    public function newEloquentBuilder($query) 
    { 
        return new QueryBuilder($query); 
    }
}