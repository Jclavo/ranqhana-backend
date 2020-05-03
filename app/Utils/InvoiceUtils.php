<?php

namespace App\Utils;

class InvoiceUtils
{
    static function getType($invoice){
        
        $invoiceResult = $invoice::
                    select('countries.timezone')
                    ->join('stores', 'stores.id', '=', 'users.store_id')
                    ->join('countries', 'countries.id', '=', 'stores.country_id')
                    ->where('users.id', '=', $user->id)
                    ->where('users.store_id', '=', $user->store_id)
                    ->first();
                    // ->pluck('countries.timezone');

        if($invoiceResult == null) return 'UTC';
        return $invoiceResult->type;
    }

}