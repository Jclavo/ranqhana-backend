<?php

namespace App\Actions\User;

use App\User;

class UserBelongsToCountry
{
    protected $identification;
    protected $countryCode;

    public function __construct($identification, $countryCode)
    {
        $this->identification = $identification;
        $this->countryCode = $countryCode;
    }    
    
    public function passes()
    {
        $count = User::
                join('stores', 'stores.id', '=', 'users.store_id')
                ->join('countries', 'countries.id', '=', 'stores.country_id')
                ->where('users.identification', '=', $this->identification)
                ->Where('countries.code', '=', $this->countryCode)
                ->count();

        if($count == 0) return false;
        
        return true;
    }    
    
    public function message()
    {
        return 'There current user does not belong to the selected country.';
    }
}