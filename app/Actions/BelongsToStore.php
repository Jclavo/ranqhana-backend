<?php

namespace App\Actions;

class BelongsToStore
{
    protected $user;
    protected $models;
    protected $className;

    public function __construct($user, $models = [])
    {
        $this->user = $user;
        $this->models = $models;
    }    
    
    public function passes()
    {
        foreach ($this->models as $model) {
            if ($this->user->store_id != $model->store_id ){
                $this->baseClass = class_basename($model);
                return false;
            } 
        } 
        
        return true;
    }    
    
    public function message()
    {
        return $this->baseClass . ' does not belong to current store.';
    }
}