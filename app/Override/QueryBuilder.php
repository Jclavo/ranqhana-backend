<?php

namespace App\Override;

// use \Illuminate\Database\Query\Builder;

class QueryBuilder extends \Illuminate\Database\Eloquent\Builder {
    
    //@Override
    public function get($columns = ['*']) {    

        $results = parent::get($columns);

        //custom logic
        $results = $results->map(function ($model) {

            if (method_exists($model, 'translate')) {
                $model->translate($model);
            }
            
            return $model;
        });

        return $results;
    }
}