<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait LanguageTrait
{
    // public function generateSlug($string)
    // {
    //     return strtolower(preg_replace(
    //         ['/[^\w\s]+/', '/\s+/'],
    //         ['', '-'],
    //         $string
    //     ));
    // }

    public function translate($collections){

        $newQuery = array();
        // $translations = $query->load('translations');

        $collections->load('translations');

        foreach ($collections->translations as $translation) {
            $collections[$translation->key] = $translation->value;
        }
        unset($collections['translations']);

        // if (is_array($collections)) {
            
        //     foreach ($collection as $collection) {

        //         foreach ($collection->translations as $translation) {
        //             $collection[$translation->key] = $translation->value;
        //         }
        //         unset($collections['translations']);
                
        //     }

        // }else{

        //     foreach ($collections->translations as $translation) {
        //         $collections[$translation->key] = $translation->value;
        //     }
        //     unset($collections['translations']);

        // }

    }
}