<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'key', 'value', 'locale', 'translationable_id', 'translationable_type', 
    ];

  
    /**
     * Get the owning translationable model.
     */
    public function translationable()
    {
        return $this->morphTo();
    }
}
