<?php

namespace App;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->relations)) {
            return parent::getAttribute($key);
        } else {
            return parent::getAttribute(Str::snake($key));
        }
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute(Str::snake($key), $value);
    }

}