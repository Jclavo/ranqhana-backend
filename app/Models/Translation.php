<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /**
     * Get the owning translationable model.
     */
    public function translationable()
    {
        return $this->morphTo();
    }
}
