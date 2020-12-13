<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\PaymentType;

use Illuminate\Support\Facades\App;

class PaymentMethod extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'code', 'name'
    ];

    /**
     * Relationships
     */

    /**
     * Get all of the PaymentMethod translations.
     */
    public function translations()
    {
         return $this->morphMany(Translation::class, 'translationable', null,null,'code')
                     ->where('locale',App::getLocale());
    }


    /**
     * Getter (statics)
     */

    static function getForMoney()
    {
        return 1;
    }

    static function getForCard()
    {
        return 2;
    }

    //function

    static function getForPaymentType($payment_type_id, $method_id = null){

        switch ($payment_type_id) {
            case PaymentType::getForCash():
                $method_id = self::getForMoney();
                break;
            case PaymentType::getForCredit():
            case PaymentType::getForDebit():
                $method_id = self::getForCard();
                break;
            case PaymentType::getForInternalCredit():
                $method_id = $method_id ? $method_id : self::getForMoney();        
                break;
            default:
                $method_id = self::getForMoney();
                break;
        }

        return $method_id;

    }
    
}
