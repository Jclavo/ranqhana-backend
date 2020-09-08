<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class InvoiceTypes extends BaseModel
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
}
