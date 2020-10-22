<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CorrelativeCode implements CastsInboundAttributes
{
      /**
     * Create a new cast class instance.
     *
     * @param  string|null  $algorithm
     * @return void
     */
    public function __construct()
    {
    }

        /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        // return is_null($this->algorithm)
        //             ? bcrypt($value)
        //             : hash($this->algorithm, $value);
        if (true) {
            # code...
        }
        return 'ABC';
    }
}