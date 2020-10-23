<?php

namespace App\Utils;
use Carbon\Carbon;
//Utils
use App\Utils\CustomCarbon;

class MoreUtils
{
    static function generateCorrelativeSerie($model){

        $today = CustomCarbon::UTCtoCountryTZ(Carbon::now())->toDateString();
        $fromDate = CustomCarbon::countryTZtoUTC($today , '00:00:00');
        $toDate = CustomCarbon::countryTZtoUTC($today , '23:59:59');

        $order = $model::whereBetween('created_at',[ $fromDate, $toDate])
                        ->orderBy('id','DESC')
                        ->first();

        if(is_null($order)){
            $newSerie = 'A001';
        }else{
            $newSerie = substr($order->serie,8);

            $number = substr($newSerie,1);
            $letter = substr($newSerie,0,1);

            if ($number == '999') {
                $number = '001';
                $letter = ++$letter;
            }else{
                $number = $number + 1;
                $number = str_pad($number, 3, '0', STR_PAD_LEFT);;
            }
            
            $newSerie = $letter . $number;
        }

       
        $today = str_replace("-","",$today);

        $newSerie = $today . $newSerie;

        return $newSerie;

    }
}