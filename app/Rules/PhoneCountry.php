<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Store;

//Services
use App\Services\LanguageService;

class PhoneCountry implements Rule
{
    protected $store_id;
    protected $languageService;

    public function __construct($store_id = 0)
    {
        $this->store_id = $store_id;

        //initialize language service
	    $this->languageService = new LanguageService();
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
        //Get country code from store
        $storeQueryResult = Store::
                            select('countries.code')
                            ->join('countries', 'countries.id', '=', 'stores.country_id')
                            ->where('stores.id', '=', $this->store_id)
                            ->first();

        if($storeQueryResult == null) return false;

        $countryCode = $storeQueryResult->code;
        
        switch ($countryCode) {
            case "55":
                if(strlen($value) != 11){
                    return false;
                }
                break;
            case "51":
                if(strlen($value) != 9){
                    return false;
                }
                break;
            default:
                return false;
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
        return $this->languageService->getSystemMessage('phone.format');
    }
}
