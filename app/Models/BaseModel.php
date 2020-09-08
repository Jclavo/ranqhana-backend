<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Collection as BaseCollection;  
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

use App\Traits\LanguageTrait;
use App\Override\QueryBuilder;

class BaseModel extends Eloquent {

    use LanguageTrait;

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

        //the next line are to get the database field type
        $type = DB::getSchemaBuilder()->getColumnType($this->getTable(), $key);


        switch ($type) {
            case 'int':
            case 'integer':
                $value = (int) 0;
                break;
            case 'real':
            case 'decimal':
            case 'float':
            case 'double':
                $value = (float) 0;
                break;
            case 'string':
                $value = ''; 
                break;
            case 'bool':
            case 'boolean':
                $value = false;
                break;
            case 'object':
            case 'array':
            case 'json':
                $value = [];
                break;
            case 'collection':
                $value = new BaseCollection();
                break;
            case 'date':
                $value = $this->asDate('0000-00-00');
                break;
            case 'datetime':
                $value = $this->asDateTime('0000-00-00');
                break;
            case 'timestamp':
                $value = $this->asTimestamp('0000-00-00');
                break;
            // default:
            //     $value = null;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate($date)
    {
        $carbonInstance = \Carbon\Carbon::instance($date);

        return $carbonInstance->toISOString();
    }

    /**
     * Overwrite method from Builder
     */

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */

    public function newEloquentBuilder($query) 
    { 
        return new QueryBuilder($query); 
    }

}