<?php

namespace App;
use Illuminate\Support\Str;
// use Illuminate\Support\Collection as BaseCollection;  
use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {

    /**
     * Overwrite the function setAttribute
     * - to cast: if the set value is null, the default value will 
     *            be set in the db field
     */

    public function setAttribute($key, $value)
    {
        if (! is_null($value)) {
            return parent::setAttribute($key, $value);
        }

        return null;
        //the next line are to get the database field type but I never tested it
        //$this->table();
        // DB::getSchemaBuilder()->getColumnType($tableName, $colName)
    }



    // public function getAttribute($key)
    // {
    //     if (array_key_exists($key, $this->relations)) {
    //         return parent::getAttribute($key);
    //     } else {
    //         return parent::getAttribute(Str::snake($key));
    //     }
    // }

    // public function setAttribute($key, $value)
    // {
    //     return parent::setAttribute(Str::snake($key), $value);
    // }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    // protected function castAttribute($key, $value)
    // {   
    //     return parent::castAttribute($key, -1);
    //     if (! is_null($value)) {
    //         return parent::castAttribute($key, $value);
    //         //return $this->traitcastAttribute($key, $value);
    //     }

    //     switch ($this->getCastType($key)) {
    //         case 'int':
    //         case 'integer':
    //             return (int) 0;
    //         case 'real':
    //         case 'float':
    //         case 'double':
    //             return (float) 0;
    //         case 'string':
    //             return ''; 
    //         case 'bool':
    //         case 'boolean':
    //             return false;
    //         case 'object':
    //         case 'array':
    //         case 'json':
    //             return [];
    //         case 'collection':
    //             return new BaseCollection();
    //         case 'date':
    //             return $this->asDate('0000-00-00');
    //         case 'datetime':
    //             return $this->asDateTime('0000-00-00');
    //         case 'timestamp':
    //             return $this->asTimestamp('0000-00-00');
    //         default:
    //             return $value;
    //     }
    // }

}