<?php

namespace App\Models;;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class InvoiceStages extends BaseModel
{

    /** 
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'code', 'description'
    ];

     /**
     * Relationships
     */

    /**
     * Get all of the Invoice Stages' translations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable')
                    ->where('locale',App::getLocale());
    }

    //Getter (statics)
    // STAGES
    static function getStagePaid()
    {
        return 1;
    }

    static function getStageAnulled()
    {
        return 2;
    }

    static function getStageDraft()
    {
        return 3;
    }

    static function getStageByInstallment()
    {
        return 4;
    }
}
