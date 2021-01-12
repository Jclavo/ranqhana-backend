<?php

namespace App\Utils;
use Carbon\Carbon;
//Utils
use App\Utils\CustomCarbon;

class MoreUtils
{
    static function generateInvoiceCorrelativeSerie($model, $type_id){

        $today = CustomCarbon::UTCtoCountryTZ(Carbon::now())->toDateString();
        $fromDate = CustomCarbon::countryTZtoUTC($today , '00:00:00');
        $toDate = CustomCarbon::countryTZtoUTC($today , '23:59:59');

        $order = $model::where('type_id',$type_id)
                        ->whereBetween('created_at',[ $fromDate, $toDate])
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

    static function generateEAN8Code($value){

        $valueLength = strlen($value);
        if($valueLength < 7 ){
            $value = str_pad($value, 7, '0', STR_PAD_LEFT);
        }else if ($valueLength > 7){
            $value = substr($value,-7);
        }

        $sumOdd = $value[1] + $value[3] + $value[5];
        $sumEven = 3 * ($value[0] + $value[2] + $value[4] + $value[6]);

        $checksum = $sumOdd + $sumEven;
        $checksumDigit = 10 - ($checksum % 10);
        if ($checksumDigit == 10){
            $checksumDigit = 0;
        } 

        $value = $value . $checksumDigit;
        return $value;

    }

}