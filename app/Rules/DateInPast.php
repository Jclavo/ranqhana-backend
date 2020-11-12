<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

//Utils
use App\Utils\CustomCarbon;

class DateInPast implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!empty($value)){
            
            $now = CustomCarbon::UTCtoCountryTZ(Carbon::now())->startOfDay();
            $dateTimeObject = Carbon::parse($value)->startOfDay();
            $date_diff = $now->diffInDays($dateTimeObject, false);

            if($date_diff < 0){
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The payment date can not be in the past.';
    }
}
