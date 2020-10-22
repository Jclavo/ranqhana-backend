<?php

namespace App\Utils;
use Carbon\Carbon;
//Utils
use App\Utils\CustomCarbon;

class MoreUtils
{
    static function generateCorrelativeCode($model){

        $today = CustomCarbon::UTCtoCountryTZ(Carbon::now())->toDateString();
        $fromDate = $today . ' 00:00:00';
        $toDate = $today . ' 23:59:59';

        $order = $model::whereBetween('created_at',[ $fromDate, $toDate])
                        ->orderBy('id','DESC')
                        ->first();

        if(is_null($order)){
            $newCode = 'A0001';
        }else{
            $newCode = substr($order->code,8);

            $number = substr($newCode,1);
            $letter = substr($newCode,0,1);

            if ($number == '999') {
                $number = '001';
                $letter = ++$letter;
            }else{
                $number = $number + 1;
                $number = str_pad($number, 3, '0', STR_PAD_LEFT);;
            }
            
            $newCode = $letter . $number;
        }

       
        $today = str_replace("-","",$today);

        $newCode = $today . $newCode;

        return $newCode;

    }
}