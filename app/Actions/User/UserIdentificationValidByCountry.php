<?php

namespace App\Actions\User;

use App\Models\User;

class UserIdentificationValidByCountry
{
    protected $store;
    protected $identification;

    protected $digitsCountry = 0;

    public function __construct($store, $identification)
    {
        $this->store = $store;
        $this->identification = $identification;
    }

    public function passes()
    {
        //Get country code from store
        $storeQueryResult = $this->store::
                            select('countries.code')
                            ->join('countries', 'countries.id', '=', 'stores.country_id')
                            ->where('stores.id', '=', $this->store->id)
                            ->first();
        
        $countryCode = $storeQueryResult->code;
        
        switch ($countryCode) {
            case "55":
                if(strlen($this->identification) != 11){
                    $this->digitsCountry = 11;
                    return false;
                }
                break;
            case "51":
                if(strlen($this->identification) != 8){
                    $this->digitsCountry = 8;
                    return false;
                }
                break;
            default:
                return false;
        }

        return true;
        
    }

    public function message()
    {
        return 'The identification must be ' . $this->digitsCountry . ' characters for your store country.';
    }

}