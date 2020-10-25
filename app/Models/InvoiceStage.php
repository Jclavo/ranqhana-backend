<?php

namespace App\Models;;

use App\Models\BaseModel;

use Illuminate\Support\Facades\App;

class InvoiceStage extends BaseModel
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
    static function getForPaid()
    {
        return 1;
    }

    static function getForAnulled()
    {
        return 2;
    }

    static function getForDraft()
    {
        return 3;
    }

    static function getForByInstallment()
    {
        return 4;
    }

    static function getForStockUpdated()
    {
        return 5;
    }
}