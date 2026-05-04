<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class MoneyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        $numericValue = is_numeric($value) ? (float) $value : 0;
        return '$' . number_format($numericValue, 2);
    }
    

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return float
     */
    public function set($model, $key, $value, $attributes)
    {
        // Remove any non-numeric characters (except for the decimal point)
        $value = preg_replace('/[^\d.]/', '', $value);
        return (float) $value;
    }
}