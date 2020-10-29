<?php

namespace App\Utils;

use App\Models\Translation;

class TranslationUtils
{

    static function updateOrCreateTranslation($id, $value, $locale, $type, $key = null ){

        is_null($key) ? $key = 'name' : null;

        $newTranslation = Translation::updateOrCreate(
            ['key' => $key, 'locale' => $locale, 'translationable_id' => $id,'translationable_type' => $type],
            [ 'value' => $value]);

    }

    static function customUpdateOrCreate($values, $model){

        $KEY = 'name';

        foreach ($values as $value) {

            $newType = $model::updateOrCreate(['code' => $value->code],['name' => $value->name]);

            foreach ($value->translations as $translation) {

                Translation::updateOrCreate(
                    ['key' => $KEY, 'locale' => $translation->locale, 'translationable_id' => $newType->code, 'translationable_type' => $model],
                    [ 'value' => $translation->value]);
            }
        }
        
    }
    
}