<?php

namespace App\Utils;

class UserUtils
{
    // static function getTimezone($user){

    //     $timezone = $user::
    //                 select('countries.timezone')
    //                 ->join('stores', 'stores.id', '=', 'users.store_id')
    //                 ->join('countries', 'countries.id', '=', 'stores.country_id')
    //                 ->where('users.id', '=', $user->id)
    //                 ->where('users.store_id', '=', $user->store_id)
    //                 ->first();
    //                 // ->pluck('countries.timezone');

    //     if($timezone == null) return 'UTC';
    //     return $timezone->timezone;
    // }

    static function generateLogin($identification,$store_id){
        return $identification . $store_id;
    }
    
}